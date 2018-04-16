<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    公共验证基类
 *
 * @author      zxm <252404501@qq.com>
 * @version     v1.1
 * @date        2017/6/8
 */

namespace app\common\validate;

use think\Validate;

class CareyShop extends Validate
{
    /**
     * 获取某个字段的描述
     * @access public
     * @param array $args 参数
     * @return bool
     */
    public function getField($field)
    {
        return isset($this->field[$field]) ? $this->field[$field] : $field;
    }

    /**
     * 时间是否在合理范围内
     * @access public
     * @param array $args 参数
     * @return bool
     */
    public function betweenTime(...$args)
    {
        if (strtotime($args[0]) >= 0 && strtotime($args[0]) <= 2147472000) {
            return true;
        }

        return $args[4] . '不在合理时间范围内';
    }

    /**
     * 某个字段的值是否小于某个字段(时间)
     * @access public
     * @param array $args 参数
     * @return bool
     */
    public function beforeTime(...$args)
    {
        if (!isset($args[2][$args[1]])) {
            return $this->getField($args[1]) . '不能为空';
        }

        if (strtotime($args[0]) <= strtotime($args[2][$args[1]])) {
            return true;
        }

        return $args[4] . '不能大于 ' . $this->getField($args[1]);
    }

    /**
     * 某个字段的值是否大于某个字段(时间)
     * @access public
     * @param array $args 参数
     * @return bool
     */
    public function afterTime(...$args)
    {
        if (!isset($args[2][$args[1]])) {
            return $this->getField($args[1]) . '不能为空';
        }

        if (strtotime($args[0]) >= strtotime($args[2][$args[1]])) {
            return true;
        }

        return $args[4] . '不能小于 ' . $this->getField($args[1]);
    }

    /**
     * 检测数组内所有键值是否都为int
     * @access public
     * @param array $args 参数
     * @return bool
     */
    public function arrayHasOnlyInts(...$args)
    {
        if (!is_array($args[0])) {
            return $args[4] . '必须是数组';
        }

        if ($args[0] === array_filter($args[0], function ($value) {
                return $this->filter($value, FILTER_VALIDATE_INT) && $value > 0;
            })
        ) {
            return true;
        }

        return $args[4] . '内的键值必须是大于零的整数';
    }

    /**
     * 获取验证器编辑场景
     * @access public
     * @param string $scene 场景名
     * @return array
     */
    public function getSetScene($scene)
    {
        return isset($this->scene[$scene]) ? $this->scene[$scene] : [];
    }

    /**
     * 验证模块是否在指定范围内
     * @access public
     * @param array $args 参数
     * @return bool
     */
    public function checkModule(...$args)
    {
        $moduleList = config('CareyShop.module_group');
        if (!isset($moduleList[$args[0]])) {
            return sprintf('%s必须在 %s 范围内', $args[4], implode(',', array_keys($moduleList)));
        }

        return true;
    }
}