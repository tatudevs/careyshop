<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    微服务控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/13
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;

class MiniService extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return void
     */
    protected static function initMethod()
    {
        self::$route = [
            // 获取框架已支持的微服务
            'get.miniservice.exist'   => ['getMiniServiceExist'],
            // 获取某个平台下指定模块的默认配置结构
            'get.miniservice.setting' => ['getMiniServiceSetting'],
            // 添加一个微服务
            'add.miniservice.item'    => ['addMiniServiceItem'],
            // 编辑一个微服务
            'set.miniservice.item'    => ['setMiniServiceItem'],
            // 获取一个微服务
            'get.miniservice.item'    => ['getMiniServiceItem'],
            // 获取微服务列表
            'get.miniservice.list'    => ['getMiniServiceList'],
            // 批量删除微服务
            'del.miniservice.list'    => ['delMiniServiceList'],
            // 批量设置微服务状态
            'set.miniservice.status'  => ['setMiniServiceStatus'],
        ];
    }
}
