<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    通知系统可用变量模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/3
 */

namespace app\careyshop\model;

class NoticeItem extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'notice_item_id';

    /**
     * 隐藏属性
     * @var mixed|string[]
     */
    protected $hidden = [
        'notice_item_id',
        'type',
    ];

    /**
     * 只读属性
     * @var mixed|string[]
     */
    protected $readonly = [
        'notice_item_id',
        'item_name',
        'replace_name',
        'type',
    ];

    /**
     * 字段类型或者格式转换
     * @var mixed|string[]
     */
    protected $type = [
        'notice_item_id' => 'integer',
        'type'           => 'integer',
    ];
}
