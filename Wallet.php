<?php

require_once 'vendor/autoload.php';

use BitcoinPHP\BitcoinECDSA\BitcoinECDSA;

class Wallet
{
    private string $privateKey;
    private string $publicKey;

    public function __construct()
    {
        $bitcoinECDSA = new BitcoinECDSA();
        $bitcoinECDSA->generateRandomPrivateKey();
        $this->privateKey = $bitcoinECDSA->getPrivateKey();
        $this->publicKey = $bitcoinECDSA->getPubKey();
    }
}

//$wallet = new Wallet();
//var_dump($wallet);
