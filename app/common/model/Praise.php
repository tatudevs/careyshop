<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    点赞记录模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/3
 */

namespace app\common\model;

class Praise extends CareyShop
{
    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'praise_id',
    ];
}
