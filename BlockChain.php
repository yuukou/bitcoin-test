<?php

class BlockChain
{
    const MINING_DIFFICULTY = 3;
    private array $transactionPool = [];
    private array $chain = [];

    public function __construct(array $transaction)
    {
        $this->transactionPool[] = $transaction;
    }

    public function getChain()
    {
        return $this->chain;
    }

    public function createBlock()
    {
        $transactions = $this->transactionPool;
        $timeStamp = time();
        if ($this->chain) {
            $previousHash = $this->getHash(end($chain));
        } else {
            $previousHash = $this->getHash([]);
        }
        $nonce = $this->getNonce($this->transactionPool, $previousHash, time());
        $this->chain[] = compact('transactions', 'timeStamp', 'previousHash', 'nonce');
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
     * マイニングに成功するナンス値を取得
     *
     * @param array $transactions
     * @param string $previousHash
     * @param $timestamp
     * @return int
     */
    private function getNonce(array $transactions, string $previousHash, $timestamp)
    {
        $nonce = 0;
        while(!($this->isSuccessMining($transactions, $previousHash, $timestamp, $nonce))) {
            $nonce++;
        }
        return $nonce;
    }

    /**
     * マイニングが成功するハッシュ値の取得に成功したかどうか
     *
     * @param array $transactions
     * @param string $previousHash
     * @param $timestamp
     * @param int $nonce
     * @return bool
     */
    private function isSuccessMining(array $transactions, string $previousHash, $timestamp, int $nonce)
    {
        return substr($this->getHash(compact('transactions', 'timeStamp', 'previousHash', 'nonce')), 0 ,(self::MINING_DIFFICULTY -1))
            === str_repeat("0", self::MINING_DIFFICULTY);
    }
}
