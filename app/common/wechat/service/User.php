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

        $cacheKey = 'WeChatUser' . $this->params['code'];
        Cache::set($cacheKey, array_reverse($openIdList));

        return true;
    }

    public function getUserList()
    {
    }
}
