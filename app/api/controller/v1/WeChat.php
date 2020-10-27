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
use app\common\wechat\service\DataCube;
use app\common\wechat\service\Server;
use app\common\wechat\service\Service;
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
        self::getServiceMethod();
        self::getSessionMethod();
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
            'put.wechat.data'  => ['putWeChatData', Server::class],
            // 长链接转短链接
            'set.wechat.short' => ['setWeChatShort', Server::class],
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
            'get.official_account.user.sync'     => ['getUserSync', User::class],
            // 获取一个公众号用户
            'get.official_account.user.item'     => ['getUserItem', User::class],
            // 获取公众号用户列表
            'get.official_account.user.list'     => ['getUserList', User::class],
            // 设置公众号用户的备注
            'set.official_account.user.remark'   => ['setUserRemark', User::class],
            // 同步公众号黑名单
            'get.official_account.black.sync'    => ['getBlackSync', User::class],
            // 获取公众号黑名单列表
            'get.official_account.black.list'    => ['getBlackList', User::class],
            // 拉黑公众号用户
            'get.official_account.black.block'   => ['getBlackBlock', User::class],
            // 取消公众号拉黑用户
            'get.official_account.black.unblock' => ['getBlackUnblock', User::class],
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
            // 获取公众号数据统计分析
            'del.official_account.datacube' => ['getDataCube', DataCube::class],
        ]);
    }

    /**
     * 客服管理
     * @access private
     * @return void
     */
    private static function getServiceMethod()
    {
        self::$route = array_merge(self::$route, [
            // 添加一名客服
            'add.official_account.service.item'    => ['addServiceItem', Service::class],
            // 编辑一名客服
            'set.official_account.service.item'    => ['setServiceItem', Service::class],
            // 删除一名客户
            'del.official_account.service.item'    => ['delServiceItem', Service::class],
            // 编辑客服头像
            'set.official_account.service.avatar'  => ['setServiceAvatar', Service::class],
            // 获取全部客服列表
            'get.official_account.service.list'    => ['getServiceList', Service::class],
            // 获取在线客服列表
            'get.official_account.service.online'  => ['getServiceOnline', Service::class],
            // 邀请微信用户绑定客服帐号
            'set.official_account.service.invite'  => ['setServiceInvite', Service::class],
            // 获取客服与客户聊天记录
            'get.official_account.service.message' => ['getServiceMessage', Service::class],
            // 主动发送消息给公众号用户
            'send.official_account.service.user'   => ['sendMessageToUser', Service::class],
            // 使用指定客服发送消息给公众号用户
            'send.official_account.service.from'   => ['sendMessageFromUser', Service::class],
        ]);
    }

    /**
     * 客服会话
     * @access private
     * @return void
     */
    private static function getSessionMethod()
    {
        self::$route = array_merge(self::$route, [
            // 创建一个客服会话
            'add.official_account.session.item'    => ['addSessionItem', Service::class],
            // 关闭一个客服会话
            'close.official_account.session.item'  => ['closeSessionItem', Service::class],
            // 获取公众号用户会话状态
            'get.official_account.session.user'    => ['getSessionUser', Service::class],
            // 根据客服账号获取会话列表
            'get.official_account.session.list'    => ['getSessionList', Service::class],
            // 获取未接入会话列表
            'get.official_account.session.waiting' => ['getSessionWaiting', Service::class],
        ]);
    }
}
