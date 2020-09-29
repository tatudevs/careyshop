<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品规格展现方式验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/4/21
 */

namespace app\common\validate;

class SpecImage extends CareyShop
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'goods_id'     => 'require|integer|gt:0',
        'spec_item_id' => 'require|integer|gt:0',
        'spec_type'    => 'require|in:1,2',
        'image'        => 'array',
        'color'        => 'max:50',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'goods_id'     => '商品规格中的商品编号',
        'spec_item_id' => '商品规格中的商品规格项编号',
        'spec_type'    => '商品规格中的规格展现方式',
        'image'        => '商品规格中的规格图片',
        'color'        => '商品规格中的规格颜色',
    ];
}
