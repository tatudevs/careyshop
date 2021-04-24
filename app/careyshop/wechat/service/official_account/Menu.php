<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    自定义菜单服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/28
 */

namespace app\careyshop\wechat\service\official_account;

use app\careyshop\wechat\service\CareyShop;
use think\facade\Cache;

class Menu extends CareyShop
{
    /**
     * 公众号菜单缓存标识
     * $var string
     */
    const WECHAT_MENU = 'WechatMenu';

    /**
     * 编辑自定义菜单(配置数据为空时表示全部删除)
     * @access public
     * @return bool
     * @throws
     */
    public function setMenuData(): bool
    {
        $button = $this->params['button'];
        if (empty($button)) {
            return $this->delMenuAll();
        }

        $result = $this->getApp('menu')->create($button);
        $cacheKey = self::WECHAT_MENU . $this->params['code'];
        Cache::store('place')->set($cacheKey, true);

        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }

    /**
     * 获取自定义菜单
     * @access public
     * @return array|false
     * @throws
     */
    public function getMenuData()
    {
        $cacheKey = self::WECHAT_MENU . $this->params['code'];
        $result = Cache::store('place')->get($cacheKey);

        if (false === $result) {
            return [];
        }

        $result = $this->getApp('menu')->current();
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result;
    }

    /**
     * 删除全部自定义菜单
     * @access public
     * @return bool
     * @throws
     */
    public function delMenuAll(): bool
    {
        $result = $this->getApp('menu')->delete();
        $cacheKey = self::WECHAT_MENU . $this->params['code'];
        Cache::store('place')->set($cacheKey, false);

        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }
}
