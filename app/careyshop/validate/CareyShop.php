<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    公共验证基类
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/21
 */

namespace app\careyshop\validate;

use think\Validate;

class CareyShop extends Validate
{
    /**
     * 获取某个字段的描述
     * @access public
     * @param string $field 参数
     * @return string
     */
    public function getField(string $field): string
    {
        return $this->field[$field] ?? $field;
    }

    /**
     * 获取规则全部键名
     * @access public
     * @return array
     */
    public function getRuleKey(): array
    {
        return array_keys($this->rule);
    }

    /**
     * 提取场景字段载入到规则
     * @access public
     * @param array  $data  验证数据
     * @param string $name  场景名
     * @param bool   $clean 当需要清理$data时场景过滤启用
     * @param string $pk    模型主键
     * @throws
     */
    public function extractScene(array $data, string $name, bool $clean, string $pk)
    {
        // 为了兼容数组格式的场景验证,不对函数式场景做检测
        if (!isset($this->scene[$name])) {
            throw new \Exception('验证规则场景 ' . $name . ' 不存在');
        }

        $rule = [];
        $scene = $this->scene[$name];

        foreach ($scene as $key => $value) {
            $sceneKey = is_numeric($key) ? $value : $key;
            if ($clean && $sceneKey != $pk) {
                if (!array_key_exists($sceneKey, $data)) {
                    continue;
                }
            }

            if (is_numeric($key)) {
                $rule[$value] = $this->rule[$value];
            } else {
                $rule[$key] = $value;
            }
        }

        $this->rule = $rule;
    }

    /**
     * 日期是否在合理范围内
     * @access public
     * @param mixed ...$args [验证数据,验证规则,全部数据,字段名,字段描述]
     * @return bool|string
     */
    public function betweenTime(...$args)
    {
        if (strtotime($args[0]) >= 0 && strtotime($args[0]) <= 2147483647) {
            return true;
        }

        return $args[4] . '不在合理日期范围内';
    }

    /**
     * 某个字段的值是否小于某个字段(日期)
     * @access public
     * @param mixed ...$args [验证数据,验证规则,全部数据,字段名,字段描述]
     * @return bool|string
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
     * 某个字段的值是否大于某个字段(日期)
     * @access public
     * @param mixed ...$args [验证数据,验证规则,全部数据,字段名,字段描述]
     * @return bool|string
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
     * @param mixed ...$args [验证数据,验证规则,全部数据,字段名,字段描述]
     * @return bool|string
     */
    public function arrayHasOnlyInts(...$args)
    {
        if (!is_array($args[0])) {
            return $args[4] . '必须是数组';
        }

        $isZero = 'zero' == $args[1]; // 允许存在小于等于0的整数
        if ($args[0] === array_filter($args[0], function ($value) use ($isZero) {
                if ($this->filter($value, FILTER_VALIDATE_INT)) {
                    if (false == $isZero && $value <= 0) {
                        return false;
                    }

                    return true;
                }

                return false;
            })
        ) {
            return true;
        }

        return $args[4] . ($isZero ? '内的键值必须是合法的整数' : '内的键值必须是大于零的整数');
    }

    /**
     * 检测数组内所有键值是否都为string
     * @access public
     * @param mixed ...$args [验证数据,验证规则,全部数据,字段名,字段描述]
     * @return bool|string
     */
    public function arrayHasOnlyStrings(...$args)
    {
        if (!is_array($args[0])) {
            return $args[4] . '必须是数组';
        }

        if ($args[0] === array_filter($args[0], fn($value) => is_string($value))) {
            return true;
        }

        return $args[4] . '内的键值必须是字符串';
    }

    /**
     * 验证模块是否在指定范围内
     * @access public
     * @param mixed ...$args [验证数据,验证规则,全部数据,字段名,字段描述]
     * @return bool|string
     */
    public function checkModule(...$args)
    {
        switch ($args[1]) {
            case 'module':
                $moduleList = config('extra.module_group');
                break;

            case 'oauth':
                $moduleList = config('extra.oauth2');
                break;

            default:
                $moduleList = null;
        }

        if (is_null($moduleList)) {
            return $args[4] . '验证规则 checkModule 设置错误';
        }

        if (!isset($moduleList[$args[0]])) {
            return sprintf('%s必须在 %s 范围内', $args[4], implode(',', array_keys($moduleList)));
        }

        return true;
    }
}
