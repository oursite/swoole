<?php
/**
 * Created by PhpStorm.
 * User: DTO
 * Date: 2015/4/9
 * Time: 16:50
 */

namespace Lib;


class TaskWorker {
    private static $serv = null;

    public static function setServ($server = null) {
        if (!self::$serv) {
            self::$serv = $server;
        }
    }
    public static function send(){
        $server = self::$serv;
        if (!$server) {
            return false;
        }
        $argv = func_get_args();
        if (count($argv) < 1) {
            return false;
        }
        $action = $argv[0];
        unset($argv[0]);
        $jsonParams = json_encode(array('action' => $action, 'params' => $argv));
        return $server->task($jsonParams);

    }
}