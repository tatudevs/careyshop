<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    用户管理服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/20
 */

namespace app\common\wechat\service;

use think\facade\Cache;

class User extends CareyShop
{
    /**
     * 同步公众号用户
     * @access public
     * @return bool
     * @throws
     */
    public function getUserSync()
    {
        $openIdList = [];
        $nextOpenId = null;

        while (true) {
            $result = $this->getApp('user')->list($nextOpenId);
            if (isset($result['errcode']) && $result['errcode'] != 0) {
                return $this->setError($result['errmsg']);
            }

            $nextOpenId = $result['next_openid'];
            $openIdList = array_merge($openIdList, $result['data']['openid']);

            if ($result['count'] < 10000) {
                break;
            }
        }

        $cacheKey = self::WECHAT_USER . $this->params['code'];
        Cache::set($cacheKey, array_reverse($openIdList));

        return true;
    }

    /**
     * 获取公众号订阅渠道来源
     * @access public
     * @return string[]
     */
    public function getSubscribeScene()
    {
        return [
            'ADD_SCENE_SEARCH'               => '公众号搜索',
            'ADD_SCENE_ACCOUNT_MIGRATION'    => '公众号迁移',
            'ADD_SCENE_PROFILE_CARD'         => '名片分享',
            'ADD_SCENE_QR_CODE'              => '扫描二维码',
            'ADD_SCENE_PROFILE_LINK'         => '图文页内名称点击',
            'ADD_SCENE_PROFILE_ITEM'         => '图文页右上角菜单',
            'ADD_SCENE_PAID'                 => '支付后关注',
            'ADD_SCENE_WECHAT_ADVERTISEMENT' => '微信广告',
            'ADD_SCENE_OTHERS'               => '其他',
        ];
    }

    /**
     * 获取一个公众号用户
     * @access public
     * @return array|false
     * @throws
     */
    public function getUserItem()
    {
        $result = $this->getApp('user')->get($this->params['open_id']);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result;
    }

    /**
     * 获取公众号用户列表
     * @access public
     * @return array|false
     * @throws
     */
    public function getUserList()
    {
        // 数据准备
        [$pageNo, $pageSize] = $this->getPageData(100);
        $cacheData = Cache::get(self::WECHAT_USER . $this->params['code'], []);

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
}
