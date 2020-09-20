<?php

namespace App;

/**
 * ブロックチェーンサーバー
 * @package App
 */
class BlockChainServer
{
    /** @var array キャッシュ用プロパティ */
    private static $cache = [];

    /**
     * ブロックチェーンノード側のサーバー立ち上げ
     *
     * @return void
     */
    public function serve()
    {
//        $cmds = ['php -S 127.0.0.1:8027', 'nohup php -S 127.0.0.1:8028', 'nohup php -S 127.0.0.1:8029'];
//        foreach ($cmds as $cmd) {
//            exec("$cmd > /dev/null &");
//        }

        $cmd = 'php -S 127.0.0.1:8027';
        exec($cmd);
    }

    /**
     * ブロックチェーンの取得
     *
     * @return array
     */
    private function getBlockChain()
    {
        $cachedBlockChain = self::$cache;
        if (empty($cachedBlockChain)) {
            $minerWalletAddress = (new Wallet())->getAddress();
            self::$cache['blockchain'] = new BlockChain($minerWalletAddress);
        }

        return self::$cache['blockchain'];
    }

    /**
     * ブロックチェーン情報を返す
     *
     * @return false|string
     */
    public function getChain()
    {
        $response = [
            'chain' => $this->getBlockChain()
        ];

        header("Access-Control-Allow-Origin: *");
        header('Content-Type: application/json');
        return json_encode($response);
    }
}

$blockChainServer = new BlockChainServer();
$blockChainServer->serve();
