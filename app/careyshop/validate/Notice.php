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
        'notice_id'       => 'integer|gt:0',
        'place_id'        => 'require|integer|egt:0',
        'notice_event_id' => 'require|integer|gt:0',
        'platform'        => 'require|max:16|checkModule:oauth',
        'type'            => 'requireIf:place_id,0|in:sms,email',
        'name'            => 'require|max:32',
        'template'        => 'require',
        'expand'          => 'array',
        'status'          => 'in:0,1',
    ];

    /**
     * 字段描述
     * @var string[]
     */
    protected $field = [
        'notice_id'       => '通知编号',
        'place_id'        => '渠道平台编号',
        'notice_event_id' => '通知事件编号',
        'platform'        => '应用渠道',
        'type'            => '系统类型',
        'name'            => '通知名称',
        'template'        => '通知模板',
        'expand'          => '扩展配置',
        'status'          => '通知状态',
    ];

    /**
     * 场景规则
     * @var string[]
     */
    protected $scene = [
        'set'    => [
            'notice_id' => 'require|integer|gt:0',
            'name',
            'template',
            'expand',
            'status',
        ],
        'del'    => [
            'notice_id' => 'require|arrayHasOnlyInts',
        ],
        'item'   => [
            'notice_id' => 'require|integer|gt:0',
        ],
        'list'   => [
            'place_id',
            'type',
        ],
        'status' => [
            'notice_id' => 'require|arrayHasOnlyInts',
            'status'    => 'require|in:0,1',
        ],
        'event'  => [
            'platform',
        ],
    ];
}
