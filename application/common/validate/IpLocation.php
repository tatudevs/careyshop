<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    IP地址查询验证器
 *
 * @author      zxm <252404501@qq.com>
 * @version     v1.1
 * @date        2019/11/21
 */

namespace app\common\validate;

class IpLocation extends CareyShop
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'ip' => 'require|arrayHasOnlyStrings',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'ip' => 'IP',
    ];
}
