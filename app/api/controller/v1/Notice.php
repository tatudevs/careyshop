<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    通知系统控制器
 *
 * @author      zxm <252404501@qq.com>
 * @version     v1.1
 * @date        2021/4/20
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;
use app\careyshop\model\NoticeEvent;

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
            // 添加一个通知模板
            'add.notice.item'   => ['addNoticeItem'],
            // 编辑一个通知模板
            'set.notice.item'   => ['setNoticeItem'],
            // 批量删除通知模板
            'del.notice.list'   => ['delNoticeList'],
            // 获取一个通知模板
            'get.notice.item'   => ['getNoticeItem'],
            // 获取通知模板列表
            'get.notice.list'   => ['getNoticeList'],
            // 批量设置模板状态
            'set.notice.status' => ['setNoticeStatus'],
            // 根据渠道获取事件列表
            'get.notice.event'  => ['getNoticeEvent', NoticeEvent::class],
        ];
    }
}
