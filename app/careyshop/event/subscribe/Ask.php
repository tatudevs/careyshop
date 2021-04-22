<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    问答事件订阅
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/20
 */

namespace app\careyshop\event\subscribe;

use app\careyshop\event\service\notice\Notice;
use think\Event;

class Ask extends Base
{
    /**
     * 提问被回复
     * @access public
     * @param array $ask 回复数据
     */
    public function onReplyAsk(array $ask)
    {
        Notice::instance()->send($ask, self::EVENT_REPLY_ASK);
    }

    /**
     * 事件订阅
     * @access public
     * @param Event $event 事件对象
     */
    public function subscribe(Event $event)
    {
        // 提问被回复
        $event->listen('ReplyAsk', [$this, 'onReplyAsk']);
    }
}
