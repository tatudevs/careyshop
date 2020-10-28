<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    自定义菜单服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/28
 */

namespace app\common\wechat\service;

use think\facade\Cache;

class Menu extends CareyShop
{
    public function setMenuData()
    {
        $button = $this->params['button'];
        if (empty($button)) {
            return $this->delMenuAll();
        }

        $result = $this->getApp('menu')->create($button);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }

    public function getMenuData()
    {
        $cacheName = self::WECHAT_MENU . $this->params['code'];
        Cache::remember($cacheName, function () {
        });

//        $result = $this->getApp('menu')->list();
//        if (isset($result['errcode']) && $result['errcode'] == 46003) {
//            return []; // 菜单尚未创建
//        }

        $result = $this->getApp('menu')->current();
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return $result;
    }

    public function delMenuAll()
    {
        $result = $this->getApp('menu')->delete();
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            return $this->setError($result['errmsg']);
        }

        return true;
    }
}
