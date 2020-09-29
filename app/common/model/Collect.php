<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    收藏夹模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/14
 */

namespace app\common\model;

class Collect extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'collect_id';

    /**
     * 是否需要自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 更新日期字段
     * @var bool|string
     */
    protected $updateTime = false;

    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'user_id',
    ];

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'collect_id',
        'user_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'collect_id' => 'integer',
        'user_id'    => 'integer',
        'goods_id'   => 'integer',
        'is_top'     => 'integer',
    ];

    /**
     * hasOne cs_goods
     * @access public
     * @return mixed
     */
    public function getGoods()
    {
        return $this->hasOne(Goods::class, 'goods_id', 'goods_id');
    }

    /**
     * 添加一个商品收藏
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function addCollectItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段,并初始化部分数据
        unset($data['collect_id']);
        $data['user_id'] = get_client_id();

        if (0 == $data['user_id']) {
            return true;
        }

        $map[] = ['goods_id', '=', $data['goods_id']];
        $map[] = ['user_id', '=', $data['user_id']];
        if (self::checkUnique($map)) {
            return true;
        }

        if ($this->save($data)) {
            return true;
        }

        return false;
    }

    /**
     * 批量删除商品收藏
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delCollectList(array $data)
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        $map[] = ['collect_id', 'in', $data['collect_id']];
        $map[] = ['user_id', '=', get_client_id()];
        $this->where($map)->delete();

        return true;
    }

    /**
     * 清空商品收藏夹
     * @access public
     * @return bool
     */
    public function clearCollectList()
    {
        self::destroy(['user_id' => get_client_id()]);
        return true;
    }

    /**
     * 设置收藏商品是否置顶
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setCollectTop(array $data)
    {
        if (!$this->validateData($data, 'top')) {
            return false;
        }

        $map[] = ['user_id', '=', get_client_id()];
        $map[] = ['collect_id', 'in', $data['collect_id']];

        self::update(['is_top' => $data['is_top']], $map);
        return true;
    }

    /**
     * 获取商品收藏列表
     * @access public
     * @param array $data 外部数据
     * @return false|array
     * @throws
     */
    public function getCollectList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map[] = ['collect.user_id', '=', get_client_id()];

        $result['total_result'] = $this->withJoin('getGoods')->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 关联表的返回字段
        $field = [
            'goods_category_id', 'name', 'short_name', 'brand_id', 'market_price', 'shop_price',
            'store_qty', 'comment_sum', 'sales_sum', 'attachment', 'status', 'is_delete',
        ];

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['collect_id' => 'desc'], ['is_top' => 'desc'], true)
            ->withJoin(['getGoods' => $field])
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        self::keyToSnake(['getGoods'], $result['items']);
        return $result;
    }

    /**
     * 获取商品收藏数量
     * @access public
     * @return array
     * @throws
     */
    public function getCollectCount()
    {
        // 搜索条件
        $map[] = ['collect.user_id', '=', get_client_id()];
        $totalResult = $this->withJoin('getGoods')->where($map)->count();

        return ['total_result' => $totalResult];
    }

    /**
     * 检测指定商品是否被收藏
     * @access public
     * @param array $data 外部数据
     * @return false|array
     */
    public function isCollectGoods(array $data)
    {
        if (!$this->validateData($data, 'goods')) {
            return false;
        }

        // 游客返回结果
        if (get_client_type() <= 0) {
            return ['is_collect' => 0];
        }

        // 查询条件
        $map[] = ['user_id', '=', get_client_id()];
        $map[] = ['goods_id', '=', $data['goods_id']];

        $result = $this->where($map)->count();
        return ['is_collect' => $result ? 1 : 0];
    }
}
