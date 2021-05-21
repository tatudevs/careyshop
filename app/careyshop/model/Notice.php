<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    通知模板模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/4/19
 */

namespace app\careyshop\model;

class Notice extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'notice_id';

    /**
     * 只读属性
     * @var string[]
     */
    protected $readonly = [
        'notice_id',
        'place_id',
        'notice_event_id',
        'platform',
        'type',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'notice_id'       => 'integer',
        'place_id'        => 'integer',
        'notice_event_id' => 'integer',
        'expand'          => 'array',
        'status'          => 'integer',
    ];

    /**
     * 添加一个通知模板
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addNoticeItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['notice_id']);

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个通知模板
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setNoticeItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        $map[] = ['notice_id', '=', $data['notice_id']];
        $result = self::update($data, $map);

        return $result->toArray();
    }

    /**
     * 批量删除通知模板
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delNoticeList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        self::destroy($data['notice_id']);
        return true;
    }

    /**
     * 获取一个通知模板
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getNoticeItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        return $this->findOrEmpty($data['notice_id'])->toArray();
    }

    /**
     * 获取通知模板列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getNoticeList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map[] = ['place_id', '=', $data['place_id']];
        empty($data['type']) ?: $map[] = ['type', '=', $data['type']];

        return self::where($map)
            ->order('notice_event_id', 'asc')
            ->select()
            ->toArray();
    }

    /**
     * 批量设置模板状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setNoticeStatus(array $data): bool
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['notice_id', 'in', $data['notice_id']];
        self::update(['status' => $data['status']], $map);

        return true;
    }
}
