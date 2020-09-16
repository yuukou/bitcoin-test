<?php

namespace App;

require __DIR__.'/../vendor/autoload.php';

use BitcoinPHP\BitcoinECDSA\BitcoinECDSA;

class Wallet
{
    private string $privateKey;
    private string $publicKey;
    private BitcoinECDSA $bitcoinECDSA;
    private BlockChain $blockChain;

    /**
     * Wallet constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->bitcoinECDSA = new BitcoinECDSA();
        $this->bitcoinECDSA->generateRandomPrivateKey();
        $this->privateKey = $this->bitcoinECDSA->getPrivateKey();
        $this->publicKey = $this->bitcoinECDSA->getPubKey();
        $this->blockChain = new BlockChain();
    }

    /**
     * ブロックチェーンアドレスを生成
     *
     * @return String
     */
    public function generateBlockChainAddress()
    {
        return $this->bitcoinECDSA->getAddress();
    }

    /**
     * トランザクションの署名
     *
     * @param array $transaction
     * @return string
     */
    public function generateSignature(array $transaction)
    {
        // transactionをハッシュ化したものを作成して、signHashに渡す
        return $this->bitcoinECDSA->signHash($this->blockChain->getHash($transaction));
    }

    /**
     * 署名が正しいかどうか
     *
     * @param string $signature
     * @param array $transaction
     * @return bool
     */
    public function verifySignature(string $signature, array $transaction)
    {
        return $this->bitcoinECDSA->checkDerSignature($this->publicKey, $signature, $this->blockChain->getHash($transaction));
    }
}

$wallet = new Wallet();
$blockChain = new BlockChain();
$signature = $wallet->generateSignature($blockChain->generateTransaction('a', 'b', 1.0));
var_dump($signature);
var_dump($wallet->verifySignature($signature, $blockChain->generateTransaction('a', 'b', 2.0)));
