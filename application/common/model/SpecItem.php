<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品规格项模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/4/18
 */

namespace app\common\model;

class SpecItem extends CareyShop
{
    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'spec_item_id',
        'spec_id',
    ];

    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'spec_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'spec_item_id' => 'integer',
        'spec_id'      => 'integer',
        'is_type'      => 'integer',
    ];
}
