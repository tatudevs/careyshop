<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    客服管理服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/26
 */

namespace app\careyshop\wechat\service;

class Service extends CareyShop
{
    /**
     * 添加一名客服
     * @access public
     * @return bool
     * @throws
     */
    public function addServiceItem(): bool
    {
        $account = $this->params['kf_account'];
        $nickname = $this->params['nickname'];

        $result = $this->getApp('customer_service')->create($account, $nickname);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }

    /**
     * 编辑一名客服
     * @access public
     * @return bool
     * @throws
     */
    public function setServiceItem(): bool
    {
        $account = $this->params['kf_account'];
        $nickname = $this->params['nickname'];

        $result = $this->getApp('customer_service')->update($account, $nickname);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }

    /**
     * 删除一名客户
     * @access public
     * @return bool
     * @throws
     */
    public function delServiceItem(): bool
    {
        $result = $this->getApp('customer_service')->delete($this->params['kf_account']);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }

    /**
     * 编辑客服头像
     * @access public
     * @return bool
     * @throws
     */
    public function setServiceAvatar(): bool
    {
        $account = $this->params['kf_account'];
        $avatarPath = $this->getUploadFile('image');

        if (false === $avatarPath) {
            return false;
        }

        $result = $this->getApp('customer_service')->setAvatar($account, $avatarPath);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }

    /**
     * 获取全部客服列表
     * @access public
     * @return array|false
     * @throws
     */
    public function getServiceList()
    {
        $result = $this->getApp('customer_service')->list();
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result['kf_list'];
    }

    /**
     * 获取在线客服列表
     * @access public
     * @return array|false
     * @throws
     */
    public function getServiceOnline()
    {
        $result = $this->getApp('customer_service')->online();
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result['kf_online_list'];
    }

    /**
     * 邀请微信用户绑定客服帐号
     * @access public
     * @return bool
     * @throws
     */
    public function setServiceInvite(): bool
    {
        $account = $this->params['kf_account'];
        $invite = $this->params['invite_wx'];

        $result = $this->getApp('customer_service')->invite($account, $invite);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }

    /**
     * 获取客服与客户聊天记录
     * @access public
     * @return array|false
     * @throws
     */
    public function getServiceMessage()
    {
        $starttime = $this->params['starttime'];
        $endtime = $this->params['endtime'];

        $msgId = $this->params['msgid'] ?? 1;
        $number = $this->params['number'] ?? 100;

        $result = $this->getApp('customer_service')->messages($starttime, $endtime, $msgId, $number);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result;
    }

    /**
     * 主动发送消息给公众号用户
     * @access public
     * @return bool
     * @throws
     */
    public function sendMessageToUser(): bool
    {
        $message = $this->params['message'];
        $openId = $this->params['openid'];

        $result = $this->getApp('customer_service')
            ->message($message)
            ->to($openId)
            ->send();

        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }

    /**
     * 使用指定客服发送消息给公众号用户
     * @access public
     * @return bool
     * @throws
     */
    public function sendMessageFromUser(): bool
    {
        $message = $this->params['message'];
        $account = $this->params['kf_account'];
        $openId = $this->params['openid'];

        $result = $this->getApp('customer_service')
            ->message($message)
            ->from($account)
            ->to($openId)
            ->send();

        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }

    /**
     * 创建一个客服会话
     * @access public
     * @return bool
     * @throws
     */
    public function addSessionItem(): bool
    {
        $account = $this->params['kf_account'];
        $openId = $this->params['openid'];

        $result = $this->getApp('customer_service_session')->create($account, $openId);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }

    /**
     * 关闭一个客服会话
     * @access public
     * @return bool
     * @throws
     */
    public function closeSessionItem(): bool
    {
        $account = $this->params['kf_account'];
        $openId = $this->params['openid'];

        $result = $this->getApp('customer_service_session')->close($account, $openId);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }

    /**
     * 获取公众号用户会话状态
     * @access public
     * @return array|false
     * @throws
     */
    public function getSessionUser()
    {
        $result = $this->getApp('customer_service_session')->get($this->params['openid']);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result;
    }

    /**
     * 根据客服账号获取会话列表
     * @access public
     * @return array|false
     * @throws
     */
    public function getSessionList()
    {
        $result = $this->getApp('customer_service_session')->list($this->params['kf_account']);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result['sessionlist'];
    }

    /**
     * 获取未接入会话列表
     * @access public
     * @return array|false
     * @throws
     */
    public function getSessionWaiting()
    {
        $result = $this->getApp('customer_service_session')->waiting();
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result;
    }
}
