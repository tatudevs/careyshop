<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    OAuth2.0控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/3/26
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;

class Oauth extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return void
     */
    protected static function initMethod()
    {
        self::$route = [
            // 添加一条授权机制
            'add.oauth.item'   => ['addOAuthItem'],
            // 编辑一条授权机制
            'set.oauth.item'   => ['setOAuthItem'],
            // 批量删除授权机制
            'del.oauth.list'   => ['delOAuthList'],
            // 获取一条授权机制
            'get.oauth.item'   => ['getOAuthItem'],
            // 获取授权机制列表
            'get.oauth.list'   => ['getOAuthList'],
            // 获取可使用的授权机制列表
            'get.oauth.type'   => ['getOAuthType'],
            // 批量设置授权机制状态
            'set.oauth.status' => ['setOAuthStatus'],
            // OAuth2.0登录
            'login.oauth.user' => ['loginOAuthUser'],
        ];
    }
}
