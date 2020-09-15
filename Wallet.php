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
     * ブロックチェーンアドレスを作成
     *
     * @return String
     */
    public function generateBlockChainAddress()
    {
        return $this->bitcoinECDSA->getAddress();
    }
}

$wallet = new Wallet();
var_dump($wallet->generateBlockChainAddress());
