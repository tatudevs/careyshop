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
     * 主键
     * @var string
     */
    protected $pk = 'praise_id';

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'praise_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'praise_id'        => 'integer',
        'user_id'          => 'integer',
        'goods_comment_id' => 'integer',
    ];
}
