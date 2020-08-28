<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    快递公司模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/25
 */

namespace app\common\model;

use think\facade\Config;
use think\helper\Str;
use util\Phonetic;

class DeliveryItem extends CareyShop
{
    /**
     * 快递鸟查询URL
     * @var string
     */
    const KDNIAO_URL = 'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx';

    /**
     * 主键
     * @var string
     */
    protected $pk = 'delivery_item_id';

    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'is_delete',
    ];

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'delivery_item_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'delivery_item_id' => 'integer',
        'type'             => 'integer',
        'is_delete'        => 'integer',
    ];

    /**
     * 添加一个快递公司
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addCompanyItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['delivery_item_id'], $data['is_delete']);

        // 检测编码是否重复
        $map[] = ['code', '=', $data['code']];
        $map[] = ['type', '=', $data['type']];
        $map[] = ['is_delete', '=', 0];

        if (self::checkUnique($map)) {
            return $this->setError('快递公司编码已存在');
        }

        // 获取快递公司首拼
        if (!isset($data['phonetic'])) {
            $data['phonetic'] = Phonetic::encode(Str::substr($data['name'], 0, 1));
            $data['phonetic'] = Str::lower($data['phonetic']);
        }

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个快递公司
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setCompanyItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        // 获取主数据
        $map = [
            ['delivery_item_id', '=', $data['delivery_item_id']],
            ['is_delete', '=', 0],
        ];

        $result = $this->where($map)->find();
        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        // 编码是否重复检测
        if (!empty($data['code']) || isset($data['type'])) {
            $map = [
                ['delivery_item_id', '<>', $data['delivery_item_id']],
                ['code', '=', !empty($data['code']) ? $data['code'] : $result->getAttr('code')],
                ['type', '=', isset($data['type']) ? $data['type'] : $result->getAttr('type')],
                ['is_delete', '=', 0],
            ];

            if (self::checkUnique($map)) {
                return $this->setError('快递公司编码已存在');
            }
        }

        // 获取快递公司首拼
        if (isset($data['name']) && !isset($data['phonetic'])) {
            $data['phonetic'] = Phonetic::encode(Str::substr($data['name'], 0, 1));
            $data['phonetic'] = Str::lower($data['phonetic']);
        }

        if ($result->save($data)) {
            return $result->toArray();
        }

        return false;
    }

    /**
     * 批量删除快递公司
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delCompanyList(array $data)
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        $result = $this->alias('i')
            ->field('i.delivery_item_id,i.name')
            ->join('delivery d', 'd.delivery_item_id = i.delivery_item_id')
            ->where('i.delivery_item_id', 'in', $data['delivery_item_id'])
            ->find();

        if ($result) {
            $error = 'Id:' . $result->getAttr('delivery_item_id') . ' "';
            $error .= $result->getAttr('name') . '"正在被配送方式使用';
            return $this->setError($error);
        }

        $map[] = ['delivery_item_id', 'in', $data['delivery_item_id']];
        self::update(['is_delete' => 1], $map);

        return true;
    }

    /**
     * 获取一个快递公司
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getCompanyItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $map[] = ['delivery_item_id', '=', $data['delivery_item_id']];
        $map[] = ['is_delete', '=', 0];

        $result = $this->where($map)->find();
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 查询快递公司编码是否已存在
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function uniqueCompanyCode(array $data)
    {
        if (!$this->validateData($data, 'unique')) {
            return false;
        }

        $map[] = ['code', '=', $data['code']];
        $map[] = ['type', '=', $data['type']];
        $map[] = ['is_delete', '=', 0];
        !isset($data['exclude_id']) ?: $map['delivery_item_id'] = ['<>', $data['exclude_id']];

        if (self::checkUnique($map)) {
            return $this->setError('快递公司编码已存在');
        }

        return true;
    }

    /**
     * 获取快递公司列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getCompanyList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['name']) ?: $map[] = ['name', 'like', '%' . $data['name'] . '%'];
        empty($data['code']) ?: $map[] = ['code', '=', $data['code']];
        is_empty_parm($data['type']) ?: $map[] = ['type', '=', $data['type']];
        isset($data['company_all']) && $data['company_all'] == 1 ?: $map[] = ['is_delete', '=', 0];

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['type' => 'asc'])
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }

    /**
     * 获取快递公司选择列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getCompanySelect(array $data)
    {
        if (!$this->validateData($data, 'select')) {
            return false;
        }

        $map[] = ['is_delete', '=', 0];
        is_empty_parm($data['type']) ?: $map[] = ['type', '=', $data['type']];

        return $this->setDefaultOrder(['delivery_item_id' => 'asc'])
            ->where($map)
            ->withSearch(['order'], $data)
            ->select()
            ->toArray();
    }

    /**
     * 根据快递单号识别快递公司
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getCompanyRecognise(array $data)
    {
        if (!$this->validateData($data, 'recognise')) {
            return false;
        }

        // 请求正文内容
        $requestData = ['LogisticCode' => $data['code']];
        $requestData = json_encode($requestData, JSON_UNESCAPED_UNICODE);

        // 请求系统参数
        $postData = [
            'RequestData' => urlencode($requestData),
            'EBusinessID' => Config::get('careyshop.delivery_dist.api_id'),
            'RequestType' => '2002',
            'DataSign'    => \app\common\service\DeliveryDist::getCallbackSign($requestData),
            'DataType'    => '2',
        ];

        $result = \util\Http::httpPost(self::KDNIAO_URL, $postData);
        $result = json_decode($result, true);

        if (!isset($result['Success']) || true != $result['Success']) {
            return $this->setError($result['Code']);
        }

        return [
            'logistic_code' => $result['LogisticCode'],
            'shippers'      => \app\common\service\DeliveryDist::snake($result['Shippers']),
        ];
    }

    /**
     * 复制一个快递公司为"热门类型"
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function copyCompanyHot(array $data)
    {
        if (!$this->validateData($data, 'hot')) {
            return false;
        }

        // 获取主数据
        $map = [
            ['delivery_item_id', '=', $data['delivery_item_id']],
            ['is_delete', '=', 0],
        ];

        $result = $this->where($map)->find();
        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        $map = [
            ['code', '=', $result->getAttr('code')],
            ['type', '=', 0],
            ['is_delete', '=', 0],
        ];

        if (self::checkUnique($map)) {
            return $this->setError('该快递公司已在热门列表中');
        }

        $result->setAttr('type', 0);
        $newData = $result->hidden(['delivery_item_id'])->toArray();

        return DeliveryItem::create($newData)->toArray();
    }
}
