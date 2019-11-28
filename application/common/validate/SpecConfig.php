<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品规格配置验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2019/11/28
 */

namespace app\common\validate;

class SpecConfig extends CareyShop
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'spec_config_id' => 'integer|gt:0',
        'goods_id'       => 'require|integer|gt:0',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'spec_config_id' => '商品规格配置编号',
        'goods_id'       => '商品编号',
    ];
}
