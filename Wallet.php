<?php

require_once 'vendor/autoload.php';

use BitcoinPHP\BitcoinECDSA\BitcoinECDSA;

class Wallet
{
    private string $privateKey;
    private string $publicKey;
    private BitcoinECDSA $bitcoinECDSA;

    /**
     * Wallet constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->bitcoinECDSA = new BitcoinECDSA();
        $this->bitcoinECDSA->generateRandomPrivateKey();
        $this->privateKey = $this->bitcoinECDSA->getPrivateKey();
        $this->publicKey = $this->bitcoinECDSA->getPubKey();
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
     * @return string
     * @throws Exception
     */
    public function generateSignature()
    {
        return $this->bitcoinECDSA->signMessage('aaa');
    }
}

$wallet = new Wallet();
var_dump($wallet->generateSignature());
