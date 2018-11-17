<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    操作日志模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/6/24
 */

namespace app\common\model;

class ActionLog extends CareyShop
{
    /**
     * 是否需要自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 更新时间字段
     * @var bool/string
     */
    protected $updateTime = false;

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'action_log_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'action_log_id' => 'integer',
        'client_type'   => 'integer',
        'user_id'       => 'integer',
        'params'        => 'json',
        'result'        => 'json',
        'status'        => 'integer',
    ];

    /**
     * 获取一条操作日志
     * @access public
     * @param  array $data 外部数据
     * @return mixed
     * @throws
     */
    public function getActionLogItem($data)
    {
        if (!$this->validateData($data, 'ActionLog.item')) {
            return false;
        }

        $result = self::get($data['action_log_id']);
        if (false !== $result) {
            return is_null($result) ? null : $result->toArray();
        }

        return false;
    }

    /**
     * 获取操作日志列表
     * @access public
     * @param  array $data 外部数据
     * @return false|array
     * @throws
     */
    public function getActionLogList($data)
    {
        if (!$this->validateData($data, 'ActionLog')) {
            return false;
        }

        $map = [];
        is_empty_parm($data['client_type']) ?: $map['client_type'] = ['eq', $data['client_type']];
        empty($data['username']) ?: $map['username'] = ['eq', $data['username']];
        empty($data['path']) ?: $map['path'] = ['eq', $data['path']];
        is_empty_parm($data['status']) ?: $map['status'] = ['eq', $data['status']];

        if (!empty($data['begin_time']) && !empty($data['end_time'])) {
            $map['create_time'] = ['between time', [$data['begin_time'], $data['end_time']]];
        }

        $totalResult = $this->where($map)->count();
        if ($totalResult <= 0) {
            return ['total_result' => 0];
        }

        $result = self::all(function ($query) use ($map, $data) {
            // 翻页页数
            $pageNo = isset($data['page_no']) ? $data['page_no'] : 1;

            // 每页条数
            $pageSize = isset($data['page_size']) ? $data['page_size'] : config('paginate.list_rows');

            // 排序方式
            $orderType = !empty($data['order_type']) ? $data['order_type'] : 'desc';

            // 排序的字段
            $orderField = !empty($data['order_field']) ? $data['order_field'] : 'action_log_id';

            $query
                ->where($map)
                ->order([$orderField => $orderType])
                ->page($pageNo, $pageSize);
        });

        if (false !== $result) {
            $logList = $result->toArray();
            $menuList = Menu::getMenuListData('api');

            if (false !== $menuList) {
                $menuMap = array_column($menuList, 'name', 'url');
                foreach ($logList as &$value) {
                    $oldPath = $value['path'];
                    $value['action'] = array_key_exists($oldPath, $menuMap) ? $menuMap[$oldPath] : '未知操作';
                }
            }

            return ['items' => $logList, 'total_result' => $totalResult];
        }

        return false;
    }
}
