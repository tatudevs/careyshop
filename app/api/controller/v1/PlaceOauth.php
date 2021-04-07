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

class PlaceOauth extends CareyShop
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
            'add.place.oauth.item'   => ['addPlaceOAuthItem'],
            // 编辑一条授权机制
            'set.place.oauth.item'   => ['setPlaceOAuthItem'],
            // 批量删除授权机制
            'del.place.oauth.list'   => ['delPlaceOAuthList'],
            // 获取一条授权机制
            'get.place.oauth.item'   => ['getPlaceOAuthItem'],
            // 获取授权机制列表
            'get.place.oauth.list'   => ['getPlaceOAuthList'],
            // 批量设置机制状态
            'set.place.oauth.status' => ['setPlaceOAuthStatus'],
            // OAuth2.0授权准备
            'authorize'              => ['authorizeOAuth'],
            // OAuth2.0回调验证
            'callback'               => ['callbackOAuth'],
        ];
    }
}
