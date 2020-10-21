<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    消息服务端服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/20
 */

namespace app\common\wechat\service;

use app\common\wechat\WeChatApp;

class Server extends CareyShop
{
    public function putWeChatData(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        $wechat = new WeChatApp($data['code']);
        return $wechat->app->user->list();
    }
}
