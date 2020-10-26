<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    客服管理服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/26
 */

namespace app\common\wechat\service;

class Service extends CareyShop
{
    /**
     * 添加一名客服
     * @access public
     * @return bool
     * @throws
     */
    public function addServiceItem()
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
    public function setServiceItem()
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
    public function delServiceItem()
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
    public function setServiceAvatar()
    {
        $account = $this->params['kf_account'];
        $avatarPath = $this->params['avatar_path'];

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
}
