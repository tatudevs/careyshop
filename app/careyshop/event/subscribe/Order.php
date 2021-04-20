<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    订单管理事件订阅
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/20
 */

namespace app\careyshop\event\subscribe;

use think\Event;

class Order
{
    /**
     * 事件订阅
     * @access public
     * @param Event $event 事件对象
     */
    public function subscribe(Event $event)
    {
        // 订单创建
        $event->listen('CreateOrder', [$this, 'onCreateOrder']);
        // 订单取消
        $event->listen('CancelOrder', [$this, 'onCancelOrder']);
        // 订单付款
        $event->listen('PayOrder', [$this, 'onPayOrder']);
        // 订单配货
        $event->listen('PickingOrder', [$this, 'onPickingOrder']);
        // 订单发货
        $event->listen('DeliveryOrder', [$this, 'onDeliveryOrder']);
        // 订单完成
        $event->listen('CompleteOrder', [$this, 'onCompleteOrder']);
        // 调整应付金额
        $event->listen('ChangePriceOrder', [$this, 'onChangePriceOrder']);
    }
}
