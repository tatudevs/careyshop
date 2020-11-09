<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    Wechat 控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/20
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;
use app\common\wechat\service\Material;
use app\common\wechat\service\Reply;
use app\common\wechat\service\DataCube;
use app\common\wechat\service\Menu;
use app\common\wechat\service\Message;
use app\common\wechat\service\Server;
use app\common\wechat\service\Service;
use app\common\wechat\service\Template;
use app\common\wechat\service\User;
use app\common\wechat\service\UserTag;

class Wechat extends CareyShop
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
        self::getMenuMethod();
        self::getAutoReplyMethod();
        self::getTemplateMethod();
        self::getMaterialMethod();
        self::getMessageMethod();
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
            'put.wechat.data'    => ['putWechatData', Server::class],
            // 长链接转短链接
            'set.wechat.short'   => ['setWechatShort', Server::class],
            // 获取微信服务器IP(或IP段)
            'get.wechat.ip'      => ['getWechatIP', Server::class],
            // 清理接口调用次数(每月10次)
            'clear.wechat.quota' => ['clearWechatQuota', Server::class],
            'test' => ['handleText', Server::class]
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

    /**
     * 自定义菜单
     * @access private
     * @return void
     */
    private static function getMenuMethod()
    {
        self::$route = array_merge(self::$route, [
            // 编辑自定义菜单(配置数据为空时表示全部删除)
            'set.official_account.menu.data' => ['setMenuData', Menu::class],
            // 获取自定义菜单
            'get.official_account.menu.data' => ['getMenuData', Menu::class],
            // 删除全部自定义菜单
            'del.official_account.menu.all'  => ['delMenuAll', Menu::class],
        ]);
    }

    /**
     * 自动回复
     * @access private
     * @return void
     */
    private static function getAutoReplyMethod()
    {
        self::$route = array_merge(self::$route, [
            // 获取自动回复配置
            'get.official_account.reply.data' => ['getReplyData', Reply::class],
            // 设置自动回复配置
            'set.official_account.reply.data' => ['setReplyData', Reply::class],
        ]);
    }

    /**
     * 模板消息
     * @access private
     * @return void
     */
    private static function getTemplateMethod()
    {
        self::$route = array_merge(self::$route, [
            // 获取行业信息列表
            'get.official_account.template.id'       => ['getIndustryId', Template::class],
            // 获取已设置的行业信息
            'get.official_account.template.industry' => ['getIndustry', Template::class],
            // 编辑行业信息
            'set.official_account.template.industry' => ['setIndustry', Template::class],
            // 使用公众号的模板库添加至模板列表
            'add.official_account.template.item'     => ['addTemplateItem', Template::class],
            // 获取已添加的所有模板列表
            'get.official_account.template.list'     => ['getTemplateList', Template::class],
            // 删除一个指定模板
            'del.official_account.template.item'     => ['delTemplateItem', Template::class],
        ]);
    }

    /**
     * 素材管理
     * @access private
     * @return void
     */
    private static function getMaterialMethod()
    {
        self::$route = array_merge(self::$route, [
            // 上传临时素材(有效期3天)
            'add.official_account.media.item'     => ['addMediaItem', Material::class],
            // 获取临时素材
            'get.official_account.media.item'     => ['getMediaItem', Material::class],
            // 上传永久素材
            'add.official_account.material.item'  => ['addMaterialItem', Material::class],
            // 获取永久素材
            'get.official_account.material.item'  => ['getMaterialItem', Material::class],
            // 获取永久素材列表
            'get.official_account.material.list'  => ['getMaterialList', Material::class],
            // 上传永久图文素材
            'add.official_account.material.news'  => ['addMaterialNews', Material::class],
            // 编辑永久图文素材
            'set.official_account.material.news'  => ['setMaterialNews', Material::class],
            // 上传永久图文消息图片
            'add.official_account.material.image' => ['addMaterialImage', Material::class],
            // 获取永久素材的总数
            'get.official_account.material.count' => ['getMaterialCount', Material::class],
            // 删除一个永久素材
            'del.official_account.material.item'  => ['delMaterialItem', Material::class],
            // 发送图文素材的预览
            'send.official_account.material.view' => ['sendMaterialView', Material::class],
        ]);
    }

    /**
     * 消息群发
     * @access private
     * @return void
     */
    private static function getMessageMethod()
    {
        self::$route = array_merge(self::$route, [
            // 发送一条群发消息
            'send.official_account.message.item'  => ['sendMessageItem', Message::class],
            // 发送预览消息给指定的微信用户或粉丝
            'send.official_account.message.view'  => ['sendMessageView', Message::class],
            // 删除一条群发消息
            'del.official_account.message.item'   => ['delMessageItem', Message::class],
            // 查询群发消息发送状态
            'get.official_account.message.status' => ['getMessageStatus', Message::class],
        ]);
    }
}
