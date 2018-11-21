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

use think\Cache;

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
     * 敏感词过滤字段
     * @var array
     */
    protected $safety = [
        'password',
        'appkey',
        'app_key',
        'app_secret',
        'give_code',
        'exchange_code',
        'setting',
        'value',
        'token',
        'token_expires',
        'refresh',
        'refresh_expires',
        'source_no',
        'tel',
        'mobile',
        'email',
        'account',
    ];

    /**
     * 设置菜单操作动作
     * @access private
     * @param string $key   来源值
     * @param string $value 修改值
     */
    private function setMenuMap($key, &$value)
    {
        static $menuMap = null;
        if (empty($menuMap)) {
            $menuMap = Cache::remember('menuOfActionLog', function () {
                $menuList = Menu::getMenuListData('api');
                return array_column($menuList, 'name', 'url');
            });

            Cache::tag('CommonAuth', 'menuOfActionLog');
        }

        if (array_key_exists($key, $menuMap)) {
            $value = $menuMap[$key];
            return;
        }

        $value = '未知操作';
    }

    /**
     * 获取器设置日志操作动作
     * @access public
     * @param $value
     * @param $data
     * @return string
     */
    public function getActionAttr($value, $data)
    {
        try {
            $this->setMenuMap($data['path'], $value);
        } catch (\Exception $e) {
            $value = '未知操作';
        }

        return $value;
    }

    /**
     * 获取器设置请求参数
     * @access public
     * @param $value
     * @return mixed
     */
    public function getParamsAttr($value)
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (is_array($value)) {
            $this->privacyField($value);
        }

        return $value;
    }

    /**
     * 获取器设置处理结果
     * @access public
     * @param $value
     * @return mixed
     */
    public function getResultAttr($value)
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (is_array($value)) {
            $this->privacyField($value);
        }

        return $value;
    }

    /**
     * 对过敏字段进行隐私保护
     * @access private
     * @param array $arr 原始数组
     */
    private function privacyField(&$arr)
    {
        foreach ($arr as $key => $val) {
            if (is_array($val)) {
                $this->privacyField($arr[$key]);
            } elseif (in_array($key, $this->safety)) {
                $arr[$key] = auto_hid_substr($val);
            }
        }
    }

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
            return is_null($result) ? null : $result->append(['action'])->toArray();
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
            return ['items' => $result->append(['action'])->toArray(), 'total_result' => $totalResult];
        }

        return false;
    }
}
