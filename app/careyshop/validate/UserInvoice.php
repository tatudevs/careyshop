<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    账号发票信息验证器
 *
 * @author      zxm <252404501@qq.com>
 * @version     v1.1
 * @date        2021/4/11
 */

namespace app\careyshop\validate;

class UserInvoice extends CareyShop
{
    /**
     * 验证规则
     * @var string[]
     */
    protected $rule = [
        'user_invoice_id' => 'integer|gt:0',
        'head'            => 'require|max:128',
        'type'            => 'require|in:0,1,2',
        'content'         => 'require|in:0,1',
        'tax'             => 'require|max:128',
        'bank'            => 'requireIf:type,2|max:64',
        'account'         => 'requireIf:type,2|max:64',
        'address'         => 'requireIf:type,2|max:128',
        'phone'           => 'requireIf:type,2|max:64',
        'email'           => 'require|email|max:128',
    ];

    /**
     * 字段描述
     * @var string[]
     */
    protected $field = [
        'user_invoice_id' => '开票信息编号',
        'head'            => '发票抬头',
        'type'            => '发票类型',
        'content'         => '发票内容',
        'tax'             => '纳税人识别码',
        'bank'            => '开户银行',
        'account'         => '开户账号',
        'address'         => '场所地址',
        'phone'           => '固定电话',
        'email'           => '收票邮箱',
    ];

    /**
     * 场景规则
     * @var string[]
     */
    protected $scene = [
        'item' => [
            'user_invoice_id' => 'require|integer|gt:0',
        ],
        'del'  => [
            'user_invoice_id' => 'require|arrayHasOnlyInts',
        ],
    ];
}
