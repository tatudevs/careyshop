<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    广告模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/24
 */

namespace app\common\model;

class Ads extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'ads_id';

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'ads_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'ads_id'          => 'integer',
        'ads_position_id' => 'integer',
        'platform'        => 'integer',
        'type'            => 'integer',
        'begin_time'      => 'timestamp',
        'end_time'        => 'timestamp',
        'sort'            => 'integer',
        'status'          => 'integer',
    ];

    /**
     * 修改器对内容处理
     * @access public
     * @param  $value
     * @param  $data
     * @return mixed
     */
    public function setContentAttr($value, $data)
    {
        if (isset($data['type']) && $data['type'] == 0) {
            return json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        return $value;
    }

    /**
     * 获取器对内容处理
     * @access public
     * @param  $value
     * @param  $data
     * @return mixed
     */
    public function getContentAttr($value, $data)
    {
        if (isset($data['type']) && $data['type'] == 0) {
            return json_decode($value, true);
        }

        return $value;
    }

    /**
     * hasOne cs_ads_position
     * @access public
     * @return mixed
     */
    public function getAdsPosition()
    {
        return $this->hasOne(AdsPosition::class, 'ads_position_id', 'ads_position_id');
    }

    /**
     * 添加一个广告
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function addAdsItem($data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['ads_id'], $data['platform'], $data['type']);
        isset($data['content']) ?: $data['content'] = '';

        // 获取广告位
        $result = AdsPosition::find($data['ads_position_id']);
        if (is_null($result)) {
            return $this->setError('广告位不存在');
        }

        // 将广告位的属性赋值到广告
        $data['platform'] = $result->getAttr('platform');
        $data['type'] = $result->getAttr('type');

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个广告
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setAdsItem($data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        if (!empty($data['code'])) {
            $map[] = ['ads_id', '<>', $data['ads_id']];
            $map[] = ['code', '=', $data['code']];

            if (self::checkUnique($map)) {
                return $this->setError('广告编码已存在');
            }
        }

        $result = $this->find($data['ads_id']);
        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        if (isset($data['ads_position_id']) && $result->getAttr('ads_position_id') != $data['ads_position_id']) {
            $position = AdsPosition::where('ads_position_id', '=', $data['ads_position_id'])->find();
            if (is_null($position)) {
                return $this->setError('广告位不存在');
            }

            $result->setAttr('platform', $position['platform']);
            $result->setAttr('type', $position['type']);

        }

        $result->save($data);
        return $result->toArray();
    }

    /**
     * 批量删除广告
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delAdsList($data)
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        self::destroy($data['ads_id']);
        return true;
    }

    /**
     * 设置广告排序
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setAdsSort($data)
    {
        if (!$this->validateData($data, 'sort')) {
            return false;
        }

        $map[] = ['ads_id', '=', $data['ads_id']];
        self::update(['sort' => $data['sort']], $map);

        return true;
    }

    /**
     * 根据编号自动排序
     * @access public
     * @param  $data
     * @return bool
     * @throws \Exception
     */
    public function setAdsIndex($data)
    {
        if (!$this->validateData($data, 'index')) {
            return false;
        }

        $list = [];
        foreach ($data['ads_id'] as $key => $value) {
            $list[] = ['ads_id' => $value, 'sort' => $key + 1];
        }

        $this->saveAll($list);
        return true;
    }

    /**
     * 批量设置是否显示
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setAdsStatus($data)
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['ads_id', 'in', $data['ads_id']];
        self::update(['status' => $data['status']], $map);

        return true;
    }

    /**
     * 获取一个广告
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAdsItem($data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $result = $this->find($data['ads_id']);
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 获取广告列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAdsList($data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        is_empty_parm($data['platform']) ?: $map[] = ['ads.platform', '=', $data['platform']];
        is_empty_parm($data['ads_position_id']) ?: $map[] = ['ads.ads_position_id', '=', $data['ads_position_id']];
        empty($data['code']) ?: $map[] = ['ads.code', '=', $data['code']];
        empty($data['name']) ?: $map[] = ['ads.name', 'like', '%' . $data['name'] . '%'];
        is_empty_parm($data['type']) ?: $map[] = ['ads.type', '=', $data['type']];
        is_empty_parm($data['status']) ?: $map[] = ['ads.status', '=', $data['status']];
        empty($data['begin_time']) ?: $map[] = ['ads.begin_time', '< time', $data['end_time']];
        empty($data['end_time']) ?: $map[] = ['ads.end_time', '> time', $data['begin_time']];

        $result['total_result'] = $this->withJoin('getAdsPosition')->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['ads_id' => 'desc'], ['sort' => 'asc'])
            ->withJoin(['getAdsPosition' => ['ads_position_id', 'name']])
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        self::keyToSnake(['getAdsPosition'], $result['items']);
        return $result;
    }

    /**
     * 根据编码获取广告
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getAdsCode($data)
    {
        if (!$this->validateData($data, 'code')) {
            return false;
        }

        $map[] = ['code', '=', $data['code']];
        $map[] = ['begin_time', '<= time', time()];
        $map[] = ['end_time', '>= time', time()];
        $map[] = ['status', '=', 1];

        $result = $this->where($map)
            ->withoutField('code,ads_position_id,begin_time,end_time,sort,status')
            ->find();

        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 验证广告编码是否唯一
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function uniqueAdsCode($data)
    {
        if (!$this->validateData($data, 'unique')) {
            return false;
        }

        $map[] = ['code', '=', $data['code']];
        !isset($data['exclude_id']) ?: $map[] = ['ads_id', '<>', $data['exclude_id']];

        if (self::checkUnique($map)) {
            return $this->setError('广告编码已存在');
        }

        return true;
    }
}
