<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    账号管理控制
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/3/31
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;
use app\careyshop\service\User as UserService;

class User extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return void
     */
    protected static function initMethod()
    {
        self::$route = [
            // 注册一个新账号
            'add.user.item'       => ['addUserItem'],
            // 编辑一个账号
            'set.user.item'       => ['setUserItem'],
            // 批量设置账号状态
            'set.user.status'     => ['setUserStatus'],
            // 修改一个账号密码
            'set.user.password'   => ['setUserPassword'],
            // 批量删除账号
            'del.user.list'       => ['delUserList'],
            // 获取一个账号
            'get.user.item'       => ['getUserItem'],
            // 获取一个账号的简易信息
            'get.user.info'       => ['getUserInfo'],
            // 获取账号列表
            'get.user.list'       => ['getUserList'],
            // 获取指定账号的基础数据
            'get.user.select'     => ['getUserSelect'],
            // 注销账号
            'logout.user.user'    => ['logoutUser'],
            // 登录账号
            'login.user.user'     => ['loginUser'],
            // 刷新Token
            'refresh.user.token'  => ['refreshToken'],
            // 忘记密码
            'find.user.password'  => ['findUserPassword'],
            // 重新绑定手机或邮箱
            'set.user.bind'       => ['setUserBind'],
            // 验证账号是否合法
            'check.user.username' => ['checkUserName', UserService::class],
            // 验证账号手机是否合法
            'check.user.mobile'   => ['checkUserMobile', UserService::class],
        ];
    }
}
