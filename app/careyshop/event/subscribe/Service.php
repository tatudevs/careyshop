<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    售后服务事件订阅
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/20
 */

namespace app\careyshop\event\subscribe;

use app\careyshop\event\service\notice\Notice;
use think\Event;

class Service extends Base
{
    /**
     * 同意售后
     * @access public
     * @param array $service 售后数据
     */
    public function onAgreeService(array $service)
    {
        Notice::instance()->send($service, self::EVENT_AGREE_SERVICE);
    }

    /**
     * 拒绝售后
     * @access public
     * @param array $service 售后数据
     */
    public function onRefuseService(array $service)
    {
        Notice::instance()->send($service, self::EVENT_REFUSE_SERVICE);
    }

    /**
     * 正在售后
     * @access public
     * @param array $service 售后数据
     */
    public function onAfterService(array $service)
    {
        Notice::instance()->send($service, self::EVENT_AFTER_SERVICE);
    }

    /**
     * 售后撤销
     * @access public
     * @param array $service 售后数据
     */
    public function onCancelService(array $service)
    {
        Notice::instance()->send($service, self::EVENT_CANCEL_SERVICE);
    }

    /**
     * 售后完成
     * @access public
     * @param array $service 售后数据
     */
    public function onCompleteService(array $service)
    {
        Notice::instance()->send($service, self::EVENT_COMPLETE_SERVICE);
    }

    /**
     * 留言被回复
     * @access public
     * @param array $service 售后数据
     */
    public function onReplyService(array $service)
    {
        Notice::instance()->send($service, self::EVENT_REPLY_SERVICE);
    }

    /**
     * 商品要求寄回
     * @access public
     * @param array $service 售后数据
     */
    public function onSendbackService(array $service)
    {
        Notice::instance()->send($service, self::EVENT_SENDBACK_SERVICE);
    }

    /**
     * 事件订阅
     * @access public
     * @param Event $event 事件对象
     */
    public function subscribe(Event $event)
    {
        // 同意售后
        $event->listen('AgreeService', [$this, 'onAgreeService']);
        // 拒绝售后
        $event->listen('RefuseService', [$this, 'onRefuseService']);
        // 正在售后
        $event->listen('AfterService', [$this, 'onAfterService']);
        // 售后撤销
        $event->listen('CancelService', [$this, 'onCancelService']);
        // 售后完成
        $event->listen('CompleteService', [$this, 'onCompleteService']);
        // 留言被回复
        $event->listen('ReplyService', [$this, 'onReplyService']);
        // 商品要求寄回
        $event->listen('SendbackService', [$this, 'onSendbackService']);
    }
}
