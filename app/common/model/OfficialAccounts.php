<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    公众号模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/13
 */

namespace app\common\model;

class OfficialAccounts extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'official_accounts_id';

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'official_accounts_id',
        'code',
        'model',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'official_accounts_id' => 'integer',
        'code'                 => 'integer',
        'setting'              => 'array',
        'status'               => 'integer',
    ];

    /**
     * 生成唯一公众号code
     * @access private
     * @return string
     */
    private function getOfficialCode()
    {
        do {
            $code = rand_number(8);
        } while (self::checkUnique(['code' => $code]));

        return $code;
    }
}
