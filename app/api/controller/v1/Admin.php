<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    管理组账号控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2018/3/5
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;
use app\common\service\Admin as AdminService;

class Admin extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return void
     */
    protected static function initMethod()
    {
        self::$route = [
            // 添加一个账号
            'add.admin.item'       => ['addAdminItem'],
            // 编辑一个账号
            'set.admin.item'       => ['setAdminItem'],
            // 批量设置账号状态
            'set.admin.status'     => ['setAdminStatus'],
            // 修改一个账号密码
            'set.admin.password'   => ['setAdminPassword'],
            // 重置一个账号密码
            'reset.admin.item'     => ['resetAdminItem'],
            // 批量删除账号
            'del.admin.list'       => ['delAdminList'],
            // 获取一个账号
            'get.admin.item'       => ['getAdminItem'],
            // 获取账号列表
            'get.admin.list'       => ['getAdminList'],
            // 获取指定账号的基础数据
            'get.admin.select'     => ['getAdminSelect'],
            // 注销账号
            'logout.admin.user'    => ['logoutAdmin'],
            // 登录账号
            'login.admin.user'     => ['loginAdmin'],
            // 刷新Token
            'refresh.admin.token'  => ['refreshToken'],
            // 验证账号是否合法
            'check.admin.username' => ['checkAdminName', AdminService::class],
            // 验证账号昵称是否合法
            'check.admin.nickname' => ['checkAdminNick', AdminService::class],
        ];
    }
}
