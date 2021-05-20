<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    通知变量模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/19
 */

namespace app\careyshop\model;

class NoticeVariable extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'notice_variable_id';

    /**
     * 只读属性
     * @var string[]
     */
    protected $readonly = [
        'notice_variable_id',
        'notice_event_id',
        'item_name',
        'replace_name',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'notice_variable_id' => 'integer',
        'notice_event_id'    => 'integer',
    ];

    /**
     * 根据事件获取替换变量
     * @access public
     * @param array $data 外部数据
     * @return array
     * @throws
     */
    public function getNoticeVariable(array $data): array
    {
        if (is_empty_parm($data['notice_event_id'])) {
            return [];
        }

        return self::cache()
            ->where('notice_event_id', '=', $data['notice_event_id'])
            ->select()
            ->toArray();
    }
}
