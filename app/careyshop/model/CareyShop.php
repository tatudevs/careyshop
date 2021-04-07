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

use think\exception\ValidateException;
use think\facade\Config;
use think\helper\Str;
use think\Model;

abstract class CareyShop extends Model
{
    /**
     * 控制器版本号
     * @var string
     */
    public string $version = 'v1';

    /**
     * 错误信息
     * @var string
     */
    protected string $error = '';

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
     * 返回模型的错误信息
     * @access public
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * 设置模型错误信息
     * @access public
     * @param string $value 错误信息
     * @return false
     */
    public function setError(string $value)
    {
        $this->error = $value;
        return false;
    }

    /**
     * 翻页搜索器
     * @access public
     * @param mixed ...$args 参数
     */
    public function searchPageAttr(...$args)
    {
        $pageNo = $args[2]['page_no'] ?? 1;
        $pageSize = $args[2]['page_size'] ?? Config::get('app.list_rows');

        $args[0]->page($pageNo, $pageSize);
    }

    /**
     * 排序搜索器
     * @access public
     * @param mixed ...$args 参数
     */
    public function searchOrderAttr(...$args)
    {
        $order = [];
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
            $args[0]->order($order);
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
    public function setDefaultOrder(array $order, $fixed = [], $reverse = false): CareyShop
    {
        $this->defaultOrder = $order;
        $this->fixedOrder = $fixed;
        $this->isReverse = $reverse;

        return $this;
    }

    /**
     * 模型验证器
     * @access public
     * @param array|object $data     验证数据
     * @param string|null  $scene    场景名
     * @param bool         $clean    是否清理规则键值不存在的$data
     * @param string|array $validate 验证器规则或类
     * @return bool
     */
    public function validateData(array &$data, $scene = null, $clean = false, $validate = ''): bool
    {
        try {
            // 确定规则来源
            if (empty($validate) && is_string($validate)) {
                $class = $this->getValidateClass();
                if ($scene) {
                    $v = new $class();
                    $v->extractScene($data, $scene, $clean, $this->getPk());
                } else {
                    $v = validate($class);
                }
            } else {
                $v = validate($validate);
                if ($scene) {
                    $v->extractScene($data, $scene, $clean, $this->getPk());
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
     * 尝试获取验证器类
     * @access private
     * @return string
     */
    private function getValidateClass(): string
    {
        $pos = mb_strrpos(__NAMESPACE__, '\\');
        $dir = '\\validate\\' . $this->getName();

        $class = Str::substr(__NAMESPACE__, 0, $pos) . $dir;
        if (!class_exists($class)) {
            throw new ValidateException("验证器 {$class} 不存在");
        }

        return $class;
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
