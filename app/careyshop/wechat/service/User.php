<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    用户管理服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/20
 */

namespace app\careyshop\wechat\service;

use think\facade\Cache;

class User extends CareyShop
{
    /**
     * 公众号用户缓存标识
     * @var string
     */
    const WECHAT_USER = 'WechatUser';

    /**
     * 公众号黑名单缓存标识
     * @var string
     */
    const WECHAT_BLACK = 'WechatBlack';

    /**
     * 同步公众号用户
     * @access public
     * @return bool
     * @throws
     */
    public function getUserSync(): bool
    {
        $openIdList = [];
        $nextOpenId = null;

        while (true) {
            $result = $this->getApp('user')->list($nextOpenId);
            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return $this->setError($result['errmsg']);
            }

            $nextOpenId = $result['next_openid'];
            if ($result['count'] > 0) {
                $openIdList = [...$openIdList, ...$result['data']['openid']];
            }

            if ($result['count'] < 10000) {
                break;
            }
        }

        $cacheKey = self::WECHAT_USER . $this->params['code'];
        Cache::store('place')->set($cacheKey, array_reverse($openIdList));

        return true;
    }

    /**
     * 获取一个公众号用户
     * @access public
     * @return array|false
     * @throws
     */
    public function getUserItem()
    {
        $result = $this->getApp('user')->get($this->params['openid']);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result;
    }

    /**
     * 获取微信用户详情列表
     * @access public
     * @param string $name 缓存名
     * @return array|false
     * @throws
     */
    private function getWechatUserList(string $name)
    {
        // 数据准备
        [$pageNo, $pageSize] = $this->getPageData(100);
        $cacheData = Cache::store('place')->get($name . $this->params['code'], []);

        // 计算合计
        $data['total_result'] = count($cacheData);
        if ($data['total_result'] <= 0) {
            return $data;
        }

        $openIdList = array_slice($cacheData, $pageNo * $pageSize, $pageSize);
        $result = $this->getApp('user')->select($openIdList);

        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        $data['items'] = $result['user_info_list'];
        return $data;
    }

    /**
     * 获取公众号用户列表
     * @access public
     * @return array|false
     */
    public function getUserList()
    {
        return $this->getWechatUserList(self::WECHAT_USER);
    }

    /**
     * 设置公众号用户的备注
     * @access public
     * @return bool
     * @throws
     */
    public function setUserRemark(): bool
    {
        // 数据准备
        $openId = $this->params['openid'];
        $remark = $this->params['remark'];

        // 发送请求
        $result = $this->getApp('user')->remark($openId, $remark);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }

    /**
     * 同步公众号黑名单
     * @access public
     * @return bool
     * @throws
     */
    public function getBlackSync(): bool
    {
        $openIdList = [];
        $beginOpenId = null;

        while (true) {
            $result = $this->getApp('user')->blacklist($beginOpenId);
            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return $this->setError($result['errmsg']);
            }

            if (isset($result['next_openid'])) {
                $beginOpenId = $result['next_openid'];
            }

            if ($result['count'] > 0) {
                $openIdList = [...$openIdList, ...$result['data']['openid']];
            }

            if ($result['count'] < 10000) {
                break;
            }
        }

        $cacheKey = self::WECHAT_BLACK . $this->params['code'];
        Cache::store('place')->set($cacheKey, array_reverse($openIdList));

        return true;
    }

    /**
     * 获取公众号黑名单列表
     * @access public
     * @return array|false
     */
    public function getBlackList()
    {
        return $this->getWechatUserList(self::WECHAT_BLACK);
    }

    /**
     * 拉黑公众号用户
     * @access public
     * @return bool
     * @throws
     */
    public function getBlackBlock(): bool
    {
        $result = $this->getApp('user')->block($this->params['openid_list']);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        $this->setBlackCache('add');
        return true;
    }

    /**
     * 取消公众号拉黑用户
     * @access public
     * @return bool
     * @throws
     */
    public function getBlackUnblock(): bool
    {
        $result = $this->getApp('user')->unblock($this->params['openid_list']);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        $this->setBlackCache('del');
        return true;
    }

    /**
     * 设置公众号黑名单缓存
     * @access private
     * @param string $type 添加或删除(add del)
     * @return void
     * @throws
     */
    private function setBlackCache(string $type)
    {
        if (is_string($this->params['openid_list'])) {
            $this->params['openid_list'] = [$this->params['openid_list']];
        }

        $cacheKey = self::WECHAT_BLACK . $this->params['code'];
        $cacheData = Cache::store('place')->get($cacheKey, []);

        if ('add' === $type) {
            foreach ($this->params['openid_list'] as $vlaue) {
                if (!in_array($vlaue, $cacheData)) {
                    array_unshift($cacheData, $vlaue);
                }
            }
        } else if ('del' === $type) {
            foreach ($this->params['openid_list'] as $vlaue) {
                $pos = array_search($vlaue, $cacheData);
                if (false !== $pos) {
                    unset($cacheData[$pos]);
                }
            }
        }

        Cache::store('place')->set($cacheKey, $cacheData);
    }
}
