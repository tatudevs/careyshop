<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    公共模型基类
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/21
 */

namespace app\common\model;

use think\exception\ValidateException;
use think\facade\Config;
use think\helper\Str;
use think\Model;

abstract class CareyShop extends Model
{
    /**
     * 错误信息
     * @var string
     */
    protected $error = '';

    /**
     * 默认排序
     * @var array
     */
    protected $defaultOrder = [];

    /**
     * 固定排序
     * @var array
     */
    protected $fixedOrder = [];

    /**
     * 检测是否存在相同值
     * @access public
     * @param array $map 查询条件
     * @return bool false:不存在
     */
    public static function checkUnique(array $map)
    {
        if (empty($map)) {
            return true;
        }

        $count = self::where($map)->count();
        if (is_numeric($count) && $count <= 0) {
            return false;
        }

        return true;
    }

    /**
     * 返回模型的错误信息
     * @access public
     * @return string|array
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 设置模型错误信息
     * @access public
     * @param string $value 错误信息
     * @return bool
     */
    public function setError(string $value)
    {
        $this->error = $value;
        return false;
    }

    /**
     * 翻页搜索器
     * @access public
     * @param object $query
     * @param mixed  $value
     * @param mixed  $data
     */
    public function searchPageAttr($query, $value, $data)
    {
        $pageNo = isset($data['page_no']) ? $data['page_no'] : 1;
        $pageSize = isset($data['page_size']) ? $data['page_size'] : Config::get('app.list_rows');

        $query->page($pageNo, $pageSize);
    }

    /**
     * 排序搜索器
     * @access public
     * @param object $query
     * @param mixed  $value
     * @param mixed  $data
     */
    public function searchOrderAttr($query, $value, $data)
    {
        $order = [];
        if (isset($data['order_field']) || isset($data['order_type'])) {
            $order[$data['order_field']] = $data['order_type'];
        } else {
            $order = $this->defaultOrder;
        }

        if (!empty($this->fixedOrder)) {
            // 固定排序必须在前,否则将导致自定义排序无法覆盖
            $order = array_merge($this->fixedOrder, $order);
            if (!empty($data['order_field'])) {
                $order = array_reverse($order);
            }
        }

        if (!empty($order)) {
            $query->order($order);
        }
    }

    /**
     * 设置默认排序
     * @access public
     * @param array $order 默认排序
     * @param array $fixed 固定排序
     * @return $this
     */
    public function setDefaultOrder(array $order, $fixed = [])
    {
        $this->defaultOrder = $order;
        $this->fixedOrder = $fixed;

        return $this;
    }

    /**
     * 模型验证器
     * @access public
     * @param array|object $data     验证数据
     * @param string|null  $scene    场景名
     * @param bool         $clean    是否清理规则键值不存在的$data
     * @param string       $validate 验证器规则或类
     * @return bool
     */
    public function validateData(array &$data, $scene = null, $clean = false, $validate = '')
    {
        try {
            // 确定规则来源
            if (empty($validate)) {
                $class = '\\app\\common\\validate\\' . $this->getName();
                if ($scene) {
                    $v = new $class();
                    $v->extractScene($scene);
                } else {
                    $v = validate($class);
                }
            } else {
                $v = validate($validate);
                if ($scene) {
                    $v->extractScene($scene);
                }
            }

            if ($clean) {
                $keys = $v->getRuleKey();
                foreach ($data as $key => $value) {
                    if (!in_array($key, $keys, true)) {
                        unset($data[$key]);
                    }
                }

                unset($key, $value);
            }

            $v->failException(true)->check($data);
        } catch (ValidateException $e) {
            return $this->setError($e->getMessage());
        }

        return true;
    }

    /**
     * 替换数组中的驼峰键名为下划线
     * @access public
     * @param array $name 需要修改的键名
     * @param array $data 源数据
     */
    public static function keyToSnake(array $name, array &$data)
    {
        if (!is_array($name)) {
            return;
        }

        foreach ($name as $value) {
            foreach ($data as &$item) {
                if (!array_key_exists($value, $item)) {
                    continue;
                }

                $temp = $item[$value];
                unset($item[$value]);

                $item[Str::snake($value)] = $temp;
            }
        }
    }
}
