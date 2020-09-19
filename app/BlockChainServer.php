<?php

namespace App;

class BlockChainServer
{
    public function server()
    {
//        $cmds = ['php -S 127.0.0.1:8027', 'nohup php -S 127.0.0.1:8028', 'nohup php -S 127.0.0.1:8029'];
//        foreach ($cmds as $cmd) {
//            exec("$cmd > /dev/null &");
//        }

        $cmd = 'php -S 127.0.0.1:8027';
        exec($cmd);
    }
}

$blockChainServer = new BlockChainServer();
$blockChainServer->server();
