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
use app\careyshop\wechat\service\Server;
use app\careyshop\wechat\service\official_account\{
    User as OfficialAccountUser,
    UserTag as OfficialAccountUserTag,
    DataCube as OfficialAccountDataCube,
    Service as OfficialAccountService,
    Menu as OfficialAccountMenu,
    Reply as OfficialAccountReply,
    Template as OfficialAccountTemplate,
    Material as OfficialAccountMaterial,
    Message as OfficialAccountMessage,
};

class Wechat extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return void
     */
    protected static function initMethod()
    {
        // 微信-服务端
        self::$route = [
            // 接收并响应微信推送
            'put.wechat.data'    => ['putWechatData', Server::class],
            // 长链接转短链接
            'set.wechat.short'   => ['setWechatShort', Server::class],
            // 获取微信服务器IP(或IP段)
            'get.wechat.ip'      => ['getWechatIP', Server::class],
            // 清理接口调用次数(每月10次)
            'clear.wechat.quota' => ['clearWechatQuota', Server::class],
        ];

        // 加载微信公众号方法路由器
        self::loadOfficialAccount();

        // 加载微信小程序方法路由器
        self::loadMiniProgram();
    }

    /**
     * 微信公众号方法路由器
     * @access private
     * @return void
     */
    private static function loadOfficialAccount()
    {
        // 微信公众号-用户管理
        $user = [
            // 同步公众号用户
            'get.official_account.user.sync'     => ['getUserSync', OfficialAccountUser::class],
            // 获取一个公众号用户
            'get.official_account.user.item'     => ['getUserItem', OfficialAccountUser::class],
            // 获取公众号用户列表
            'get.official_account.user.list'     => ['getUserList', OfficialAccountUser::class],
            // 设置公众号用户的备注
            'set.official_account.user.remark'   => ['setUserRemark', OfficialAccountUser::class],
            // 同步公众号黑名单
            'get.official_account.black.sync'    => ['getBlackSync', OfficialAccountUser::class],
            // 获取公众号黑名单列表
            'get.official_account.black.list'    => ['getBlackList', OfficialAccountUser::class],
            // 拉黑公众号用户
            'get.official_account.black.block'   => ['getBlackBlock', OfficialAccountUser::class],
            // 取消公众号拉黑用户
            'get.official_account.black.unblock' => ['getBlackUnblock', OfficialAccountUser::class],
        ];

        // 微信公众号-用户标签
        $userTag = [
            // 添加一个公众号标签
            'add.official_account.tag.item' => ['addTagItem', OfficialAccountUserTag::class],
            // 编辑一个公众号标签
            'set.official_account.tag.item' => ['setTagItem', OfficialAccountUserTag::class],
            // 删除一个公众号标签
            'del.official_account.tag.item' => ['delTagItem', OfficialAccountUserTag::class],
            // 获取公众号标签列表
            'get.official_account.tag.list' => ['getTagList', OfficialAccountUserTag::class],
            // 获取指定公众号用户下的所有标签
            'get.official_account.tag.user' => ['getTagOfUser', OfficialAccountUserTag::class],
            // 批量为公众号用户添加标签
            'set.official_account.tag.user' => ['setTagToUser', OfficialAccountUserTag::class],
            // 批量为公众号用户删除标签
            'del.official_account.tag.user' => ['delTagToUser', OfficialAccountUserTag::class],
        ];

        // 微信公众号-数据统计
        $dataCube = [
            // 获取公众号数据统计分析
            'del.official_account.datacube' => ['getDataCube', OfficialAccountDataCube::class],
        ];

        // 微信公众号-客服管理
        $service = [
            // 添加一名客服
            'add.official_account.service.item'    => ['addServiceItem', OfficialAccountService::class],
            // 编辑一名客服
            'set.official_account.service.item'    => ['setServiceItem', OfficialAccountService::class],
            // 删除一名客户
            'del.official_account.service.item'    => ['delServiceItem', OfficialAccountService::class],
            // 编辑客服头像
            'set.official_account.service.avatar'  => ['setServiceAvatar', OfficialAccountService::class],
            // 获取全部客服列表
            'get.official_account.service.list'    => ['getServiceList', OfficialAccountService::class],
            // 获取在线客服列表
            'get.official_account.service.online'  => ['getServiceOnline', OfficialAccountService::class],
            // 邀请微信用户绑定客服帐号
            'set.official_account.service.invite'  => ['setServiceInvite', OfficialAccountService::class],
            // 获取客服与客户聊天记录
            'get.official_account.service.message' => ['getServiceMessage', OfficialAccountService::class],
            // 主动发送消息给公众号用户
            'send.official_account.service.user'   => ['sendMessageToUser', OfficialAccountService::class],
            // 使用指定客服发送消息给公众号用户
            'send.official_account.service.from'   => ['sendMessageFromUser', OfficialAccountService::class],
        ];

        // 微信公众号-客服会话
        $session = [
            // 创建一个客服会话
            'add.official_account.session.item'    => ['addSessionItem', OfficialAccountService::class],
            // 关闭一个客服会话
            'close.official_account.session.item'  => ['closeSessionItem', OfficialAccountService::class],
            // 获取公众号用户会话状态
            'get.official_account.session.user'    => ['getSessionUser', OfficialAccountService::class],
            // 根据客服账号获取会话列表
            'get.official_account.session.list'    => ['getSessionList', OfficialAccountService::class],
            // 获取未接入会话列表
            'get.official_account.session.waiting' => ['getSessionWaiting', OfficialAccountService::class],
        ];

        // 微信公众号-自定义菜单
        $menu = [
            // 编辑自定义菜单(配置数据为空时表示全部删除)
            'set.official_account.menu.data' => ['setMenuData', OfficialAccountMenu::class],
            // 获取自定义菜单
            'get.official_account.menu.data' => ['getMenuData', OfficialAccountMenu::class],
            // 删除全部自定义菜单
            'del.official_account.menu.all'  => ['delMenuAll', OfficialAccountMenu::class],
        ];

        // 微信公众号-自动回复
        $autoReply = [
            // 获取自动回复配置
            'get.official_account.reply.data' => ['getReplyData', OfficialAccountReply::class],
            // 设置自动回复配置
            'set.official_account.reply.data' => ['setReplyData', OfficialAccountReply::class],
        ];

        // 微信公众号-模板消息
        $template = [
            // 获取行业信息列表
            'get.official_account.template.id'       => ['getIndustryId', OfficialAccountTemplate::class],
            // 获取已设置的行业信息
            'get.official_account.template.industry' => ['getIndustry', OfficialAccountTemplate::class],
            // 编辑行业信息
            'set.official_account.template.industry' => ['setIndustry', OfficialAccountTemplate::class],
            // 使用公众号的模板库添加至模板列表
            'add.official_account.template.item'     => ['addTemplateItem', OfficialAccountTemplate::class],
            // 获取已添加的所有模板列表
            'get.official_account.template.list'     => ['getTemplateList', OfficialAccountTemplate::class],
            // 删除一个指定模板
            'del.official_account.template.item'     => ['delTemplateItem', OfficialAccountTemplate::class],
            // 发送消息模板
            'send.official_account.template.item'    => ['sendTemplateItem', OfficialAccountTemplate::class],
        ];

        // 微信公众号-素材管理
        $material = [
            // 上传临时素材(有效期3天)
            'add.official_account.media.item'     => ['addMediaItem', OfficialAccountMaterial::class],
            // 获取临时素材
            'get.official_account.media.item'     => ['getMediaItem', OfficialAccountMaterial::class],
            // 上传永久素材
            'add.official_account.material.item'  => ['addMaterialItem', OfficialAccountMaterial::class],
            // 获取永久素材
            'get.official_account.material.item'  => ['getMaterialItem', OfficialAccountMaterial::class],
            // 获取永久素材列表
            'get.official_account.material.list'  => ['getMaterialList', OfficialAccountMaterial::class],
            // 上传永久图文素材
            'add.official_account.material.news'  => ['addMaterialNews', OfficialAccountMaterial::class],
            // 编辑永久图文素材
            'set.official_account.material.news'  => ['setMaterialNews', OfficialAccountMaterial::class],
            // 上传永久图文消息图片
            'add.official_account.material.image' => ['addMaterialImage', OfficialAccountMaterial::class],
            // 获取永久素材的总数
            'get.official_account.material.count' => ['getMaterialCount', OfficialAccountMaterial::class],
            // 删除一个永久素材
            'del.official_account.material.item'  => ['delMaterialItem', OfficialAccountMaterial::class],
            // 发送图文素材的预览
            'send.official_account.material.view' => ['sendMaterialView', OfficialAccountMaterial::class],
        ];

        // 微信公众号-消息群发
        $message = [
            // 发送一条群发消息
            'send.official_account.message.item'  => ['sendMessageItem', OfficialAccountMessage::class],
            // 发送预览消息给指定的微信用户或粉丝
            'send.official_account.message.view'  => ['sendMessageView', OfficialAccountMessage::class],
            // 删除一条群发消息
            'del.official_account.message.item'   => ['delMessageItem', OfficialAccountMessage::class],
            // 查询群发消息发送状态
            'get.official_account.message.status' => ['getMessageStatus', OfficialAccountMessage::class],
        ];

        // 微信公众号-方法合并
        self::$route = array_merge(
            self::$route,
            $user,
            $userTag,
            $dataCube,
            $service,
            $session,
            $menu,
            $autoReply,
            $template,
            $material,
            $message
        );
    }

    /**
     * 微信小程序方法路由器
     * @access private
     * @return void
     */
    private static function loadMiniProgram()
    {
        // 方法占位
    }
}
