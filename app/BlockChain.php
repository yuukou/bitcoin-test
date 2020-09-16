<?php

namespace App;

require __DIR__.'/../vendor/autoload.php';

use BitcoinPHP\BitcoinECDSA\BitcoinECDSA;

class BlockChain
{
    use Functions;

    const MINING_DIFFICULTY = 1;
    const MIN_LENGTH = 1;
    const MINING_REWARD = 0.25;
    const MINING_SYSTEM_SENDER = 'system';
    private array $transactionPool = [];
    private array $chain = [];
    private string $minerAddress;

    //Todo: minerAddressは配列でいくつも登録できるようにするべき
    public function __construct(string $minerAddress)
    {
        $this->minerAddress = $minerAddress;
    }

    /**
     * トランザクションプールに新規のトランザクションを追加する
     *
     * @param string $sender
     * @param string $recipient
     * @param float $value
     * @param string $signature
     * @param string $senderPublicKey
     * @return void
     */
    public function addTransaction(string $sender, string $recipient, float $value, string $senderPublicKey = '', string $signature = '')
    {
        $transaction = $this->generateTransaction($sender, $recipient, $value, $senderPublicKey, $signature);

        // マイニング報酬の場合は署名確認なしで、トランザクションを追加
        if ($sender == self::MINING_SYSTEM_SENDER) {
            $this->transactionPool[] = $transaction;
        }

        // マイニング報酬以外は署名確認処理を通す
        if ($this->verifySignature($senderPublicKey, $signature, $this->getExchangeData($sender, $recipient, $value))) {
            $this->transactionPool[] = $transaction;
        }
    }

    /**
     * 送信元・送信先・取引量の配列を返す
     *
     * @param string $sender
     * @param string $recipient
     * @param float $value
     * @return array
     */
    public function getExchangeData(string $sender, string $recipient, float $value)
    {
        return compact('sender', 'recipient', 'value');
    }

    /**
     * トランザクションを生成
     *
     * @param string $sender
     * @param string $recipient
     * @param float $value
     * @param string $signature
     * @param string $senderPublicKey
     * @return array
     */
    public function generateTransaction(string $sender, string $recipient, float $value, string $senderPublicKey, string $signature)
    {
        return compact('sender', 'recipient', 'value', 'senderPublicKey', 'signature');
    }

    /**
     * 合計値を取得する
     *
     * @param string $address
     * @return int
     */
    public function calcTotalAmount(string $address)
    {
        $total = 0;
        foreach ($this->chain as $block) {
            foreach ($block['transactions'] as $transaction) {
                if ($address === $transaction['sender']) {
                    $total -= $transaction['value'];
                } elseif ($address === $transaction['recipient']) {
                    $total += $transaction['value'];
                }
            }
        }
        return $total;
    }

    /**
     * ブロック生成とチェーンへの追加
     *
     * @return void
     */
    public function mining()
    {
        $transactions = $this->transactionPool;
        $timeStamp = time();
        if (!empty($this->chain)) {
            $previousHash = $this->getHash(end($this->chain));
        } else {
            $previousHash = $this->getHash([]);
        }
        $nonce = $this->proofOfWork($this->transactionPool, $previousHash, time());
        $this->chain[] = compact('transactions', 'timeStamp', 'previousHash', 'nonce');
        // マイニングに成功したユーザーに一定量の報酬を提供する
        $this->addTransaction(self::MINING_SYSTEM_SENDER, $this->minerAddress, self::MINING_REWARD);
    }

    /**
     * プルーフオブワーク
     *
     * @param array $transactions
     * @param string $previousHash
     * @param $timestamp
     * @return int
     */
    private function proofOfWork(array $transactions, string $previousHash, $timestamp)
    {
        $nonce = 0;
        while(!$this->isValidProof($transactions, $previousHash, $timestamp, $nonce)) {
            $nonce++;
        }
        return $nonce;
    }

    /**
     * プルーフが有効かどうか
     *
     * @param array $transactions
     * @param string $previousHash
     * @param $timeStamp
     * @param int $nonce
     * @return bool
     */
    private function isValidProof(array $transactions, string $previousHash, $timeStamp, int $nonce)
    {
        $length = (self::MINING_DIFFICULTY === 1) ? self::MIN_LENGTH : self::MINING_DIFFICULTY -1;
        return substr($this->getHash(compact('transactions', 'timeStamp', 'previousHash', 'nonce')), 0 ,$length)
            === str_repeat("0", self::MINING_DIFFICULTY);
    }

    /**
     * 署名が正しいかどうか
     *
     * @param string $publicKey
     * @param string $signature
     * @param array $transaction
     * @return bool
     */
    public function verifySignature(string $publicKey, string $signature, array $transaction)
    {
        $bitcoinECDSA = new BitcoinECDSA();
        return $bitcoinECDSA->checkDerSignature($publicKey, $signature, $this->getHash($transaction));
    }

    /**
     * トランザクションプールの値を取得
     *
     * @return array
     */
    public function getTransactionPool()
    {
        return $this->transactionPool;
    }
}

// Aのウォレット
$wallet_A = new Wallet();
// Bのウォレット
$wallet_B = new Wallet();
// マイナーのウォレット
$wallet_M = new Wallet();

$blockChain = new BlockChain($wallet_M->getAddress());
// A -> B に 1.0 送信するトランザクションの署名を取得
$signature = $wallet_A->generateSignature($blockChain->getExchangeData($wallet_A->getAddress(), $wallet_B->getAddress(), 1.0));
// A -> B に 1.0 送信するトランザクションをブロックチェーン上のトランザクションプールに追加する
$blockChain->addTransaction($wallet_A->getAddress(), $wallet_B->getAddress(), 1.0, $wallet_A->getPublicKey(), $signature);
var_dump($blockChain->getTransactionPool());
// マイニング
$blockChain->mining();
var_dump($blockChain->getTransactionPool());
