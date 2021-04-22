<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品事件订阅
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/20
 */

namespace app\careyshop\event\subscribe;

use app\careyshop\event\service\notice\Notice;
use think\Event;

class Goods extends Base
{
    /**
     * 咨询被客服回复
     * @access public
     * @param array $user 账号数据
     */
    public function onServiceReplyConsult(array $user)
    {
        Notice::instance()->send($user, self::EVENT_SERVICE_REPLY_CONSULT);
    }

    /**
     * 评价被顾客咨询
     * @access public
     * @param array $user 账号数据
     */
    public function onCustomerAdvisoryComment(array $user)
    {
        Notice::instance()->send($user, self::EVENT_CUSTOMER_ADVISORY_COMMENT);
    }

    /**
     * 评价被客服回复
     * @access public
     * @param array $user 账号数据
     */
    public function onServiceReplyComment(array $user)
    {
        Notice::instance()->send($user, self::EVENT_SERVICE_REPLY_COMMENT);
    }

    /**
     * 事件订阅
     * @access public
     * @param Event $event 事件对象
     */
    public function subscribe(Event $event)
    {
        // 咨询被客服回复
        $event->listen('ServiceReplyConsult', [$this, 'onServiceReplyConsult']);
        // 评价被顾客咨询
        $event->listen('CustomerAdvisoryComment', [$this, 'onCustomerAdvisoryComment']);
        // 评价被客服回复
        $event->listen('ServiceReplyComment', [$this, 'onServiceReplyComment']);
    }
}
