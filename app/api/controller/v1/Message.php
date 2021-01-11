<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    消息控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/11/27
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;
use app\careyshop\model\MessageUser;

class Message extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return void
     */
    protected static function initMethod()
    {
        self::$route = [
            // 添加一条消息
            'add.message.item'         => ['addMessageItem'],
            // 编辑一条消息
            'set.message.item'         => ['setMessageItem'],
            // 批量删除消息
            'del.message.list'         => ['delMessageList'],
            // 批量正式发布消息
            'set.message.status'       => ['setMessageStatus'],
            // 获取一条消息(后台)
            'get.message.item'         => ['getMessageItem'],
            // 获取消息列表(后台)
            'get.message.list'         => ['getMessageList'],
            // 用户获取一条消息
            'get.message.user.item'    => ['getMessageUserItem'],
            // 用户获取消息列表
            'get.message.user.list'    => ['getMessageUserList'],
            // 用户获取未读消息数
            'get.message.user.unread'  => ['getMessageUserUnread'],
            // 用户批量设置消息已读
            'set.message.user.read'    => ['setMessageUserRead', MessageUser::class],
            // 用户设置消息全部已读
            'set.message.user.allread' => ['setMessageUserAllRead', MessageUser::class],
            // 用户批量删除消息
            'del.message.user.list'    => ['delMessageUserList', MessageUser::class],
            // 用户删除全部消息
            'del.message.user.all'     => ['delMessageUserAll', MessageUser::class],
        ];
    }
}
