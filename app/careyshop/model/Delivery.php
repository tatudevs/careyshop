<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    配送方式模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/25
 */

namespace app\careyshop\model;

class Delivery extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'delivery_id';

    /**
     * 只读属性
     * @var mixed|string[]
     */
    protected $readonly = [
        'delivery_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var mixed|string[]
     */
    protected $type = [
        'delivery_id'         => 'integer',
        'delivery_item_id'    => 'integer',
        'first_weight'        => 'float',
        'first_weight_price'  => 'float',
        'second_weight'       => 'float',
        'second_weight_price' => 'float',
        'first_item'          => 'integer',
        'first_item_price'    => 'float',
        'second_item'         => 'integer',
        'second_item_price'   => 'float',
        'first_volume'        => 'float',
        'first_volume_price'  => 'float',
        'second_volume'       => 'float',
        'second_volume_price' => 'float',
        'sort'                => 'integer',
        'status'              => 'integer',
    ];

    /**
     * hasMany cs_delivery_area
     * @access public
     * @return mixed
     */
    public function getDeliveryArea()
    {
        $field = [
            'region', 'first_weight_price', 'second_weight_price', 'first_item_price',
            'second_item_price', 'first_volume_price', 'second_volume_price',
        ];

        return $this
            ->hasMany(DeliveryArea::class)
            ->field($field);
    }

    /**
     * hasOne cs_delivery_item
     * @access public
     * @return mixed
     */
    public function getDeliveryItem()
    {
        return $this->hasOne(DeliveryItem::class, 'delivery_item_id', 'delivery_item_id');
    }

    /**
     * 添加一个配送方式
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addDeliveryItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['delivery_id']);

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个配送方式
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setDeliveryItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        if (!empty($data['delivery_item_id'])) {
            $map = [
                ['delivery_id', '<>', $data['delivery_id']],
                ['delivery_item_id', '=', $data['delivery_item_id']],
            ];

            if (self::checkUnique($map)) {
                return $this->setError('快递公司编号已存在');
            }
        }

        $map = [
            ['delivery_id', '=', $data['delivery_id']],
            ['delivery_item_id', '=', $data['delivery_item_id']],
        ];

        $result = self::update($data, $map);
        return $result->toArray();
    }

    /**
     * 批量删除配送方式
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delDeliveryList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        self::destroy($data['delivery_id']);
        return true;
    }

    /**
     * 获取一个配送方式
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getDeliveryItems(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        return $this->findOrEmpty($data['delivery_id'])->toArray();
    }

    /**
     * 获取配送方式列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getDeliveryList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];

        // 后台管理搜索
        if (is_client_admin()) {
            is_empty_parm($data['status']) ?: $map[] = ['delivery.status', '=', $data['status']];
            empty($data['name']) ?: $map[] = ['delivery.alias|getDeliveryItem.name', 'like', '%' . $data['name'] . '%'];
        } else {
            $map[] = ['delivery.status', '=', 1];
        }

        $result = $this->setAliasOrder('delivery')
            ->setDefaultOrder(['delivery_id' => 'desc'], ['sort' => 'asc'], true)
            ->withJoin(['getDeliveryItem'])
            ->where($map)
            ->withSearch(['order'], $data)
            ->select()
            ->toArray();

        self::keyToSnake(['getDeliveryItem'], $result);
        return $result;
    }

    /**
     * 获取配送方式选择列表
     * @access public
     * @return array
     */
    public function getDeliverySelect(): array
    {
        $map[] = ['d.status', '=', 1];
        $map[] = ['i.is_delete', '=', 0];

        return $this->alias('d')
            ->field('d.delivery_id,i.name,d.alias,i.code')
            ->join('delivery_item i', 'i.delivery_item_id = d.delivery_item_id', 'inner')
            ->where($map)
            ->order('d.sort asc')
            ->select()
            ->toArray();
    }

    /**
     * 根据配送方式获取运费
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getDeliveryFreight(array $data)
    {
        if (!$this->validateData($data, 'freight')) {
            return false;
        }

        // 获取基础数据
        $map[] = ['delivery_id', '=', $data['delivery_id']];
        $map[] = ['status', '=', 1];

        $delivery = $this->where($map)->find();
        if (is_null($delivery)) {
            return $this->setError('配送方式不存在');
        }

        // 获取配送区域数据
        $deliveryArea = $delivery->getDeliveryArea()->select();

        // 获取区域列表
        $regionList = Region::getRegionCacheList();
        $regionId = [];

        while (true) {
            if (!isset($regionList[$data['region_id']])) {
                break;
            }

            if ($regionList[$data['region_id']]['parent_id'] <= 0) {
                break;
            }

            $regionId[] = $regionList[$data['region_id']]['region_id'];
            $data['region_id'] = $regionList[$data['region_id']]['parent_id'];
        }

        // 确认各个计量基础费用
        $firstWeightPrice = $delivery['first_weight_price'];
        $secondWeightPrice = $delivery['second_weight_price'];
        $firstItemPrice = $delivery['first_item_price'];
        $secondItemPrice = $delivery['second_item_price'];
        $firstVolumePrice = $delivery['first_volume_price'];
        $secondVolumePrice = $delivery['second_volume_price'];

        // 存在区域则需要取区域的费用
        foreach ($regionId as $value) {
            foreach ($deliveryArea->toArray() as $item) {
                foreach ($item['region'] as $region) {
                    if ($region['region_id'] == $value) {
                        $firstWeightPrice = $item['first_weight_price'];
                        $secondWeightPrice = $item['second_weight_price'];
                        $firstItemPrice = $item['first_item_price'];
                        $secondItemPrice = $item['second_item_price'];
                        $firstVolumePrice = $item['first_volume_price'];
                        $secondVolumePrice = $item['second_volume_price'];
                        break 3;
                    }
                }
            }
        }

        // 计算各个计量续量费用
        $result = [
            'delivery_fee' => 0,
            'weight_fee'   => 0,
            'item_fee'     => 0,
            'volume_fee'   => 0,
        ];

        if (!empty($data['weight_total'])) {
            $result['weight_fee'] = $firstWeightPrice;
            $result['delivery_fee'] += $firstWeightPrice;
            $weight = $data['weight_total'] - $delivery['first_weight'];

            while ($weight > 0 && $delivery['second_weight'] > 0 && $secondWeightPrice > 0) {
                $weight -= $delivery['second_weight'];
                $result['weight_fee'] += $secondWeightPrice;
                $result['delivery_fee'] += $secondWeightPrice;
            }
        }

        if (!empty($data['item_total'])) {
            $result['item_fee'] = $firstItemPrice;
            $result['delivery_fee'] += $firstItemPrice;
            $item = $data['item_total'] - $delivery['first_item'];

            while ($item > 0 && $delivery['second_item'] > 0 && $secondItemPrice > 0) {
                $item -= $delivery['second_item'];
                $result['item_fee'] += $secondItemPrice;
                $result['delivery_fee'] += $secondItemPrice;
            }
        }

        if (!empty($data['volume_total'])) {
            $result['volume_fee'] = $firstVolumePrice;
            $result['delivery_fee'] += $firstVolumePrice;
            $volume = $data['volume_total'] - $delivery['first_volume'];

            while ($volume > 0 && $delivery['second_volume'] > 0 && $secondVolumePrice > 0) {
                $volume -= $delivery['second_volume'];
                $result['volume_fee'] += $secondVolumePrice;
                $result['delivery_fee'] += $secondVolumePrice;
            }
        }

        return ['delivery_fee' => round($result['delivery_fee'], 2)];
    }

    /**
     * 批量设置配送方式状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setDeliveryStatus(array $data): bool
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['delivery_id', 'in', $data['delivery_id']];
        self::update(['status' => $data['status']], $map);

        return true;
    }

    /**
     * 验证快递公司编号是否已存在
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function uniqueDeliveryItem(array $data): bool
    {
        if (!$this->validateData($data, 'unique')) {
            return false;
        }

        $map[] = ['delivery_item_id', '=', $data['delivery_item_id']];
        !isset($data['exclude_id']) ?: $map[] = ['delivery_id', '<>', $data['exclude_id']];

        if (self::checkUnique($map)) {
            return $this->setError('快递公司编号已存在');
        }

        return true;
    }

    /**
     * 设置配送方式排序
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setDeliverySort(array $data): bool
    {
        if (!$this->validateData($data, 'sort')) {
            return false;
        }

        $map[] = ['delivery_id', '=', $data['delivery_id']];
        self::update(['sort' => $data['sort']], $map);

        return true;
    }

    /**
     * 根据编号自动排序
     * @access public
     * @param array $data
     * @return bool
     */
    public function setDeliveryIndex(array $data): bool
    {
        if (!$this->validateData($data, 'index')) {
            return false;
        }

        foreach ($data['delivery_id'] as $key => $value) {
            self::update(['sort' => $key + 1], ['delivery_id' => $value]);
        }

        return true;
    }
}
