<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    通知事件模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/19
 */

namespace app\careyshop\model;

use app\careyshop\validate\Notice as Validate;

class NoticeEvent extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'notice_event_id';

    /**
     * 只读属性
     * @var string[]
     */
    protected $readonly = [
        'notice_event_id',
        'parent_id',
        'platform',
        'name',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'notice_event_id' => 'integer',
        'parent_id'       => 'integer',
    ];

    /**
     * 根据渠道获取事件列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getNoticeEvent(array $data)
    {
        if (!$this->validateData($data, 'event', false, Validate::class)) {
            return false;
        }

        return self::where('platform', '=', $data['platform'])
            ->cache('NoticeEvent')
            ->order(['notice_event_id' => 'asc'])
            ->select()
            ->toArray();
    }
}