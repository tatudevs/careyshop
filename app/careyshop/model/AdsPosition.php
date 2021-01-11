<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    广告位置模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/24
 */

namespace app\careyshop\model;

class AdsPosition extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'ads_position_id';

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'ads_position_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'ads_position_id' => 'integer',
        'platform'        => 'integer',
        'width'           => 'integer',
        'height'          => 'integer',
        'type'            => 'integer',
        'display'         => 'integer',
        'status'          => 'integer',
    ];

    /**
     * 修改器对内容处理
     * @access public
     * @param  $value
     * @param  $data
     * @return false|string
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
     * 添加一个广告位置
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function addPositionItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['ads_position_id']);
        isset($data['content']) ?: $data['content'] = '';

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个广告位置
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setPositionItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        if (!empty($data['code'])) {
            $map[] = ['ads_position_id', '<>', $data['ads_position_id']];
            $map[] = ['code', '=', $data['code']];

            if (self::checkUnique($map)) {
                return $this->setError('广告位置编码已存在');
            }
        }

        $result = $this->find($data['ads_position_id']);
        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        if (isset($data['platform']) && $result->getAttr('platform') != $data['platform']) {
            $adsData['platform'] = $data['platform'];
        }

        if (isset($data['type']) && $result->getAttr('type') != $data['type']) {
            $adsData['type'] = $data['type'];
        }

        if ($result->save($data)) {
            if (!empty($adsData)) {
                Ads::update($adsData, ['ads_position_id' => $data['ads_position_id']]);
            }

            return $result->toArray();
        }

        return false;
    }

    /**
     * 批量删除广告位置(支持检测是否存在关联广告)
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delPositionList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        // 检测是否存在关联广告
        if (isset($data['not_empty']) && $data['not_empty'] == 1) {
            $result = $this->alias('p')
                ->field('p.ads_position_id,p.name')
                ->join('ads a', 'a.ads_position_id = p.ads_position_id')
                ->where('p.ads_position_id', 'in', $data['ads_position_id'])
                ->group('p.ads_position_id')
                ->find();

            if (!is_null($result)) {
                $error = 'Id:' . $result->getAttr('ads_position_id') . ' 广告位置"';
                $error .= $result->getAttr('name') . '"存在关联广告';
                return $this->setError($error);
            }
        }

        self::destroy($data['ads_position_id']);
        return true;
    }

    /**
     * 验证广告位置编号是否唯一
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function uniquePositionCode(array $data): bool
    {
        if (!$this->validateData($data, 'unique')) {
            return false;
        }

        $map[] = ['code', '=', $data['code']];
        !isset($data['exclude_id']) ?: $map[] = ['ads_position_id', '<>', $data['exclude_id']];

        if (self::checkUnique($map)) {
            return $this->setError('广告位置编码已存在');
        }

        return true;
    }

    /**
     * 批量设置广告位置状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setPositionStatus(array $data): bool
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['ads_position_id', 'in', $data['ads_position_id']];
        self::update(['status' => $data['status']], $map);

        return true;
    }

    /**
     * 获取一个广告位置
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getPositionItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $result = $this->find($data['ads_position_id']);
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 获取广告位置列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getPositionList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['name']) ?: $map[] = ['name', 'like', '%' . $data['name'] . '%'];
        empty($data['code']) ?: $map[] = ['code', '=', $data['code']];
        is_empty_parm($data['platform']) ?: $map[] = ['platform', '=', $data['platform']];
        is_empty_parm($data['type']) ?: $map[] = ['type', '=', $data['type']];
        is_empty_parm($data['display']) ?: $map[] = ['display', '=', $data['display']];
        is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];

        // 获取总数量,为空直接返回
        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['ads_position_id' => 'desc'])
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }

    /**
     * 获取广告位置选择列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getPositionSelect(array $data)
    {
        if (!$this->validateData($data, 'select')) {
            return false;
        }

        // 搜索条件
        $map = [];
        is_empty_parm($data['platform']) ?: $map[] = ['platform', '=', $data['platform']];
        is_empty_parm($data['type']) ?: $map[] = ['type', '=', $data['type']];
        is_empty_parm($data['display']) ?: $map[] = ['display', '=', $data['display']];
        is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];

        // 实际查询
        return $this->setDefaultOrder(['ads_position_id' => 'desc'])
            ->withoutField('description,width,height,content,color')
            ->where($map)
            ->withSearch(['order'], $data)
            ->select()
            ->toArray();
    }

    /**
     * 根据广告位置编码获取广告
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getPositionCode(array $data)
    {
        if (!$this->validateData($data, 'code')) {
            return false;
        }

        $map = [
            ['code', '=', $data['code']],
            ['status', '=', 1],
        ];

        $result = $this->withoutField('code,description,status')->where($map)->find();
        if (is_null($result)) {
            return null;
        }

        $map = [
            ['ads_position_id', '=', $result->getAttr('ads_position_id')],
            ['begin_time', '<= time', time()],
            ['end_time', '>= time', time()],
            ['status', '=', 1],
        ];

        // 随机展示的广告没必要排序
        $adsDb = new Ads();
        if (in_array($result->getAttr('display'), [0, 1])) {
            $adsDb = $adsDb->order(['sort' => 'asc', 'ads_id' => 'desc']);
        }

        $adsResult = $adsDb->field('ads_id,name,url,target,content,color,type')->where($map)->select();
        $adsData = $adsResult->toArray(); // 从Ads数据集获取 0=多个 1=单个 2=随机多个 3=随机单个

        if (!empty($adsData)) {
            switch ($result->getAttr('display')) {
                case 1:
                    $adsData = [array_shift($adsData)];
                    break;

                case 2:
                    shuffle($adsData);
                    break;

                case 3:
                    shuffle($adsData);
                    $adsData = [array_shift($adsData)];
                    break;
            }
        }

        $result->setAttr('ads_items', $adsData);
        return $result->toArray();
    }
}
