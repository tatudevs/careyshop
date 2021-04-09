<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    渠道用户模型
 *
 * @author      zxm <252404501@qq.com>
 * @version     v1.1
 * @date        2021/4/8
 */

namespace app\careyshop\model;

class PlaceUser extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'place_user_id';

    /**
     * 是否需要自动写入时间戳
     * @var bool|string
     */
    protected $autoWriteTimestamp = true;

    /**
     * 创建日期字段
     * @var false|string
     */
    protected $createTime = false;

    /**
     * 只读属性
     * @var string[]
     */
    protected $readonly = [
        'place_user_id',
        'user_id',
        'place_oauth_id',
        'model',
        'openid',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'place_user_id'  => 'integer',
        'user_id'        => 'integer',
        'place_oauth_id' => 'integer',
        'expires_in'     => 'integer',
        'token_response' => 'array',
    ];
}
