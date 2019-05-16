<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    区域模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/3/27
 */

namespace app\common\validate;

class Region extends CareyShop
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'region_id'   => 'integer|gt:0',
        'parent_id'   => 'require|integer|egt:0',
        'region_name' => 'require|max:120',
        'sort'        => 'integer|between:0,255',
        'is_delete'   => 'in:0,1',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'region_id'   => '区域编号',
        'parent_id'   => '父区域编号',
        'region_name' => '区域名称',
        'sort'        => '区域排序值',
        'is_delete'   => '是否已删除',
    ];

    /**
     * 场景规则
     * @var array
     */
    protected $scene = [
        'set'   => [
            'region_id' => 'require|integer|gt:0',
            'region_name',
            'sort',
            'is_delete',
        ],
        'del'   => [
            'region_id' => 'require|arrayHasOnlyInts',
            'is_delete' => 'require|in:0,1',
        ],
        'item'  => [
            'region_id' => 'require|integer|gt:0',
        ],
        'list'  => [
            'region_id' => 'integer|egt:0',
            'is_delete',
        ],
        'sort'  => [
            'region_id' => 'require|integer|gt:0',
            'sort'      => 'require|integer|between:0,255',
        ],
        'index' => [
            'region_id' => 'require|arrayHasOnlyInts',
        ],
        'name'  => [
            'region_id' => 'require|arrayHasOnlyInts',
        ],
    ];
}
