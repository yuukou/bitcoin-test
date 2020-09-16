<?php

namespace App;

require __DIR__.'/../vendor/autoload.php';

use BitcoinPHP\BitcoinECDSA\BitcoinECDSA;

class Wallet
{
    use Functions;

    private string $privateKey;
    private string $publicKey;
    private string $address;
    private BitcoinECDSA $bitcoinECDSA;

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
        $this->address = $this->generateBlockChainAddress();
    }

    /**
     * ブロックチェーンアドレスを生成
     *
     * @return String
     */
    private function generateBlockChainAddress()
    {
        return $this->bitcoinECDSA->getAddress();
    }

    /**
     * アドレスの取得
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * 公開鍵の取得
     *
     * @return array|string
     */
    public function getPublicKey()
    {
        return $this->publicKey;
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
