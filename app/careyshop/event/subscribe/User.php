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

use app\careyshop\event\service\notice\Notice;
use think\Event;

class User extends Base
{
    /**
     * 账号登录事件触发
     * @access public
     * @param array $user 账号数据
     */
    public function onUserLogin(array $user)
    {
        Notice::instance()->send($user, self::EVENT_USER_LOGIN);
    }

    /**
     * 账号注册事件触发
     * @access public
     * @param array $user 账号数据
     */
    public function onUserRegister(array $user)
    {
        Notice::instance()->send($user, self::EVENT_USER_REGISTER);
    }

    /**
     * 修改密码事件触发
     * @access public
     * @param array $user 账号数据
     */
    public function onChangePassword(array $user)
    {
        Notice::instance()->send($user, self::EVENT_CHANGE_PASSWORD);
    }

    /**
     * 余额增加事件触发
     * @access public
     * @param array $money 资金变动数据
     */
    public function onIncBalance(array $money)
    {
        Notice::instance()->send($money, self::EVENT_INC_BALANCE);
    }

    /**
     * 余额减少事件触发
     * @access public
     * @param array $money 资金变动数据
     */
    public function onDecBalance(array $money)
    {
        Notice::instance()->send($money, self::EVENT_DEC_BALANCE);
    }

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
