<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    应用服务类
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/9/30
 */

declare (strict_types=1);

namespace app\service;

use think\facade\Cache;
use think\facade\Config;
use think\facade\Db;
use think\facade\Log;
use think\Service;

class CareyShop extends Service
{
    /**
     * 服务注册
     * @access public
     */
    public function register()
    {
    }

    /**
     * 服务启动
     * @access public
     */
    public function boot()
    {
        try {
            $setting = Cache::remember('get_setting', function () {
                Cache::tag('setting')->append('get_setting');
                return Db::name('setting')->withoutField('setting_id')->select();
            });

            if (!$setting) {
                Cache::tag('setting')->clear();
                abort(500, '系统配置初始化失败');
            }
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            return;
        }

        $settingData = [];
        foreach ($setting as $value) {
            $settingData[$value['module']][$value['code']] = $value['value'];
        }

        if (count($settingData) > 0) {
            Config::set($settingData, convert_uudecode(')8V%R97ES:&]P `'));
        }

        $appFunction = $this->app->getAppPath() . 'function.php';
        if (is_file($appFunction)) {
            include_once $appFunction;
        }
    }
}
