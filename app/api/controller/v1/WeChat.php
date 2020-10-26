<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    WeChat 控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/20
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;
use app\common\wechat\service\Server;
use app\common\wechat\service\User;
use app\common\wechat\service\UserTag;

class WeChat extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return void
     */
    protected static function initMethod()
    {
        self::getServerMethod();
        self::getUserMethod();
        self::getUserTagMethod();
        self::getDataCubeMethod();
    }

    /**
     * 服务端
     * @access private
     * @return void
     */
    private static function getServerMethod()
    {
        self::$route = array_merge(self::$route, [
            // 接收并响应微信推送
            'put.wechat.data' => ['putWeChatData', Server::class],
        ]);
    }

    /**
     * 用户管理
     * @access private
     * @return void
     */
    private static function getUserMethod()
    {
        self::$route = array_merge(self::$route, [
            // 同步公众号用户
            'get.official_account.user.sync'      => ['getUserSync', User::class],
            // 获取公众号订阅渠道来源
            'get.official_account.user.subscribe' => ['getSubscribeScene', User::class],
            // 获取一个公众号用户
            'get.official_account.user.item'      => ['getUserItem', User::class],
            // 获取公众号用户列表
            'get.official_account.user.list'      => ['getUserList', User::class],
            // 设置公众号用户的备注
            'set.official_account.user.remark'    => ['setUserRemark', User::class],
            // 同步公众号黑名单
            'get.official_account.black.sync'     => ['getBlackSync', User::class],
            // 获取公众号黑名单列表
            'get.official_account.black.list'     => ['getBlackList', User::class],
            // 拉黑公众号用户
            'get.official_account.black.block'    => ['getBlackBlock', User::class],
            // 取消公众号拉黑用户
            'get.official_account.black.unblock'  => ['getBlackUnblock', User::class],
        ]);
    }

    /**
     * 用户标签
     * @access private
     * @return void
     */
    private static function getUserTagMethod()
    {
        self::$route = array_merge(self::$route, [
            // 添加一个公众号标签
            'add.official_account.tag.item' => ['addTagItem', UserTag::class],
            // 编辑一个公众号标签
            'set.official_account.tag.item' => ['setTagItem', UserTag::class],
            // 删除一个公众号标签
            'del.official_account.tag.item' => ['delTagItem', UserTag::class],
            // 获取公众号标签列表
            'get.official_account.tag.list' => ['getTagList', UserTag::class],
            // 获取指定公众号用户下的所有标签
            'get.official_account.tag.user' => ['getTagOfUser', UserTag::class],
            // 批量为公众号用户添加标签
            'set.official_account.tag.user' => ['setTagToUser', UserTag::class],
            // 批量为公众号用户删除标签
            'del.official_account.tag.user' => ['delTagToUser', UserTag::class],
        ]);
    }

    /**
     * 数据统计
     * @access private
     * @return void
     */
    private static function getDataCubeMethod()
    {
        self::$route = array_merge(self::$route, [
        ]);
    }
}
