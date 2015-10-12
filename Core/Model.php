<?php
namespace Core;


class Model extends \Illuminate\Database\Eloquent\Model {

    public function __construct() {
    }

    /**
     * 获取最后一条sql语句
     * @return mixed
     */
    public static function lastSql() {
        $logArr = self::sqlLog(false);

        var_dump(end($logArr));
    }

    /**
     * @desc   获取一组sql语句
     * @param bool $isOutput
     * @return array
     */
    public static function sqlLog($isOutput = true) {
        $callClass = get_called_class();
        $connection = (new $callClass)->getConnectionName();
        $connection || $connection = 'default';
        $sqlLogArr = (new \Illuminate\Database\Capsule\Manager)->connection($connection)->getQueryLog();

        if ($isOutput) {
            var_dump($sqlLogArr);
        } else {
            return $sqlLogArr;
        }
    }
}
