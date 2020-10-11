<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    操作日志控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2018/10/24
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;

class ActionLog extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return void
     */
    protected static function initMethod()
    {
        self::$route = [
            // 获取一条操作日志
            'get.action.log.item' => ['getActionLogItem'],
            // 获取操作日志列表
            'get.action.log.list' => ['getActionLogList'],
        ];
    }
}
