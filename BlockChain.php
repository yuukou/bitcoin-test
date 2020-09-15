<?php

class BlockChain
{
    const MINING_DIFFICULTY = 1;
    const MIN_LENGTH = 1;
    const MINING_REWARD = 0.25;
    private array $transactionPool = [];
    private array $chain = [];

    /**
     * トランザクションプールに新規のトランザクションを追加する
     *
     * @param string $sender
     * @param string $recipient
     * @param float $value
     * @return void
     */
    public function addTransaction(string $sender, string $recipient, float $value)
    {
        $this->transactionPool[] = compact('sender', 'recipient', 'value');
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
    public function createBlock()
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
        // Todo: recipientが今は固定だが、動的に取得できるようにする
        $this->addTransaction('system', 'a', self::MINING_REWARD);
    }

    /**
     * 任意値をハッシュ化した値を取得
     *
     * @param array $block
     * @return string
     */
    private function getHash(array $block)
    {
        // phpのhash関数は文字列にかけるものなので、一旦serializeで配列を文字列に変換してからhash関数をかける
        return hash('sha256', serialize($block));
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
}

$blockChain = new BlockChain();
$blockChain->addTransaction('a', 'b', 1.0);
$blockChain->createBlock();
