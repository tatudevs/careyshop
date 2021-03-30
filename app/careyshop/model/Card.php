<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    购物卡模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/14
 */

namespace app\careyshop\model;

class Card extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'card_id';

    /**
     * 是否需要自动写入时间戳
     * @var bool|string
     */
    protected $autoWriteTimestamp = true;

    /**
     * 更新日期字段
     * @var false|string
     */
    protected $updateTime = false;

    /**
     * 只读属性
     * @var mixed|string[]
     */
    protected $readonly = [
        'card_id',
        'money',
        'give_num',
        'create_time',
        'end_time',
    ];

    /**
     * 字段类型或者格式转换
     * @var mixed|string[]
     */
    protected $type = [
        'card_id'          => 'integer',
        'money'            => 'float',
        'category'         => 'array',
        'exclude_category' => 'array',
        'give_num'         => 'integer',
        'active_num'       => 'integer',
        'end_time'         => 'timestamp',
        'status'           => 'integer',
        'is_delete'        => 'integer',
    ];

    /**
     * hasMany cs_card_use
     * @access public
     * @return mixed
     */
    public function getCardUse()
    {
        return $this->hasMany(CardUse::class, 'card_id');
    }

    /**
     * 添加一条购物卡
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addCardItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段并初始化
        unset($data['card_id'], $data['active_num']);
        !empty($data['category']) ?: $data['category'] = [];
        !empty($data['exclude_category']) ?: $data['exclude_category'] = [];

        // 开启事务
        $this->startTrans();

        try {
            // 添加购物卡
            $this->save($data);

            // 准备购物卡使用数据
            $useData = [];
            for ($i = 0; $i < $data['give_num']; $i++) {
                $useData[] = [
                    'card_id'  => $this->getAttr('card_id'),
                    'number'   => rand_number(11),
                    'password' => rand_string(16, false),
                    'money'    => $data['money'],
                ];
            }

            // 添加购物卡使用集合
            if (!$this->getCardUse()->insertAll($useData)) {
                throw new \Exception($this->getError());
            }

            $this->commit();
            return $this->toArray();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 编辑一条购物卡
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setCardItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        // 避免不允许修改字段
        unset($data['active_num'], $data['is_delete']);

        // 数组字段特殊处理
        if (isset($data['category']) && '' == $data['category']) {
            $data['category'] = [];
        }

        if (isset($data['exclude_category']) && '' == $data['exclude_category']) {
            $data['exclude_category'] = [];
        }

        $map[] = ['card_id', '=', $data['card_id']];
        $map[] = ['is_delete', '=', 0];

        $result = self::update($data, $map);
        return $result->toArray();
    }

    /**
     * 获取一条购物卡
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getCardItem(array $data)
    {
        if (!$this->validateData($data, 'get')) {
            return false;
        }

        // 搜索条件
        $map[] = ['card_id', '=', $data['card_id']];
        $map[] = ['is_delete', '=', 0];

        $result = $this->where($map)->withoutField('is_delete')->find();
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 批量设置购物卡状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setCardStatus(array $data): bool
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['card_id', 'in', $data['card_id']];
        $map[] = ['is_delete', '=', 0];

        self::update(['status' => $data['status']], $map);
        return true;
    }

    /**
     * 批量删除购物卡
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delCardList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        $map[] = ['card_id', 'in', $data['card_id']];
        $map[] = ['is_delete', '=', 0];

        self::update(['is_delete' => 1], $map);
        return true;
    }

    /**
     * 获取购物卡列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getCardList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map[] = ['is_delete', '=', 0];
        empty($data['name']) ?: $map[] = ['name', 'like', '%' . $data['name'] . '%'];
        is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['card_id' => 'desc'])
            ->where($map)
            ->withoutField('is_delete')
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }
}
