<?php
/*
 *  Swoole 服务端主程序
 * 
 */
namespace Lib;
use V4\Core\Config;

class Server {
    //swoole_server 实例
    private $server = null;
    private $_package_max_length = 2097152;
    private $env = null;
    // private $listens = null;
    /*
     *  构造函数，设置swoole配置、回调函数、启动服务
     * 
     */
    public function __construct($env = 'local') {

        $this->env = $env;

        $this->server = new \swoole_server("0.0.0.0", 9501, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
        $this->server->set(array(
            'worker_num'            => 30,
            'daemonize'             => false,
            'max_request'           => 800,
            'dispatch_mode'         => 2,
            'package_max_length'    => $this->_package_max_length,
            'open_length_check'     => true,
            'package_length_offset' => 0,
            'package_body_offset'   => 4,
            'package_length_type'   => 'N',
            'debug_mode'            => 1,
            'task_worker_num'       => 30,
            'log_file'              => '/home/wwwroot/log/swoole9501.log'
        ));

        $this->server->on('Start', array($this, 'onStart'));
        $this->server->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->server->on('Connect', array($this, 'onConnect'));
        $this->server->on('Receive', array($this, 'onReceive'));
        $this->server->on('Close', array($this, 'onClose'));
        $this->server->on('Task', array($this, 'onTask'));
        $this->server->on('Finish', array($this, 'onFinish'));
        $this->server->start();
    }

    /*
     *  服务端启动时回调
     */
    public function onStart($serv) {
        echo "Start\n";
    }
    public function onWorkerStart() {
        echo "onWorkerStart\n";
        // $this->getListenList();
    }
    /*
     *  有新的client端连接时回调
     */
    public function onConnect($serv, $fd, $from_id) {
//        var_dump($this->env, 'env');
        echo "$fd onConnect\n";
    }

    /*
     *  接收到客户端消息时回调
     */
    public function onReceive(\swoole_server $serv, $fd, $from_id, $data) {
        try {
            $length = unpack("N" , $data)[1];
            echo "Length = {$length}\n";
            $msg = substr($data,-$length);
            \register_shutdown_function(array($this, 'fatalErrorHandle'), $fd);
            TaskWorker::setServ($serv);
            $callData = $this->call($msg);
            $this->send($fd, $callData);
        } catch(\Exception $e) {
            $code = $e->getCode() != 0 ? $e->getCode() : -1;

            $this->send($fd, $e->getMessage(), $code);
        }
    }

    /**
     * 当接收到task任务时触发
     * @param $serv
     * @param $task_id
     * @param $from_id
     * @param $data
     */

    public function onTask($serv, $task_id, $from_id, $data) {
        try {
            $callData = $this->call($data, 'runTask');
            $serv->finish($callData);
        } catch (\Exception $e) {
            var_dump($e);
        }
    }

    public function onFinish($serv, $task_id, $data) {

    }
    /*
     *  关闭与客户端连接时回调
     */
    public function onClose($serv, $fd, $from_id) {
//        $serv->reload();
        //local环境 连接关闭时重启服务（local环境压测无意义）
        if ($this->env == 'local') {
//            $this->server->reload();  //有可能导致客户端连接失败
        }
        echo "Client {$fd} close connection\n";
    }

    /*
     *  发送消息到客户端, 统一格式，减少客户端验证
     *  保证客户端收到的消息格式为：array('code' => int, 'message' => string, 'data' => array | string )
     */
    private function send($fd, $message = '', $code = 0) {
        $sendData = array('code' => $code, 'msg' => '', 'data' => $message);
        if ($code != 0) {
            $sendData = array('code' => $code, 'msg' => $message, 'data' =>'');
        }
        if (is_array($message) && isset($message['code'])) {
            $sendData = $message;
        }
        $sendData = json_encode($sendData);
        // for($i=0;$i<5;$i++){
        //     $sendData .= $sendData;
        // }
        $msg = pack("N" , strlen($sendData)) . $sendData;
        if (strlen($msg) > 2097152) {
            $sendData = json_encode(array('code' => -1, 'msg' => '请求数据过大，不能超过2M'));
            $msg = pack("N" , strlen($sendData)). $sendData;
        }
        $this->server->send($fd, $msg);
    }


    private function call($data, $func = 'run') {
        list($module, $controller, $action, $params) = $this->parse($data);
        Config::loadByDir(SERVICE_PATH . DS . $module . DS . 'Conf');
        Config::loadByDir(SERVICE_PATH . DS . $module . DS . 'Conf' . DS . $this->env);
        $className = '\\service\\' . $module .'\\' . 'Start';
        $class = new $className();
        if (!method_exists($class, $func)) {
            throw new \Exception("操作类型错误3");
        }
        return $class->$func($controller, $action ,$params);
        // $test = new \service\Acl\Controllers\Access();
    }

    private function parse($data) {
        if (!$data) {
            throw new \Exception("没有找到数据");
        }
        $dataArr = json_decode($data, true);
        if (!isset($dataArr['action']) || empty($dataArr['action'])) {
            throw new \Exception("操作类型错误1");
        }
        $action = explode('.', $dataArr['action']);
        if (count($action) != 3) {
            throw new \Exception("操作类型错误2");
        }
        $params = array();
        if (isset($dataArr['params']) && !empty($dataArr['params'])) {
            $params = $dataArr['params'];
        }
        return array($action[0], $action[1], $action[2], $params);
    }

    public function fatalErrorHandle($fd) {
        $error = error_get_last();
        var_dump(func_get_args());
        var_dump($error);
        if (isset($error['type'])) {
            switch ($error['type']) {
                case E_ERROR :
                case E_PARSE :
                case E_DEPRECATED:
                case E_CORE_ERROR :
                case E_COMPILE_ERROR :
                    $message = $error['message'];
                    $file = $error['file'];
                    $line = $error['line'];
                    $log = "$message ($file:$line)\nStack trace:\n";
                    $trace = debug_backtrace();
                    foreach ($trace as $i => $t) {
                        if (!isset($t['file'])) {
                            $t['file'] = 'unknown';
                        }
                        if (!isset($t['line'])) {
                            $t['line'] = 0;
                        }
                        if (!isset($t['function'])) {
                            $t['function'] = 'unknown';
                        }
                        $log .= "#$i {$t['file']}({$t['line']}): ";
                        if (isset($t['object']) && is_object($t['object'])) {
                            $log .= get_class($t['object']) . '->';
                        }
                        $log .= "{$t['function']}()\n";
                    }
                    if (isset($_SERVER['REQUEST_URI'])) {
                        $log .= '[QUERY] ' . $_SERVER['REQUEST_URI'];
                    }
                    //error_log($log);
                    $this->send($fd, $log, -1);
                    
            }
        }
    }
}
