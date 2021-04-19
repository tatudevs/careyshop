<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    通知事件模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/19
 */

namespace app\careyshop\model;

class NoticeEvent extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'notice_event_id';

    /**
     * 只读属性
     * @var string[]
     */
    protected $readonly = [
        'notice_event_id',
        'platform',
        'module',
        'event',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'notice_event_id' => 'integer',
    ];
}
