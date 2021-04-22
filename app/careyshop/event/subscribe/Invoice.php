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

use app\careyshop\event\service\notice\Notice;
use think\Event;

class Invoice extends Base
{
    /**
     * 票据已开
     * @access public
     * @param array $invoice 开票数据
     */
    public function onCompleteInvoice(array $invoice)
    {
        Notice::instance()->send($invoice, self::EVENT_COMPLETE_INVOICE);
    }

    /**
     * 拒绝开票
     * @access public
     * @param array $invoice 开票数据
     */
    public function onRefuseInvoice(array $invoice)
    {
        Notice::instance()->send($invoice, self::EVENT_REFUSE_INVOICE);
    }

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
