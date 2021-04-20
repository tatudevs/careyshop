<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    账号管理事件订阅
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/20
 */

namespace app\careyshop\event\subscribe;

use think\Event;

class User
{
    /**
     * 事件订阅
     * @access public
     * @param Event $event 事件对象
     */
    public function subscribe(Event $event)
    {
        // 账号登录
        $event->listen('UserLogin', [$this, 'onUserLogin']);
        // 账号注册
        $event->listen('UserRegister', [$this, 'onUserRegister']);
        // 修改密码
        $event->listen('ChangePassword', [$this, 'onChangePassword']);
        // 余额增加
        $event->listen('IncBalance', [$this, 'onIncBalance']);
        // 余额减少
        $event->listen('DecBalance', [$this, 'onDecBalance']);
    }
}
