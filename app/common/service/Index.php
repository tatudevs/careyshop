<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    API协助接口服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/21
 */

namespace app\common\service;

use think\facade\Cache;

class Index extends CareyShop
{
    /**
     * 调整最优状态(正式环境有效)
     * @access public
     * @return array|false
     */
    public function setSystemOptimize()
    {
        if (ini_get('safe_mode')) {
            return $this->setError('PHP安全模式下无法运行');
        }

        if (env('app_debug', true)) {
            return $this->setError('调试模式下不需要执行');
        }

        if (!function_exists('shell_exec')) {
            return $this->setError('shell_exec 函数未启用');
        }

        $shell = [
            'clear'      => '--clear --dir',
            'schema_api' => 'optimize:schema common',
        ];

        $result = [];
        $rootPath = root_path() . 'think';

        foreach ($shell as $key => $value) {
            $output = shell_exec(sprintf('php "%s" %s', $rootPath, $value));
            $result[$key] = chop($output);
        }

        sleep(1);
        return !empty($result) ? $result : false;
    }

    /**
     * 清空所有缓存
     * @access public
     * @return bool
     */
    public function clearCacheAll()
    {
        Cache::clear();
        sleep(1);

        return true;
    }

    /**
     * 获取系统版本号
     * @access public
     * @return array
     */
    public function getVersion()
    {
        return ['version' => get_version()];
    }
}
