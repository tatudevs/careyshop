<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    我的足迹模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/4
 */

namespace app\careyshop\model;

class History extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'history_id';

    /**
     * 隐藏属性
     * @var mixed|string[]
     */
    protected $hidden = [
        'user_id',
    ];

    /**
     * 只读属性
     * @var mixed|string[]
     */
    protected $readonly = [
        'history_id',
        'user_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var mixed|string[]
     */
    protected $type = [
        'history_id'  => 'integer',
        'user_id'     => 'integer',
        'goods_id'    => 'integer',
        'update_time' => 'timestamp',
    ];

    /**
     * hasOne cs_goods
     * @access public
     * @return mixed
     */
    public function getGoods()
    {
        $field = [
            'goods_id', 'goods_category_id', 'name', 'short_name', 'brand_id',
            'store_qty', 'comment_sum', 'sales_sum', 'attachment', 'status', 'is_delete',
        ];

        return $this
            ->hasOne(Goods::class, 'goods_id', 'goods_id')
            ->joinType('left')
            ->field($field)
            ->hidden(['goods_id']);
    }

    /**
     * 关联查询NULL处理
     * @param Object $value
     * @return mixed
     */
    public function getGetGoodsAttr($value = null)
    {
        return is_null($value) ? new \stdClass : $value;
    }

    /**
     * 添加一个我的足迹
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function addHistoryItem(array $data): bool
    {
        if (get_client_id() == 0) {
            return true;
        }

        if (!$this->validateData($data)) {
            return false;
        }

        $map[] = ['user_id', '=', get_client_id()];
        $map[] = ['goods_id', '=', $data['goods_id']];

        $result = $this->where($map)->find();
        if (!is_null($result)) {
            $result->setAttr('update_time', time());
            $result->save();
            return true;
        }

        // 避免无关字段
        unset($data['history_id']);
        $data['user_id'] = get_client_id();
        $data['update_time'] = time();

        if ($this->save($data)) {
            return true;
        }

        return false;
    }

    /**
     * 批量删除我的足迹
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delHistoryList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        $map[] = ['history_id', 'in', $data['history_id']];
        $map[] = ['user_id', '=', is_client_admin() ? $data['client_id'] : get_client_id()];

        $this->where($map)->delete();
        return true;
    }

    /**
     * 清空我的足迹
     * @access public
     * @return bool
     */
    public function clearHistoryList(): bool
    {
        self::destroy(['user_id' => get_client_id()]);
        return true;
    }

    /**
     * 获取我的足迹数量
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getHistoryCount(array $data)
    {
        if (!$this->validateData($data, 'count')) {
            return false;
        }

        // 搜索条件
        $map[] = ['user_id', '=', is_client_admin() ? $data['client_id'] : get_client_id()];
        $totalResult = $this->with('get_goods')->where($map)->count();

        return ['total_result' => $totalResult];
    }

    /**
     * 获取我的足迹列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getHistoryList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map[] = ['user_id', '=', is_client_admin() ? $data['client_id'] : get_client_id()];
        $result['total_result'] = $this->where($map)->count();

        if ($result['total_result'] <= 0) {
            return $result;
        }

        $result['items'] = $this->setDefaultOrder(['update_time' => 'desc'])
            ->with('get_goods')
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }
}
