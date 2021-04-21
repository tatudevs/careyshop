<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    票据管理事件订阅
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/20
 */

namespace app\careyshop\event\subscribe;

use think\Event;

class Invoice extends Base
{
    /**
     * 事件订阅
     * @access public
     * @param Event $event 事件对象
     */
    public function subscribe(Event $event)
    {
        // 票据已开
        $event->listen('CompleteInvoice', [$this, 'onCompleteInvoice']);
        // 拒绝开票
        $event->listen('RefuseInvoice', [$this, 'onRefuseInvoice']);
    }
}
