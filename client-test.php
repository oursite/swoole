<?php

        header("Content-type: text/html; charset=utf-8");
        $client = new swoole_client(SWOOLE_SOCK_TCP);
        
        if (!$client->connect('127.0.0.1', 9501, 2)) {
            exit("connect failed. Error: {$client->errCode}\n");
        }
        $msg_normal = json_encode(array('action' => 'Chefubao.AutoPay.test'));
//        $msg_normal = json_encode(array('action' => 'Acl.Access.test', 'params' => array("'")));
        $msg = pack("N" , strlen($msg_normal) ). $msg_normal;
        $client->send($msg);


        $data = $client->recv(4,1);
        $len = unpack("N" , $data)[1];

        var_dump($len);
        $recvLen = 0;
        $recvData = '';
        while($recvLen < $len) {
            // var_dump($len,$recvLen);
            $back = $client->recv();
            var_dump(strlen($back));
            $recvData .= $back;
            $recvLen += strlen($back);
        }
//        file_put_contents('test-data.txt', $recvData);

        var_dump($recvData);
        // echo "\n\n\n" . $data . "\n\n";
        // $client->close();





        //         $client = new swoole_client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
        
        // $client->on("connect", function($cli) {
        //     $msg_normal = json_encode(array('action' => 'Common.City.getCityList', 'params' => array()));
        //     $msg = pack("N" , strlen($msg_normal) ). $msg_normal;
        //     $cli->send($msg);

        // });
        // $client->on('receive', function($cli, $data = "") {
        //     file_put_contents('test-data.txt', $data);
        //     var_dump(strlen($data));
        // });
        // $client->on("close", function($cli){
        //     echo "close\n";
        // });

        // $client->on("error", function($cli){
        //     exit("error\n");
        // });
        // $client->connect('127.0.0.1', 9501, 0.5);