<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    公共模型基类
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/21
 */

namespace app\careyshop\model;

use think\facade\Config;
use think\helper\Str;
use think\Model;

abstract class CareyShop extends Model
{
    use \app\careyshop\concern\Base;
    use \app\careyshop\concern\Error;
    use \app\careyshop\concern\Validate;

    /**
     * 默认排序
     * @var array
     */
    protected array $defaultOrder = [];

    /**
     * 固定排序
     * @var array
     */
    protected array $fixedOrder = [];

    /**
     * 是否调整顺序
     * @var bool
     */
    protected bool $isReverse = false;

    /**
     * 排序别名
     * @var string
     */
    protected string $aliasOrder = '';

    /**
     * 检测是否存在相同值
     * @access public
     * @param array $map 查询条件
     * @return bool false:不存在
     */
    public static function checkUnique(array $map): bool
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
     * 翻页搜索器
     * @access public
     * @param mixed ...$args 参数
     */
    public function searchPageAttr(...$args)
    {
        $args[0]->page($args[2]['page_no'] ?? 1, $args[2]['page_size'] ?? Config::get('app.list_rows'));
    }

    /**
     * 排序搜索器
     * @access public
     * @param mixed ...$args 参数
     */
    public function searchOrderAttr(...$args)
    {
        if (isset($args[2]['order_field']) || isset($args[2]['order_type'])) {
            $order[$args[2]['order_field']] = $args[2]['order_type'];
        } else {
            $order = $this->defaultOrder;
        }

        if (!empty($this->fixedOrder)) {
            // 固定排序必须在前,否则将导致自定义排序无法覆盖
            $order = array_merge($this->fixedOrder, $order);
            if (!empty($args[2]['order_field']) && $this->isReverse) {
                $order = array_reverse($order);
            }
        }

        if (!empty($order)) {
            if (!empty($this->aliasOrder)) {
                foreach ($order as $key => $value) {
                    $alias = false === strpos($key, '.') ? ($this->aliasOrder . '.' . $key) : $key;
                    $aliasOrder[$alias] = $value;
                }
            }

            $args[0]->order($aliasOrder ?? $order);
        }
    }

    /**
     * 设置默认排序
     * @access public
     * @param array $order   默认排序
     * @param array $fixed   固定排序
     * @param bool  $reverse 是否调整顺序
     * @return $this
     */
    public function setDefaultOrder(array $order, array $fixed = [], bool $reverse = false): CareyShop
    {
        $this->defaultOrder = $order;
        $this->fixedOrder = $fixed;
        $this->isReverse = $reverse;

        return $this;
    }

    /**
     * 设置排序别名
     * @access public
     * @param string $alias 别名
     * @return $this
     */
    public function setAliasOrder(string $alias): CareyShop
    {
        $this->aliasOrder = $alias;

        return $this;
    }

    /**
     * 替换数组中的驼峰键名为下划线
     * @access public
     * @param array  $name 需要修改的键名
     * @param array &$data 源数据
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
