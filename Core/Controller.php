<?php
namespace Core;
/*
 * Controller 基类
 */
class Controller {
    //数据模型对象
    protected $model = null;
    
    /**
     * 拼接查询语句
     * @param array $params
     * @author linyu@273.cn 2015-5-20
     * @example $conditions = array( 
     * 'where' => array( array('test', '=', 1), array('test2', '>', 1) ), 
     * 'orWhere' => array( array('test', '=', 1), 'or' => array('test2', '>', 1) ), 
     * 'whereOr' => array( array('test', '=', 1), 'or' => array('test2', '>', 1) ), 
     * 'order' => array('create_time' => 'desc', 'id' => 'desc'), 
     * 'limit' => array($page, $pageSize)
     * );
     */
    protected function _createConditions($params) {
        if (empty($params['limit'])) {
            $params['limit'] = array(1, 1);
        }
        foreach ($params as $key => $val) {
            switch (strtolower($key)) {
                case 'where':
                    $this->_createWhere($val);
                    break;
                case 'orwhere':
                    $this->_createOrWhere($val);
                    break;
                case 'whereor':
                    $this->_createWhereOr($val);
                    break;
                case 'order':
                    $this->_createOrder($val);
                    break;
                case 'limit':
                    $this->_createLimit($val);
                    break;
                default:
                    break;
            }
        }
    }

    /**
     * @desc 根据条件拼接查询语句where
     * @param array $params
     * @author linyu@273.cn 2015-5-21
     * @example
     *  array( array('test', '=', 1), array('test2', '>', 1) ) => $this->model = $this->model->where('test', '=', 1)->where('test2', '>', 1);
     */
    protected function _createWhere($params) {
        if (empty($params)) return $this->model;
        foreach ($params as $key => $val) {
            switch (strtolower($val[1])) {
                case '=':
                case '>':
                case '<':
                case '!=':
                case 'like':
                    $this->model = $this->model->where($val[0], $val[1], $val[2]);
                    break;
                case 'in':
                    $this->model = $this->model->whereIn($val[0], $val[2]);
                    break;
                case 'not in':
                    $this->model = $this->model->whereNotIn($val[0], $val[2]);
                    break;
                default:
                    break;
            }
        }
    }
    
    /**
     * @desc 根据条件拼接查询语句where前置or查询
     * @param array $params
     * @author linyu@273.cn 2015-5-21
     * @example
     *  array(
     *   array('test', '=', 1),
     *   'or' => array('test2', '>', 1)
     *  )
     *  => $this->model = $this->model->orWhere(function($query) use ($params) { $query->where('test', '=', 1)->orWhere('test2', '>', 1);});
     *  => sql: or (`test` = 1 or `test` > 1)
     */
    protected function _createOrWhere($params) {
        if (empty($params)) return $this->model;
        $this->model = $this->model->orWhere(function($query) use ($params) {
            foreach ($params as $key => $val) {
                if (is_array($val)) {
                    if (strtolower($key) == 'or') {
                        switch (strtolower($val[1])) {
                            case '=':
                            case '>':
                            case '<':
                            case '!=':
                            case 'like':
                                $query = $query->orWhere($val[0], $val[1], $val[2]);
                                break;
                            case 'in':
                                $query = $query->orWhereIn($val[0], $val[2]);
                                break;
                            case 'not in':
                                $query = $query->orWhereNotIn($val[0], $val[2]);
                                break;
                            default:
                                break;
                        }
                    } else {
                        switch (strtolower($val[1])) {
                            case '=':
                            case '>':
                            case '<':
                            case '!=':
                            case 'like':
                                $query = $query->orWhere($val[0], $val[1], $val[2]);
                                break;
                            case 'in':
                                $query = $query->orWhereIn($val[0], $val[2]);
                                break;
                            case 'not in':
                                $query = $query->orWhereNotIn($val[0], $val[2]);
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
        });
    }
    
    /**
     * @desc 根据条件拼接查询语句where内置or查询
     * @param array $params
     * @author linyu@273.cn 2015-5-21
     * @example
     *  array(
     *   array('test', '=', 1), 
     *   'or' => array('test2', '>', 1)
     *  ) 
     *  => $this->model = $this->model->where(function($query) use ($params) { $query->where('test', '=', 1)->orWhere('test2', '>', 1);});
     *  => sql: and (`test` = 1 or `test` > 1)
     */
    protected function _createWhereOr($params) {
        if (empty($params)) return $this->model;
        $this->model = $this->model->where(function($query) use ($params) {
            foreach ($params as $key => $val) {
                if (is_array($val)) {
                    if (strtolower($key) == 'or') {
                        switch (strtolower($val[1])) {
                            case '=':
                            case '>':
                            case '<':
                            case '!=':
                            case 'like':
                                $query = $query->orWhere($val[0], $val[1], $val[2]);
                                break;
                            case 'in':
                                $query = $query->orWhereIn($val[0], $val[2]);
                                break;
                            case 'not in':
                                $query = $query->orWhereNotIn($val[0], $val[2]);
                                break;
                            default:
                                break;
                        }
                    } else {
                        switch (strtolower($val[1])) {
                            case '=':
                            case '>':
                            case '<':
                            case '!=':
                            case 'like':
                                $query = $query->orWhere($val[0], $val[1], $val[2]);
                                break;
                            case 'in':
                                $query = $query->orWhereIn($val[0], $val[2]);
                                break;
                            case 'not in':
                                $query = $query->orWhereNotIn($val[0], $val[2]);
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
        });
    }
    
    /**
     * @desc 根据条件拼接查询语句order by 
     * @param array $params
     * @author linyu@273.cn 2015-5-21
     * @example
     *  array('create_time' => 'desc', 'id' => 'desc') => $this->model = $this->model->orderBy('create_time', 'desc')->orderBy('id', 'desc');
     */
    protected function _createOrder($params) {
        if (empty($params)) return $this->model;
        foreach ($params as $key => $val) {
            $this->model = $this->model->orderBy($key, $val);
        }
    }
    
    /**
     * @desc 根据条件拼接查询语句分页部分
     * @param array $params
     * @author linyu@273.cn 2015-5-21
     * @example
     *  array(1, 10) => $this->model = $this->model->forPage(1, 10);
     */
    protected function _createLimit($params) {
        if (empty($params)) return $this->model;
        $this->model = $this->model->forPage($params[0], $params[1]);
    }
}