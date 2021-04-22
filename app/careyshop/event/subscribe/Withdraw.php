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

use app\careyshop\event\service\notice\Notice;
use think\Event;

class Withdraw extends Base
{
    /**
     * 申请提现
     * @access public
     * @param array $withdraw 提现数据
     */
    public function onApplyWithdraw(array $withdraw)
    {
        Notice::instance()->send($withdraw, self::EVENT_APPLY_WITHDRAW);
    }

    /**
     * 取消提现
     * @access public
     * @param array $withdraw 提现数据
     */
    public function onCancelWithdraw(array $withdraw)
    {
        Notice::instance()->send($withdraw, self::EVENT_CANCEL_WITHDRAW);
    }

    /**
     * 处理提现
     * @access public
     * @param array $withdraw 提现数据
     */
    public function onProcessWithdraw(array $withdraw)
    {
        Notice::instance()->send($withdraw, self::EVENT_PROCESS_WITHDRAW);
    }

    /**
     * 完成提现
     * @access public
     * @param array $withdraw 提现数据
     */
    public function onCompleteWithdraw(array $withdraw)
    {
        Notice::instance()->send($withdraw, self::EVENT_COMPLETE_WITHDRAW);
    }

    /**
     * 拒绝提现
     * @access public
     * @param array $withdraw 提现数据
     */
    public function onRefuseWithdraw(array $withdraw)
    {
        Notice::instance()->send($withdraw, self::EVENT_REFUSE_WITHDRAW);
    }

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
