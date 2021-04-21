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

use think\Event;

class Ask
{
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