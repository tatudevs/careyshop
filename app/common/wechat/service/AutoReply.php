<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    自动回复服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/29
 */

namespace app\common\wechat\service;

class AutoReply extends CareyShop
{
    public function getReplyData()
    {
        return $this->getApp('auto_reply')->current();
    }
}
