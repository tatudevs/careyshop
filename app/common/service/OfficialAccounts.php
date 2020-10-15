<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    公众号服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/13
 */

namespace app\common\service;

class OfficialAccounts extends CareyShop
{
    /**
     * 获取框架已支持的公众号平台
     * @access public
     * @return mixed
     */
    public function getOfficialPlatform()
    {
        return config('extra.official_group');
    }
}
