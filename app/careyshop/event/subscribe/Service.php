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

use think\Event;

class Service extends Base
{
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
