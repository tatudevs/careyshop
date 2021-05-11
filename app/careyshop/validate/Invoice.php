<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    票据管理验证器
 *
 * @author      zxm <252404501@qq.com>
 * @version     v1.1
 * @date        2021/4/11
 */

namespace app\careyshop\validate;

class Invoice extends CareyShop
{
    /**
     * 验证规则
     * @var string[]
     */
    protected $rule = [
        'invoice_id'      => 'integer|gt:0',
        'order_no'        => 'require|unique:invoice|max:50',
        'client_id'       => 'require|integer|gt:0',
        'user_invoice_id' => 'require|integer|gt:0',
        'premium'         => 'float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'order_amount'    => 'float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'invoice_amount'  => 'float|egt:0|regex:^\d+(\.\d{1,2})?$',
        'number'          => 'max:32',
        'remark'          => 'max:255',
        'attachment'      => 'array',
        'status'          => 'in:0,1,2',
        'send_email'      => 'in:0,1',
        'begin_time'      => 'date|betweenTime|beforeTime:end_time',
        'end_time'        => 'date|betweenTime|afterTime:begin_time',
        'page_no'         => 'integer|egt:0',
        'page_size'       => 'integer|egt:0',
        'order_type'      => 'requireWith:order_field|in:asc,desc',
        'order_field'     => 'requireWith:order_type|in:invoice_id,status,create_time',
    ];

    /**
     * 字段描述
     * @var string[]
     */
    protected $field = [
        'invoice_id'      => '票据编号',
        'order_no'        => '订单号',
        'client_id'       => '账号编号',
        'user_invoice_id' => '开票信息编号',
        'premium'         => '开票税率',
        'order_amount'    => '订单金额',
        'invoice_amount'  => '开票金额',
        'number'          => '发票编号',
        'remark'          => '发票备注',
        'attachment'      => '开票资源',
        'status'          => '发票状态',
        'send_email'      => '发送系统邮件',
        'begin_time'      => '开始日期',
        'end_time'        => '结束日期',
        'page_no'         => '页码',
        'page_size'       => '每页数量',
        'order_type'      => '排序方式',
        'order_field'     => '排序字段',
    ];

    /**
     * 场景规则
     * @var string[]
     */
    protected $scene = [
        'set'   => [
            'invoice_id' => 'require|integer|gt:0',
            'number',
            'remark',
            'attachment',
            'status',
            'send_email',
        ],
        'reset' => [
            'invoice_id' => 'require|integer|gt:0',
        ],
        'order' => [
            'order_no' => 'require|max:50',
        ],
        'list'  => [
            'order_no' => 'max:50',
            'status',
            'begin_time',
            'end_time',
            'page_no',
            'page_size',
            'order_type',
            'order_field',
        ],
    ];
}
