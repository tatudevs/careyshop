<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    通知系统验证器
 *
 * @author      zxm <252404501@qq.com>
 * @version     v1.1
 * @date        2021/4/20
 */

namespace app\careyshop\validate;

class Notice extends CareyShop
{
    /**
     * 验证规则
     * @var string[]
     */
    protected $rule = [
        'platform' => 'require|max:16|checkModule:oauth',
    ];

    /**
     * 字段描述
     * @var string[]
     */
    protected $field = [
        'platform' => '应用渠道',
    ];

    /**
     * 场景规则
     * @var string[]
     */
    protected $scene = [
        'event' => [
            'platform',
        ],
    ];
}
