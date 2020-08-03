<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    通知系统可用变量模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/3
 */

namespace app\common\model;

class NoticeItem extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'notice_item_id';

    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'notice_item_id',
        'type',
    ];

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'notice_item_id',
        'item_name',
        'replace_name',
        'type',
    ];
}
