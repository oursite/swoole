<?php
/**
 * 数据验证类，继承自vlucas/valitron ：https://github.com/vlucas/valitron
 * @author    dutao <dutao@273.cn>
 * @since     2015/4/30
 * @copyright Copyright (c) 2003-2015 273 Inc. (http://www.273.cn)
 */

namespace Lib;


class Validator extends \Valitron\Validator {

    public function __construct($data, $fields = array(), $lang = null, $langDir = null) {
        parent::__construct($data, $fields, 'zh-cn', $langDir);
    }

    /**
     * Get the length of a string
     * 复写此方法，因为mb_strlen长度计算的问题
     *
     * @param  string $value
     * @return int
     */
    protected function stringLength($value) {
        if (function_exists('mb_strlen')) {
            return mb_strlen($value, 'UTF-8');
        }
        return strlen($value);
    }

    /**
     * 获取第一个错误的错误说明（errors方法只能回去所有错误的数组）
     * @return string
     */
    public function firstError() {
        $errors = $this->errors();
        if (!empty($errors)) {
            foreach ($errors as $error) {
                return $error[0];
            }
        }
        return false;
    }
}