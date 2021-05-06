<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/14
 */

namespace app\careyshop\concern;

use think\{Model, exception\ValidateException};

trait Validate
{
    /**
     * 模型验证器
     * @access public
     * @param array|object $data     验证数据
     * @param string|null  $scene    场景名
     * @param bool         $clean    是否清理规则键值不存在的$data
     * @param string|array $validate 验证器规则或类
     * @return bool
     */
    public function validateData(array &$data, string $scene = null, bool $clean = false, $validate = ''): bool
    {
        try {
            // 确定规则来源
            if (empty($validate) && is_string($validate)) {
                $class = $this->getValidateClass();
                if ($scene) {
                    $v = new $class();
                    $v->extractScene($data, $scene, $clean, $this instanceof Model ? $this->getPk() : '');
                } else {
                    $v = validate($class);
                }
            } else {
                $v = validate($validate);
                if ($scene) {
                    $v->extractScene($data, $scene, $clean, $this instanceof Model ? $this->getPk() : '');
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
        $namespace = '\\app\\careyshop\\validate\\' . basename(static::class);
        if (!class_exists($namespace)) {
            throw new ValidateException("验证器 $namespace 不存在");
        }

        return $namespace;
    }
}
