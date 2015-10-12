<?php
/**
 * @desc 读取数据表的配置
 * @author limy
 * @since 2015-04-23
 */
namespace Lib;

class TableConfig {
    
    //数据表配置主目录
    protected static $_BASE_PATH = '';

    //配置缓存变量
    protected static $_DATA = array();

    /**
     * @desc 获取某张表的配置
     * @author limy
     * @return array 数据表的配置数组
     */
    public static function getConfig($db, $table) {
        $key = $db . '_' . $table;
        $path = self::_getBasePath();
        $file = $path . $db . '/' . $table . '.php';

        $config = array();
        if( !isset(self::$_DATA[$key]) ) {
            if( file_exists($file) ) {
                $config = require $file;
            }

            self::$_DATA[$key] = $config;
        }

        return self::$_DATA[$key];
    }

    /**
     * @desc 获取数据表配置主目录
     * @author limy
     * @return string 目录路径
     */
    protected static function _getBasePath() {
        if( self::$_BASE_PATH ) {
            return self::$_BASE_PATH;
        }
        self::$_BASE_PATH = ROOT_PATH . '../../conf/tableConfig/';
        return self::$_BASE_PATH;
    }
}
