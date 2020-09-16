<?php

namespace App;

use BitcoinPHP\BitcoinECDSA\BitcoinECDSA;

class Transaction
{
    use Functions;

    private BitcoinECDSA $bitcoinECDSA;

    /**
     * Transaction constructor.
     *
     *  signHashメソッドがBitcoinECDSAインスタンスの内部で共有されているkeyを使用して、ハッシュ化を行うため、
     *  送信元のBitcoinECDSAインスタンスをトランザクションクラスとも共有する必要がある
     *
     * @param BitcoinECDSA $senderBitcoinECDSA
     */
    public function __construct(BitcoinECDSA $senderBitcoinECDSA)
    {
        $this->bitcoinECDSA = $senderBitcoinECDSA;
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
        return $this->bitcoinECDSA->signHash($this->getHash($transaction));
    }
}
