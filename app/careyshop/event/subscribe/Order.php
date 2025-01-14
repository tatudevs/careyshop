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

use app\careyshop\event\service\notice\Notice;
use think\Event;

class Order extends Base
{
    /**
     * 订单创建
     * @access public
     * @param array $order 订单数据
     */
    public function onCreateOrder(array $order)
    {
        Notice::instance()->send($order, self::EVENT_CREATE_ORDER);
    }

    /**
     * 订单取消
     * @access public
     * @param array $order 订单数据
     */
    public function onCancelOrder(array $order)
    {
        Notice::instance()->send($order, self::EVENT_CANCEL_ORDER);
    }

    /**
     * 订单付款
     * @access public
     * @param array $payment 支付日志
     */
    public function onPayOrder(array $payment)
    {
        Notice::instance()->send($payment, self::EVENT_PAY_ORDER);
    }

    /**
     * 订单配货
     * @access public
     * @param array $order 订单数据
     */
    public function onPickingOrder(array $order)
    {
        Notice::instance()->send($order, self::EVENT_PICKING_ORDER);
    }

    /**
     * 订单发货
     * @access public
     * @param array $order 订单数据
     */
    public function onDeliveryOrder(array $order)
    {
        Notice::instance()->send($order, self::EVENT_DELIVERY_ORDER);
    }

    /**
     * 订单完成
     * @access public
     * @param array $order 订单数据
     */
    public function onCompleteOrder(array $order)
    {
        Notice::instance()->send($order, self::EVENT_COMPLETE_ORDER);
    }

    /**
     * 调整应付金额
     * @access public
     * @param array $order 订单数据
     */
    public function onChangePriceOrder(array $order)
    {
        Notice::instance()->send($order, self::EVENT_CHANGE_PRICE_ORDER);
    }

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
