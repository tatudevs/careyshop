<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    通知变量模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/19
 */

namespace app\careyshop\model;

class NoticeVariable extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'notice_variable_id';

    /**
     * 只读属性
     * @var string[]
     */
    protected $readonly = [
        'notice_variable_id',
        'notice_event_id',
        'item_name',
        'replace_name',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'notice_variable_id' => 'integer',
        'notice_event_id'    => 'integer',
    ];
}
