<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    应用管理控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/21
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;

class App extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return array
     */
    protected static function initMethod()
    {
        return [
            // 添加一个应用
            'add.app.item'            => ['addAppItem'],
            // 编辑一个应用
            'set.app.item'            => ['setAppItem'],
            // 获取一个应用
            'get.app.item'            => ['getAppItem'],
            // 获取应用列表
            'get.app.list'            => ['getAppList'],
            // 批量删除应用
            'del.app.list'            => ['delAppList'],
            // 查询应用名称是否已存在
            'unique.app.name'         => ['uniqueAppName'],
            // 更换应用Secret
            'replace.app.secret'      => ['replaceAppSecret'],
            // 批量设置应用验证码
            'set.app.captcha'         => ['setAppCaptcha'],
            // 批量设置应用状态
            'set.app.status'          => ['setAppStatus'],
            // 查询应用验证码状态
            'get.app.captcha'         => ['getAppCaptcha'],
            // 获取应用验证码调用地址
            'get.app.captcha.callurl' => ['getCaptchaCallurl', 'app\common\service\App'],
            // 获取应用验证码
            'image.app.captcha'       => ['imageAppCaptcha', 'app\common\service\App'],
        ];
    }

    /**
     * 查询应用验证码
     * @access protected
     * @return array|false
     */
    protected function getAppCaptcha()
    {
//        $appKey = $this->request->param('appkey');
//        return \app\common\model\App::getAppCaptcha($appKey);
        return [];
    }
}
