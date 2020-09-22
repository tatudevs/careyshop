<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    数据统计验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/9/17
 */

namespace app\api\validate;

use think\Validate;

class Stats extends Validate
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'type'       => 'require|in:shop,goods,order,client',
        'begin_time' => 'requireWith:end_time|date',
        'end_time'   => 'requireWith:begin_time|date',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'type'       => '查询方式',
        'begin_time' => '起始日期',
        'end_time'   => '截止日期',
    ];
}
