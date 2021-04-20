<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    提现事件订阅
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/20
 */

namespace app\careyshop\event\subscribe;

use think\Event;

class Withdraw
{
    /**
     * 事件订阅
     * @access public
     * @param Event $event 事件对象
     */
    public function subscribe(Event $event)
    {
        // 申请提现
        $event->listen('ApplyWithdraw', [$this, 'onApplyWithdraw']);
        // 取消提现
        $event->listen('CancelWithdraw', [$this, 'onCancelWithdraw']);
        // 处理提现
        $event->listen('ProcessWithdraw', [$this, 'onProcessWithdraw']);
        // 完成提现
        $event->listen('CompleteWithdraw', [$this, 'onCompleteWithdraw']);
        // 拒绝提现
        $event->listen('RefuseWithdraw', [$this, 'onRefuseWithdraw']);
    }
}
