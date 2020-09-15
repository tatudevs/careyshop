<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    配送轨迹模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/27
 */

namespace app\common\model;

use app\common\service\DeliveryDist as Dist;
use think\facade\Config;
use util\Http;

class DeliveryDist extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'delivery_dist_id';

    /**
     * 快递鸟查询URL
     * @var string
     */
    const KDNIAO_URL = 'http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx';

    /**
     * 轨迹订阅URL
     * @var string
     */
    const FOLLOW_URL = 'http://api.kdniao.com/api/dist';

    /**
     * 是否需要自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 隐藏属性
     * @var array
     */
    protected $hidden = [
        'delivery_item_id',
        'delivery_code',
    ];

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'delivery_dist_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'delivery_dist_id' => 'integer',
        'user_id'          => 'integer',
        'delivery_item_id' => 'integer',
        'state'            => 'integer',
        'is_sub'           => 'integer',
        'trace'            => 'array',
    ];

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
     * hasOne cs_user
     * @access public
     * @return mixed
     */
    public function getUser()
    {
        return $this
            ->hasOne(User::class, 'user_id', 'user_id')
            ->joinType('left');
    }

    /**
     * 关联查询NULL处理
     * @param mixed $value
     * @return \stdClass
     */
    public function getGetUserAttr($value)
    {
        return is_null($value) ? new \stdClass : $value;
    }

    /**
     * 添加一条配送轨迹
     * @access public
     * @param array $data 外部数据
     * @return false|array
     * @throws
     */
    public function addDeliveryDistItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        if (empty($data['delivery_id']) && empty($data['delivery_item_id'])) {
            return $this->setError('配送方式编号或快递公司编号不能为空');
        }

        if (!empty($data['delivery_id']) && !empty($data['delivery_item_id'])) {
            return $this->setError('配送方式编号与快递公司编号不能同时存在');
        }

        // 避免无关字段及设置部分字段
        $data['trace'] = [];
        $data['user_id'] = is_client_admin() ? $data['client_id'] : get_client_id();
        unset($data['delivery_dist_id'], $data['delivery_code'], $data['state']);

        $deliveryResult = null;
        if (!empty($data['delivery_id'])) {
            // 根据配送方式编号获取快递公司编码
            $deliveryResult = Delivery::alias('d')
                ->field('i.delivery_item_id,i.code')
                ->join('delivery_item i', 'i.delivery_item_id = d.delivery_item_id')
                ->where('d.delivery_id', '=', $data['delivery_id'])
                ->find();
        } else if (!empty($data['delivery_item_id'])) {
            $deliveryResult = DeliveryItem::find($data['delivery_item_id']);
        }

        if (!$deliveryResult) {
            return $this->setError('配送方式数据不存在');
        }

        // 对数据再次进行处理
        $data['delivery_code'] = $deliveryResult->getAttr('code');
        $data['delivery_item_id'] = $deliveryResult->getAttr('delivery_item_id');
        $data['is_sub'] = Config::get('careyshop.delivery_dist.is_sub', 0);
        unset($data['client_id'], $data['delivery_id']);

        // 配送轨迹存在则直接返回
        $map[] = ['user_id', '=', $data['user_id']];
        $map[] = ['order_code', '=', $data['order_code']];
        $map[] = ['delivery_code', '=', $data['delivery_code']];
        $map[] = ['logistic_code', '=', $data['logistic_code']];

        $distResult = $this->where($map)->find();
        if (!is_null($distResult)) {
            return $distResult->toArray();
        }

        // 如开启订阅配送轨迹则向第三方订阅
        if (1 == $data['is_sub']) {
            // 请求正文内容
            $requestData = [
                'ShipperCode'  => $deliveryResult->getAttr('code'),
                'LogisticCode' => $data['logistic_code'],
                'OrderCode'    => $data['order_code'],
                'Remark'       => 'CareyShop',
            ];
            $requestData = json_encode($requestData, JSON_UNESCAPED_UNICODE);

            // 请求系统参数
            $postData = [
                'RequestData' => urlencode($requestData),
                'EBusinessID' => Config::get('careyshop.delivery_dist.api_id'),
                'RequestType' => '1008',
                'DataSign'    => Dist::getCallbackSign($requestData),
                'DataType'    => '2',
            ];

            $result = Http::httpPost(self::FOLLOW_URL, $postData);
            $result = json_decode($result, true);

            // todo 快递格式不允许订阅时如何处理?
            if (!isset($result['Success']) || true != $result['Success']) {
                return $this->setError(isset($result['Reason']) ? $result['Reason'] : '订阅配送轨迹出错');
            }
        }

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 接收推送过来的配送轨迹
     * @access public
     * @param array $data 外部数据
     * @return false|array
     */
    public function putDeliveryDistData(array $data)
    {
        $result['callback_return_type'] = 'json';
        $result['is_callback'] = [
            'EBusinessID' => Config::get('careyshop.delivery_dist.api_id'),
            'UpdateTime'  => date('Y-m-d H:i:s'),
            'Success'     => true,
        ];

        if (empty($data['RequestData'])) {
            $result['is_callback']['Success'] = false;
            $result['is_callback']['Reason'] = '请提交推送内容';
            return $result;
        }

        // 目前只有101配送轨迹订阅,如有其他业务则进行派分
        if (!isset($data['RequestType']) || '101' != $data['RequestType']) {
            $result['is_callback']['Success'] = false;
            $result['is_callback']['Reason'] = '请求指令错误';
            return $result;
        }

        // 需要把HTML实体转换为字符
        $requestData = htmlspecialchars_decode($data['RequestData']);
        if (Dist::getCallbackSign($requestData) != urlencode($data['DataSign'])) {
            $result['is_callback']['Success'] = false;
            $result['is_callback']['Reason'] = '请求非法';
            return $result;
        }

        $requestData = json_decode($requestData, true);
        foreach ($requestData['Data'] as $value) {
            if (true == $value['Success']) {
                $update = [
                    'state' => $value['State'],
                    'trace' => Dist::snake($value['Traces']),
                ];

                $map[] = ['delivery_code', '=', $value['ShipperCode']];
                $map[] = ['logistic_code', '=', $value['LogisticCode']];
                self::update($update, $map);
            }
        }

        return $result;
    }

    /**
     * 查询实时物流轨迹
     * @access private
     * @param string $deliveryCode 快递公司编码
     * @param string $logisticCode 快递单号
     * @return false|array
     */
    private function getOrderTracesByJson(string $deliveryCode, string $logisticCode)
    {
        // 请求正文内容
        $requestData = ['ShipperCode' => $deliveryCode, 'LogisticCode' => $logisticCode];
        $requestData = json_encode($requestData, JSON_UNESCAPED_UNICODE);

        // 请求系统参数
        $postData = [
            'RequestData' => urlencode($requestData),
            'EBusinessID' => Config::get('careyshop.delivery_dist.api_id'),
            'RequestType' => '1002',
            'DataSign'    => Dist::getCallbackSign($requestData),
            'DataType'    => '2',
        ];

        $result = Http::httpPost(self::KDNIAO_URL, $postData);
        $result = json_decode($result, true);

        if (!isset($result['Success']) || true != $result['Success']) {
            return $this->setError($result['Reason']);
        }

        return [
            'state' => $result['State'],
            'trace' => Dist::snake($result['Traces']),
        ];
    }

    /**
     * 根据快递单号即时查询配送轨迹
     * @access public
     * @param array $data 外部数据
     * @return array|bool
     */
    public function getDeliveryDistTrace(array $data)
    {
        if (!$this->validateData($data, 'trace')) {
            return false;
        }

        return $this->getOrderTracesByJson($data['delivery_code'], $data['logistic_code']);
    }

    /**
     * 根据流水号获取配送轨迹
     * @access public
     * @param array $data 外部数据
     * @return false|array
     * @throws
     */
    public function getDeliveryDistCode(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        // 搜索条件
        $map[] = ['delivery_dist.order_code', '=', $data['order_code']];
        empty($data['logistic_code']) ?: $map[] = ['delivery_dist.logistic_code', '=', $data['logistic_code']];
        empty($data['exclude_code']) ?: $map[] = ['delivery_dist.logistic_code', 'not in', $data['exclude_code']];
        is_client_admin() ?: $map[] = ['delivery_dist.user_id', '=', get_client_id()];

        // 关联查询
        $with['getDeliveryItem'] = ['name', 'code'];
        !is_client_admin() ?: $with['getUser'] = ['username', 'level_icon', 'head_pic', 'nickname'];

        $result = $this->withJoin($with)->where($map)->select()->toArray();
        if (empty($result)) {
            return [];
        }

        $update = [];
        foreach ($result as $key => $value) {
            // 忽略已订阅或已签收的配送轨迹
            if (1 === $value['is_sub'] || 3 === $value['state']) {
                continue;
            }

            $track = $this->getOrderTracesByJson($value['getDeliveryItem']['code'], $value['logistic_code']);
            if (false !== $track) {
                $result[$key]['state'] = (int)$track['state'];
                $result[$key]['trace'] = $track['trace'];

                // 如已签收则更新数据
                if (3 == $track['state']) {
                    $update[] = [
                        'delivery_dist_id' => $value['delivery_dist_id'],
                        'state'            => $track['state'],
                        'trace'            => $track['trace'],
                    ];
                }
            }
        }

        if (!empty($update)) {
            $this->saveAll($update);
        }

        self::keyToSnake(['getDeliveryItem', 'getUser'], $result);
        return $result;
    }

    /**
     * 获取配送轨迹列表
     * @access public
     * @param array $data 外部数据
     * @return false|array
     * @throws
     */
    public function getDeliveryDistList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['order_code']) ?: $map[] = ['delivery_dist.order_code', '=', $data['order_code']];
        empty($data['logistic_code']) ?: $map[] = ['delivery_dist.logistic_code', '=', $data['logistic_code']];
        is_empty_parm($data['is_sub']) ?: $map[] = ['delivery_dist.is_sub', '=', $data['is_sub']];

        if (!empty($data['timeout'])) {
            $map[] = ['delivery_dist.state', '<>', 3];
            $map[] = ['delivery_dist.create_time', '<=', time() - ($data['timeout'] * 86400)];
        } else {
            is_empty_parm($data['state']) ?: $map[] = ['delivery_dist.state', '=', $data['state']];
        }

        if (is_client_admin() && !empty($data['account'])) {
            $map[] = ['getUser.username|getUser.nickname', '=', $data['account']];
        }

        // 关联查询
        $with['getDeliveryItem'] = ['name', 'code'];
        !is_client_admin() ?: $with['getUser'] = ['username', 'level_icon', 'head_pic', 'nickname'];

        $result['total_result'] = $this->withJoin($with)->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['delivery_dist_id' => 'desc'])
            ->withoutField(empty($data['is_trace']) ? 'trace' : null) // 默认不返回"trace"字段
            ->withJoin($with)
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        self::keyToSnake(['getDeliveryItem', 'getUser'], $result['items']);
        return $result;
    }
}
