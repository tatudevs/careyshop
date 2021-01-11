<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    通知系统控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/7/17
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;
use app\careyshop\service\Notice as NoticeService;

class Notice extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return void
     */
    protected static function initMethod()
    {
        self::$route = [
            // 获取一个通知系统
            'get.notice.item'   => ['getNoticeItem'],
            // 批量设置通知系统是否启用
            'set.notice.status' => ['setNoticeStatus'],
            // 设置一个通知系统
            'set.notice.item'   => ['setNoticeItem'],
            // 获取通知系统列表
            'get.notice.list'   => ['getNoticeList', NoticeService::class],
        ];
    }
}
