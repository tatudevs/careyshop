<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    素材管理服务层
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/30
 */

namespace app\common\wechat\service;

class Material extends CareyShop
{
    public function test()
    {
        return $this->getUploadFile($this->params['type']);
    }
}
