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
     * 检测是否存在相同值
     * @access public
     * @param array $map 查询条件
     * @return bool false:不存在
     * @throws
     */
    public static function checkUnique($map)
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
     * 设置模型错误信息并抛出异常
     * @access public
     * @param string $value 错误信息
     * @return bool
     */
    public function setError($value)
    {
        $this->error = $value;
        return false;
    }

    /**
     * 翻页搜索器
     * @access public
     * @param CareyShop $query
     * @param string    $value
     * @param array     $data
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
     * @param CareyShop $query
     * @param array     $value
     * @param array     $data
     */
    public function searchOrderAttr($query, $value, $data)
    {
        if (isset($data['order_field']) && isset($data['order_type'])) {
            $query->order([$data['order_field'] => $data['order_type']]);
        } else {
            $query->order($this->defaultOrder);
        }
    }

    /**
     * 设置默认排序
     * @access public
     * @param array $order
     * @return $this
     */
    public function setDefaultOrder($order)
    {
        $this->defaultOrder = $order;
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
    public function validateData(&$data, $scene = null, $clean = false, $validate = '')
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
}
