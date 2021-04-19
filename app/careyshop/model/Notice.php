<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    通知系统模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/19
 */

namespace app\careyshop\model;

class Notice extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'notice_id';

    /**
     * 隐藏属性
     * @var string[]
     */
    protected $hidden = [
        'type',
    ];

    /**
     * 只读属性
     * @var string[]
     */
    protected $readonly = [
        'notice_id',
        'place_id',
        'platform',
        'type',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'notice_id' => 'integer',
        'place_id'  => 'integer',
        'expand'    => 'array',
        'status'    => 'integer',
    ];
}
