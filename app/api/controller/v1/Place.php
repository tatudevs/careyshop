<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    渠道平台控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/13
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;

class Place extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return void
     */
    protected static function initMethod()
    {
        self::$route = [
            // 获取框架已支持的渠道平台
            'get.place.exist'   => ['getPlaceExist'],
            // 获取某个平台下指定模块的默认配置结构
            'get.place.setting' => ['getPlaceSetting'],
            // 添加一个渠道平台
            'add.place.item'    => ['addPlaceItem'],
            // 编辑一个渠道平台
            'set.place.item'    => ['setPlaceItem'],
            // 获取一个渠道平台
            'get.place.item'    => ['getPlaceItem'],
            // 获取渠道平台列表
            'get.place.list'    => ['getPlaceList'],
            // 批量删除渠道平台
            'del.place.list'    => ['delPlaceList'],
            // 批量设置渠道平台状态
            'set.place.status'  => ['setPlaceStatus'],
            // 获取渠道的授权机制
            'get.place.oauth'   => ['getPlaceOAuth'],
        ];
    }
}
