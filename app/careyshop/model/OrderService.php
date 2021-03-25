<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    售后服务模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/9/4
 */

namespace app\careyshop\model;

use think\facade\Config;

class OrderService extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'order_service_id';

    /**
     * 是否需要自动写入时间戳
     * @var bool|string
     */
    protected $autoWriteTimestamp = true;

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'order_service_id',
        'order_no',
        'order_goods_id',
        'user_id',
        'qty',
        'type',
        'goods_status',
        'refund_fee',
        'create_time',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'order_service_id' => 'integer',
        'order_goods_id'   => 'integer',
        'user_id'          => 'integer',
        'admin_id'         => 'integer',
        'qty'              => 'integer',
        'type'             => 'integer',
        'goods_status'     => 'integer',
        'image'            => 'array',
        'status'           => 'integer',
        'is_return'        => 'integer',
        'refund_fee'       => 'float',
        'refund_detail'    => 'array',
        'delivery_fee'     => 'float',
        'admin_event'      => 'integer',
        'user_event'       => 'integer',
    ];

    /**
     * belongsTo cs_order
     * @access public
     * @return mixed
     */
    public function getOrder()
    {
        return $this->belongsTo(Order::class, 'order_no', 'order_no');
    }

    /**
     * belongsTo cs_order_goods
     * @access public
     * @return mixed
     */
    public function getOrderGoods()
    {
        return $this->belongsTo(OrderGoods::class);
    }

    /**
     * belongsTo cs_order_refund
     * @access public
     * @return mixed
     */
    public function getOrderRefund()
    {
        return $this
            ->belongsTo(OrderRefund::class, 'refund_no', 'refund_no')
            ->joinType('left');
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
     * hasOne cs_admin
     * @access public
     * @return mixed
     */
    public function getAdmin()
    {
        return $this
            ->hasOne(Admin::class, 'admin_id', 'admin_id')
            ->joinType('left');
    }

    /**
     * hasMany cs_service_log
     * @access public
     * @return mixed
     */
    public function getServiceLog()
    {
        return $this
            ->hasMany(ServiceLog::class)
            ->order(['service_log_id' => 'desc']);
    }

    /**
     * 关联查询NULL处理
     * @param Object $value
     * @return mixed
     */
    public function getGetOrderRefundAttr($value = null)
    {
        return is_null($value) ? new \stdClass : $value;
    }

    /**
     * 关联查询NULL处理
     * @param Object $value
     * @return mixed
     */
    public function getGetUserAttr($value = null)
    {
        return is_null($value) ? new \stdClass : $value;
    }

    /**
     * 关联查询NULL处理
     * @param Object $value
     * @return mixed
     */
    public function getGetAdminAttr($value = null)
    {
        return is_null($value) ? new \stdClass : $value;
    }

    /**
     * 生成唯一售后单号
     * @access private
     * @return string
     */
    private function getServiceNo(): string
    {
        do {
            $serviceNo = get_order_no('SH_');
        } while (self::checkUnique(['service_no' => $serviceNo]));

        return $serviceNo;
    }

    /**
     * 添加售后服务日志
     * @access public
     * @param array  $serviceData 售后单数据
     * @param string $comment     备注
     * @param string $desc        描述
     * @return bool
     */
    public function addServiceLog(array $serviceData, string $comment, string $desc): bool
    {
        $data = [
            'order_service_id' => $serviceData['order_service_id'],
            'service_no'       => $serviceData['service_no'],
            'comment'          => $comment,
            'description'      => $desc,
        ];

        $serviceDb = new ServiceLog();
        if (!$serviceDb->addServiceLogItem($data)) {
            return $this->setError($serviceDb->getError());
        }

        if (is_client_admin()) {
            $saveData['user_event'] = 1;
            $saveData['admin_event'] = 0;
        } else {
            $saveData['admin_event'] = 1;
        }

        $map[] = ['service_no', '=', $data['service_no']];
        self::update($saveData, $map);

        return true;
    }

    /**
     * 根据订单号撤销符合条件的售后单(内部调用)
     * @access public
     * @param string $orderNo 订单号
     * @param string $type    撤销类型
     * @return bool
     * @throws
     */
    public function inCancelOrderService(string $orderNo, string $type): bool
    {
        if (!in_array($type, ['delivery', 'complete'])) {
            return false;
        }

        // 搜索条件
        $map[] = ['order_no', '=', $orderNo];
        $map[] = ['status', 'not in', '2,5,6'];

        // 过滤不需要的字段
        $field = [
            'reason', 'description', 'image', 'address', 'consignee',
            'zipcode', 'mobile', 'logistic_code',
        ];

        // 关联查询
        $with['get_order_goods'] = function ($query) {
            $query->field('order_goods_id,goods_name,goods_id,goods_image,key_value,qty,is_service,status');
        };

        $with['get_order_refund'] = function ($query) {
            $query->field('order_refund_id,refund_no,status');
        };

        // 准备初始化数据
        $logData = [];
        $comment = $type == 'delivery' ? '由于商品已发货' : '由于您已确认收货';

        // 查询结果,无处理数据直接返回
        $result = $this->with($with)->withoutField($field)->where($map)->select();
        if ($result->isEmpty()) {
            return true;
        }

        // 开启事务
        $this->startTrans();

        try {
            foreach ($result as $value) {
                // 修改售后服务单
                if (false === $value->save(['status' => 5, 'result' => '撤销申请'])) {
                    throw new \Exception($value->getError());
                }

                // 修改订单商品售后状态
                $goodsDb = $value->getAttr('get_order_goods');
                if ($goodsDb instanceof OrderGoods && $goodsDb->getAttr('is_service') === 1) {
                    if (false === $goodsDb->save(['is_service' => 0])) {
                        throw new \Exception($goodsDb->getError());
                    }
                }

                // 修改退款申请状态
                $refundDb = $value->getAttr('get_order_refund');
                if ($refundDb instanceof OrderRefund && $refundDb->getAttr('status') === 0) {
                    $refundData = ['status' => 3, 'out_trade_msg' => $comment . '，本次退款申请撤销。'];
                    if (false === $refundDb->save($refundData)) {
                        throw new \Exception($refundDb->getError());
                    }
                }

                $logData[] = [
                    'order_service_id' => $value->getAttr('order_service_id'),
                    'service_no'       => $value->getAttr('service_no'),
                    'action'           => get_client_name(),
                    'client_type'      => get_client_type(),
                    'comment'          => $comment . '，本次售后服务申请撤销。',
                    'description'      => '撤销申请',
                ];
            }

            // 写入操作日志
            $serviceLogDb = new ServiceLog();
            $serviceLogDb->insertAll($logData);

            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 获取订单商品可申请的售后服务
     * @access public
     * @param array $data         外部数据
     * @param null  $orderGoodsDb 订单商品模型对象
     * @param bool  $isRefundFee  是否返回退款结构
     * @return array|false
     * @throws
     */
    public function getOrderServiceGoods(array $data, &$orderGoodsDb = null, $isRefundFee = false)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 初始化可申请的售后服务 0=否 1=是 $refund=可退金额
        $refund = [
            'refund_fee'    => 0,           // 最大可退金额
            'refund_detail' => [],          // 退款明细
            'delivery_fee'  => 0,           // 包含运费
        ];

        $service = [
            'is_refund'         => 0,       // 是否可申请退款
            'is_refund_refunds' => 0,       // 是否可申请退款退货
            'is_exchange'       => 0,       // 是否可申请换货
            'is_maintain'       => 0,       // 是否可申请维修
            'order_goods'       => $refund, // 订单商品数据
        ];

        // 搜索订单商品是否存在正在进行的售后单
        $map[] = ['order_goods_id', '=', $data['order_goods_id']];
        $map[] = ['user_id', '=', get_client_id()];
        $map[] = ['status', 'in', '0,1,3,4'];

        // 订单商品存在售后单则返回单号
        $result = $this->field('service_no')->where($map)->find();
        if (!is_null($result)) {
            return ['service_no' => $result->getAttr('service_no')];
        }

        // 获取订单商品基础数据(订单、订单商品)
        $orderGoodsDb = (new OrderGoods())->getOrderGoodsItem($data, false);
        if (!$orderGoodsDb || !is_object($orderGoodsDb)) {
            return !is_null($orderGoodsDb) ? $this->setError('数据获取异常') : $service;
        }

        // 获取订单商品需要的字段
        $visible = ['order_no', 'goods_name', 'goods_id', 'qty'];
        $service['order_goods'] = array_merge($orderGoodsDb->visible($visible)->toArray(), $refund);
        unset($service['order_goods']['getOrder']);

        // 检测订单商品是否允许申请售后(可申请、已售后)
        if (!in_array($orderGoodsDb->getAttr('is_service'), [0, 2])) {
            return $service;
        }

        // 检测订单商品状态是否允许申请售后(已发、已收)
        if (!in_array($orderGoodsDb->getAttr('status'), [1, 2])) {
            return $service;
        }

        // 此"getOrder"来自"OrderGoods"模型,因此不受字段限制
        $orderDb = $orderGoodsDb->getAttr('getOrder');
        switch ($orderDb->getAttr('trade_status')) {
            case 2: // 处理已发货,未确认收货
                $service['is_refund'] = 1;
                $service['is_refund_refunds'] = 1;
                break;
            case 3: // 处理已发货,已确认收货
                $service['is_maintain'] = 1;
                $finishedTime = $orderDb->getData('finished_time') + Config::get('careyshop.service.days') * 86400;
                if ($finishedTime >= time()) {
                    $service['is_refund'] = 1;
                    $service['is_refund_refunds'] = 1;
                    $service['is_exchange'] = 1;
                }
                break;
        }

        // 获取订单商品最大可退金额
        if ($service['is_refund'] === 1 || $service['is_refund_refunds'] === 1) {
            if (!empty($data['is_refund_fee']) || $isRefundFee) {
                $isDelivery = false;
                $service['order_goods']['refund_detail'] = $this->getMaxRefundFee($orderGoodsDb, $isDelivery);
                $service['order_goods']['refund_fee'] = round(array_sum($service['order_goods']['refund_detail']), 2);
                !$isDelivery ?: $service['order_goods']['delivery_fee'] = $orderDb->getAttr('delivery_fee');
            }
        }

        return $service;
    }

    /**
     * 客服对售后服务单添加备注(顾客不可见)
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setOrderServiceRemark(array $data): bool
    {
        if (!$this->validateData($data, 'remark')) {
            return false;
        }

        $map[] = ['service_no', '=', $data['service_no']];
        self::update(['remark' => $data['remark']], $map);

        return true;
    }

    /**
     * 获取一个售后服务单
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getOrderServiceItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        // 搜索条件
        $map[] = ['service_no', '=', $data['service_no']];
        if (!is_client_admin()) {
            $map[] = ['user_id', '=', get_client_id()];
        }

        // 关联查询
        $with['getUser'] = ['user_id', 'username', 'nickname', 'level_icon', 'head_pic'];
        $with['getAdmin'] = ['admin_id', 'username', 'nickname', 'head_pic'];
        $with['getOrderGoods'] = [
            'order_goods_id', 'goods_name', 'goods_id', 'goods_image',
            'key_value', 'qty', 'is_service', 'status',
        ];

        // 过滤字段
        $field = !is_client_admin() ? 'admin_id,remark,admin_event' : '';

        // 实际查询
        $result = $this
            ->with('get_service_log')
            ->withJoin($with)
            ->withoutField($field)
            ->where($map)
            ->find();

        if (false !== $result && !is_null($result)) {
            // 隐藏不需要输出的字段
            $hidden = [
                'order_service_id',
                'getUser.user_id',
                'getAdmin.admin_id',
                'getOrderGoods.order_goods_id',
                'get_service_log.service_log_id',
                'get_service_log.order_service_id',
                'get_service_log.service_no',
            ];

            if (is_client_admin()) {
                if ($result->getAttr('admin_id') == get_client_id()) {
                    $result->setAttr('admin_event', 0);
                    $result->save();
                }
            } else {
                $result->setAttr('user_event', 0);
                $result->save();
            }

            $temp = [$result->hidden($hidden)->toArray()];
            self::keyToSnake(['getUser', 'getAdmin', 'getOrderGoods'], $temp);

            return $temp[0];
        }

        return is_null($result) ? [] : false;
    }

    /**
     * 获取售后服务单列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getOrderServiceList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        is_empty_parm($data['type']) ?: $map[] = ['type', '=', $data['type']];
        is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];

        if (!empty($data['order_code'])) {
            $map[] = ['service_no|order_no', '=', $data['order_code']];
        }

        if (!empty($data['begin_time']) && !empty($data['end_time'])) {
            $map[] = ['create_time', 'between time', [$data['begin_time'], $data['end_time']]];
        }

        if (is_client_admin()) {
            if (!empty($data['account'])) {
                $userId = User::where('username', '=', $data['account'])->value('user_id', 0);
                $map[] = ['user_id', '=', $userId];
            }

            if (!empty($data['my_service'])) {
                $map[] = ['admin_id', '=', get_client_id()];
            }
        } else {
            $map[] = ['user_id', '=', get_client_id()];
        }

        if (!empty($data['new_event'])) {
            $map[] = [is_client_admin() ? 'admin_event' : 'user_event', '=', 1];
        }

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 过滤字段
        $field = ['description', 'image', 'refund_detail', 'refund_no'];
        is_client_admin() ?: array_push($field, 'admin_id', 'remark', 'admin_event');

        // 关联查询
        $with['get_order_goods'] = function ($query) {
            $query->field('order_goods_id,goods_name,goods_id,goods_image,key_value,qty,is_service,status');
        };

        if (is_client_admin()) {
            $with['get_user'] = function ($query) {
                $query->field('user_id,username,nickname,level_icon,head_pic');
            };

            $with['get_admin'] = function ($query) {
                $query->field('admin_id,username,nickname,head_pic');
            };
        }

        // 隐藏不需要输出的字段
        $hidden = [
            'order_service_id',
            'get_user.user_id',
            'get_admin.admin_id',
            'get_order_goods.order_goods_id',
        ];

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['order_service_id' => 'desc'])
            ->with($with)
            ->withoutField($field)
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->hidden($hidden)
            ->toArray();

        return $result;
    }

    /**
     * 检测售后服务单数量是否已大于等于订单商品
     * @access private
     * @param string $orderNo 订单号
     * @return bool
     */
    private function isServiceEgtOrderGoods(string $orderNo): bool
    {
        $map[] = ['order_no', '=', $orderNo];
        $map[] = ['type', 'in', '0,1'];
        $map[] = ['status', 'not in', '2,5']; // 不包括"已拒绝","已撤销"

        $serviceCount = $this->where($map)->group('order_goods_id')->count();
        $goodsCount = OrderGoods::where('order_no', '=', $orderNo)->count();

        return $serviceCount + 1 >= $goodsCount;
    }

    /**
     * 获取订单商品最大可退金额
     * @access private
     * @param object  $orderGoodsDb 订单商品模型对象
     * @param bool   &$isDelivery   是否退回运费
     * @return int[]
     */
    private function getMaxRefundFee(object $orderGoodsDb, bool &$isDelivery): array
    {
        $data = [
            'money_amount'    => 0, // 余额
            'integral_amount' => 0, // 积分
            'card_amount'     => 0, // 购物卡
            'payment_amount'  => 0, // 支付
        ];

        if (!isset($orderGoodsDb) || !is_object($orderGoodsDb)) {
            return $data;
        }

        // 获取订单模型对象(此"getOrder"来自"OrderGoods"模型,因此不受字段限制)
        $orderDb = $orderGoodsDb->getAttr('getOrder');

        // 获取各项实付金额
        $data['money_amount'] = $orderDb->getAttr('use_money');
        $data['integral_amount'] = $orderDb->getAttr('use_integral');
        $data['card_amount'] = $orderDb->getAttr('use_card');
        $data['payment_amount'] = PaymentLog::getPaymentLogValue($orderDb->getAttr('payment_no'));

        // 存在运费时,非最后订单商品一律按比分比扣除运费
        $totalAmount = array_sum($data);
        $deliveryFee = $orderDb->getAttr('delivery_fee');

        if ($deliveryFee > 0 && $totalAmount > 0) {
            $isDelivery = $this->isServiceEgtOrderGoods($orderDb->getAttr('order_no'));
            foreach ($data as $key => $value) {
                $data[$key] -= ($deliveryFee / $totalAmount) * $value;
            }

            unset($key, $value);
        }

        // 计算订单商品可退百分比
        $orderScale = $orderGoodsDb->getAttr('shop_price') * $orderGoodsDb->getAttr('qty');
        $orderScale /= $orderDb->getAttr('goods_amount');

        // 计算实际可退金额
        $tempData = $data;
        $totalAmount = array_sum($tempData);

        foreach ($data as $key => $value) {
            $data[$key] = $value * $orderScale;
            !$isDelivery ?: $data[$key] += ($tempData[$key] / $totalAmount) * $deliveryFee;
            $data[$key] = round($data[$key], 2);
        }

        return $data;
    }

    /**
     * 添加一个维修或换货售后服务单
     * @access private
     * @param array  $data 外部数据
     * @param string $type 售后类型 maintain或exchange
     * @return array|false
     */
    private function addMaintainOfExchange(array &$data, string $type)
    {
        if (!$this->validateData($data, 'maintain')) {
            return false;
        }

        if ($type !== 'maintain' && $type !== 'exchange') {
            return $this->setError('售后类型只能为 maintain 或 exchange');
        }

        $orderGoodsDb = null;
        $result = $this->getOrderServiceGoods($data, $orderGoodsDb);

        if (false === $result) {
            return false;
        }

        if (isset($result['service_no'])) {
            return $this->setError('订单商品存在尚未完成的售后服务');
        }

        if ($result['is_' . $type] !== 1) {
            return $this->setError('订单商品不满足申请该服务的条件');
        }

        if ($data['qty'] > $result['order_goods']['qty']) {
            return $this->setError('最大允许申请数量为 ' . $result['order_goods']['qty']);
        }

        // 售后单入库数据准备
        $serviceData = [
            'service_no'     => $this->getServiceNo(),
            'order_no'       => $result['order_goods']['order_no'],
            'order_goods_id' => $data['order_goods_id'],
            'user_id'        => get_client_id(),
            'qty'            => $result['order_goods']['qty'],
            'type'           => $type === 'maintain' ? 3 : 2,
            'reason'         => $data['reason'],
            'description'    => !empty($data['description']) ? $data['description'] : '',
            'goods_status'   => 2,
            'image'          => !empty($data['image']) ? $data['image'] : [],
        ];

        // 开启事务
        $this->startTrans();

        try {
            // 写入售后服务单
            if (false === $this->save($serviceData)) {
                throw new \Exception($this->getError());
            }

            // 修改订单商品售后状态
            if (false === $orderGoodsDb->save(['is_service' => 1])) {
                throw new \Exception($orderGoodsDb->getError());
            }

            // 写入售后服务单日志
            if (!$this->addServiceLog($this->toArray(), '发起申请售后服务。', '申请售后')) {
                throw new \Exception($this->getError());
            }

            $this->commit();
            return $this->hidden(['order_service_id', 'admin_event'])->toArray();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 添加一个维修售后服务单
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addOrderServiceMaintain(array $data)
    {
        return $this->addMaintainOfExchange($data, 'maintain');
    }

    /**
     * 添加一个换货售后服务单
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addOrderServiceExchange(array $data)
    {
        return $this->addMaintainOfExchange($data, 'exchange');
    }

    /**
     * 添加一个仅退款或退货退款售后服务单
     * @access private
     * @param array  $data 外部数据
     * @param string $type 售后类型 refund或refund_refunds
     * @return array|false
     */
    private function addServiceRefund(array &$data, string $type)
    {
        if (!$this->validateData($data, $type)) {
            return false;
        }

        if ($type !== 'refund' && $type !== 'refund_refunds') {
            return $this->setError('售后类型只能为 refund 或 refund_refunds');
        }

        $orderGoodsDb = null;
        $result = $this->getOrderServiceGoods($data, $orderGoodsDb, true);

        if (false === $result) {
            return false;
        }

        if (isset($result['service_no'])) {
            return $this->setError('订单商品存在尚未完成的售后服务');
        }

        if ($result['is_' . $type] !== 1) {
            return $this->setError('订单商品不满足申请该服务的条件');
        }

        if (bccomp($data['refund_fee'], $result['order_goods']['refund_fee'], 2) === 1) {
            return $this->setError('最大允许退款金额为 ' . $result['order_goods']['refund_fee']);
        }

        // 按申请额计算实际退款结构
        $totalAmount = array_sum($result['order_goods']['refund_detail']);
        foreach ($result['order_goods']['refund_detail'] as &$value) {
            $value = round(($data['refund_fee'] / $totalAmount) * $value, 2);
        }

        unset($value);

        // 售后单入库数据准备
        $serviceData = [
            'service_no'     => $this->getServiceNo(),
            'order_no'       => $result['order_goods']['order_no'],
            'order_goods_id' => $data['order_goods_id'],
            'user_id'        => get_client_id(),
            'type'           => $type === 'refund' ? 0 : 1,
            'reason'         => $data['reason'],
            'description'    => !empty($data['description']) ? $data['description'] : '',
            'goods_status'   => $type === 'refund' ? $data['goods_status'] : 2,
            'image'          => !empty($data['image']) ? $data['image'] : [],
            'refund_fee'     => $data['refund_fee'],
            'refund_detail'  => $result['order_goods']['refund_detail'],
            'delivery_fee'   => $result['order_goods']['delivery_fee'],
        ];

        // 开启事务
        $this->startTrans();

        try {
            // 写入售后服务单
            if (false === $this->save($serviceData)) {
                throw new \Exception($this->getError());
            }

            // 修改订单商品售后状态
            if (false === $orderGoodsDb->save(['is_service' => 1])) {
                throw new \Exception($orderGoodsDb->getError());
            }

            // 写入售后服务单日志
            if (!$this->addServiceLog($this->toArray(), '发起申请售后服务。', '申请售后')) {
                throw new \Exception($this->getError());
            }

            $this->commit();
            return $this->hidden(['order_service_id', 'admin_event'])->toArray();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 添加一个仅退款售后服务单
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addOrderServiceRefund(array $data)
    {
        return $this->addServiceRefund($data, 'refund');
    }

    /**
     * 添加一个退款退货售后服务单
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addOrderServiceRefunds(array $data)
    {
        return $this->addServiceRefund($data, 'refund_refunds');
    }

    /**
     * 添加一条售后服务单留言
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function addOrderServiceMessage(array $data): bool
    {
        if (!$this->validateData($data, 'message')) {
            return false;
        }

        $map[] = ['service_no', '=', $data['service_no']];
        is_client_admin() ?: $map[] = ['user_id', '=', get_client_id()];

        $result = $this->where($map)->find();
        if (is_null($result)) {
            return $this->setError('售后服务单不存在');
        }

        // 写入售后服务单日志
        $desc = is_client_admin() ? '商家留言' : '买家留言';
        if ($this->addServiceLog($result->toArray(), $data['message'], $desc)) {
            return true;
        }

        return false;
    }

    /**
     * 同意(接收)一个售后服务单
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setOrderServiceAgree(array $data)
    {
        if (!$this->validateData($data, 'agree')) {
            return false;
        }

        $result = $this->where('service_no', '=', $data['service_no'])->find();
        if (is_null($result)) {
            return $this->setError('售后服务单不存在');
        }

        $adminId = $result->getAttr('admin_id');
        if ($adminId > 0 && $adminId != get_client_id()) {
            $nickname = $result->getAttr('get_admin');
            return $this->setError(($nickname instanceof Admin ? $nickname['nickname'] : '其他人员') . '已在处理此售后单');
        }

        if ($result->getAttr('status') !== 0) {
            return $this->setError('售后服务单当前状态不允许设置');
        }

        // 开启事务
        $result::startTrans();

        try {
            // 更新主数据
            if (false === $result->save(['status' => 1, 'admin_id' => get_client_id()])) {
                throw new \Exception($result->getError());
            }

            $returnData = $result->toArray();
            $comment = '商家已同意处理此笔售后服务单。';

            // 写入售后服务单日志
            if (!$this->addServiceLog($returnData, $comment, '同意售后')) {
                throw new \Exception($this->getError());
            }

            $result::commit();
            return $returnData;
        } catch (\Exception $e) {
            $result::rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 拒绝一个售后服务单
     * @access public
     * @param array $data 外部数据
     * @return false|mixed
     * @throws
     */
    public function setOrderServiceRefused(array $data): bool
    {
        if (!$this->validateData($data, 'refused')) {
            return false;
        }

        // 关联查询
        $with['getOrderGoods'] = [
            'order_goods_id', 'goods_name', 'goods_id', 'goods_image',
            'key_value', 'qty', 'is_service', 'status',
        ];

        $result = $this
            ->withJoin($with)
            ->where('order_service.service_no', '=', $data['service_no'])
            ->find();

        if (is_null($result)) {
            return $this->setError('售后服务单不存在');
        }

        if ($result->getAttr('status') !== 0) {
            return $this->setError('售后服务单当前状态不允许设置');
        }

        // 开启事务
        $result::startTrans();

        try {
            // 更新主数据
            $saveData = ['status' => 2, 'result' => $data['result']];
            $adminId = $result->getAttr('admin_id');

            if ($adminId <= 0) {
                $saveData['admin_id'] = get_client_id();
            }

            if (false === $result->save($saveData)) {
                throw new \Exception($this->getError());
            }

            // 更新订单商品售后状态
            $goodsDb = $result->getAttr('getOrderGoods');
            if (false === $goodsDb->save(['is_service' => 2])) {
                throw new \Exception($goodsDb->getError());
            }

            $returnData = [$result->toArray()];
            self::keyToSnake(['getOrderGoods'], $returnData);
            $comment = '商家已拒绝售后服务，如有需要您可以再次申请。';

            // 写入售后服务单日志
            if (!$this->addServiceLog($returnData[0], $comment, '拒绝售后')) {
                throw new \Exception($this->getError());
            }

            $result::commit();
            return $returnData[0];
        } catch (\Exception $e) {
            $result::rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 设置退换货、维修商品是否寄还商家
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function setOrderServiceSendback(array $data): bool
    {
        if (!$this->validateData($data, 'sendback')) {
            return false;
        }

        $result = $this->where('service_no', '=', $data['service_no'])->find();
        if (is_null($result)) {
            return $this->setError('售后服务单不存在');
        }

        if ($result->getAttr('is_return') === $data['is_return']) {
            return true;
        }

        if ($result->getAttr('type') === 0) {
            return $this->setError('该售后服务单类型不允许设置');
        }

        if ($result->getAttr('status') !== 1) {
            return $this->setError('售后服务单当前状态不允许设置');
        }

        if (!empty($result->getAttr('logistic_code'))) {
            return $this->setError('买家已寄件，不允许设置');
        }

        // 开启事务
        $result::startTrans();

        try {
            if (false === $result->save(['is_return' => $data['is_return']])) {
                throw new \Exception($this->getError());
            }

            $comment = $data['is_return'] == 0 ?
                '商家取消了商品寄回的请求。' :
                '请按商家收件地址将商品寄出，填写快递单号、并填写您的收件地址。' . PHP_EOL .
                '收件地址：' . Config::get('careyshop.service.address', '') . PHP_EOL .
                '收件人：' . Config::get('careyshop.service.consignee', '') . PHP_EOL .
                '电话：' . Config::get('careyshop.service.mobile', '') . PHP_EOL .
                '邮编：' . Config::get('careyshop.service.zipcode', '');

            // 写入售后服务单日志
            if (!$this->addServiceLog($result->toArray(), $comment, '商品寄回')) {
                throw new \Exception($this->getError());
            }

            $result::commit();
            return true;
        } catch (\Exception $e) {
            $result::rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 买家填写快递单号或寄回信息
     * @access private
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    private function setLogisticCode(array &$data): bool
    {
        $map[] = ['service_no', '=', $data['service_no']];
        $map[] = ['user_id', '=', get_client_id()];

        $result = $this->where($map)->find();
        if (is_null($result)) {
            return $this->setError('售后服务单不存在');
        }

        if ($result->getAttr('type') === 0) {
            return $this->setError('该售后服务单类型不允许填写');
        }

        if ($result->getAttr('type') === 1 && $result->getAttr('goods_status') !== 2) {
            return $this->setError('售后服务单中的商品未收到货，不需要填写');
        }

        if ($result->getAttr('status') !== 1) {
            return $this->setError('售后服务单当前状态不允许填写');
        }

        if ($result->getAttr('is_return') !== 1) {
            return $this->setError('商家未要求寄回待售后商品');
        }

        // 开启事务
        $result::startTrans();

        try {
            // 添加一条配送记录
            $distData = [
                'client_id'        => $result->getAttr('user_id'),
                'order_code'       => $result->getAttr('service_no'),
                'delivery_item_id' => $data['delivery_item_id'],
                'logistic_code'    => $data['logistic_code'],
                'customer_name'    => Config::get('careyshop.service.mobile'),
            ];

            $distDb = new DeliveryDist();
            if (false === $distDb->addDeliveryDistItem($distData)) {
                throw new \Exception($distDb->getError());
            }

            // 更新售后服务单部分数据,并准备允许写入的字段
            $data['status'] = 3; // 已寄件
            unset($data['order_service_id']);
            $field = ['logistic_code', 'status'];
            if (in_array($result->getAttr('type'), [2, 3])) {
                $field = array_merge($field, ['address', 'consignee', 'zipcode', 'mobile']);
            }

            if (false === $result->allowField($field)->save($data)) {
                throw new \Exception($this->getError());
            }

            // 写入售后服务单日志
            $comment = '买家已将待售后商品寄出，请注意查收！';
            if (!$this->addServiceLog($result->toArray(), $comment, '买家寄出')) {
                throw new \Exception($this->getError());
            }

            $result::commit();
            return true;
        } catch (\Exception $e) {
            $result::rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 买家上报换货、维修后的快递单号,并填写商家寄回时需要的信息
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setOrderServiceBuyer(array $data): bool
    {
        if (!$this->validateData($data, 'buyer')) {
            return false;
        }

        return $this->setLogisticCode($data);
    }

    /**
     * 买家上报退款退货后的快递单号
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setOrderServiceLogistic(array $data): bool
    {
        if (!$this->validateData($data, 'logistic')) {
            return false;
        }

        return $this->setLogisticCode($data);
    }

    /**
     * 设置一个售后服务单状态为"售后中"
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setOrderServiceAfter(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $result = $this->where('service_no', '=', $data['service_no'])->find();
        if (is_null($result)) {
            return $this->setError('售后服务单不存在');
        }

        if (!in_array($result->getAttr('status'), [1, 3])) {
            return $this->setError('售后服务单当前状态不允许设置');
        }

        // 开启事务
        $result::startTrans();

        try {
            // 更新主数据
            if (false === $result->save(['status' => 4])) {
                throw new \Exception($this->getError());
            }

            $returnData = $result->toArray();
            $comment = '商家对该售后服务单正在进行售后服务。';

            // 写入售后服务单日志
            if (!$this->addServiceLog($returnData, $comment, '售后服务')) {
                throw new \Exception($this->getError());
            }

            $result::commit();
            return $returnData;
        } catch (\Exception $e) {
            $result::rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 撤销一个售后服务单
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setOrderServiceCancel(array $data)
    {
        if (!$this->validateData($data, 'cancel')) {
            return false;
        }

        // 搜索条件
        $map[] = ['service_no', '=', $data['service_no']];
        is_client_admin() ?: $map[] = ['user_id', '=', get_client_id()];

        // 关联查询
        $with['getOrderRefund'] = ['order_refund_id', 'refund_no', 'status'];
        $with['getOrderGoods'] = [
            'order_goods_id', 'goods_name', 'goods_id', 'goods_image',
            'key_value', 'qty', 'is_service', 'status',
        ];

        $result = $this->withJoin($with)->where($map)->find();
        if (is_null($result)) {
            return $this->setError('售后服务单不存在');
        }

        if (in_array($result->getAttr('status'), [2, 5, 6])) {
            return $this->setError('售后服务单当前状态不允许设置');
        }

        // 开启事务
        $result::startTrans();

        try {
            // 更新主数据
            if (false === $result->save(['status' => 5])) {
                throw new \Exception($this->getError());
            }

            // 更新订单商品售后状态
            $goodsDb = $result->getAttr('getOrderGoods');
            if (false === $goodsDb->save(['is_service' => 2])) {
                throw new \Exception($goodsDb->getError());
            }

            // 写入售后服务单日志
            $comment = (is_client_admin() ? '商家' : '买家') . '主动撤销售后服务单';
            if (!$this->addServiceLog($result->toArray(), $comment . '。', '撤销申请')) {
                throw new \Exception($this->getError());
            }

            // 更新订单退款单状态
            if (!empty($result->getAttr('refund_no'))) {
                $refundDb = $result->getAttr('getOrderRefund');

                if ($refundDb instanceof OrderRefund && $refundDb->getAttr('status') === 0) {
                    $refundData = ['status' => 3, 'out_trade_msg' => '由于' . $comment . '，本次退款申请撤销。'];
                    if (false === $refundDb->save($refundData)) {
                        throw new \Exception($refundDb->getError());
                    }
                }
            }

            $result::commit();
            $temp = [$result->toArray()];
            self::keyToSnake(['getOrderGoods', 'getOrderRefund'], $temp);

            return $temp[0];
        } catch (\Exception $e) {
            $result::rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 追回已赠送出的所有资源(累计消费(会员等级)、积分、优惠劵)
     * @access private
     * @param array  $orderData 订单数据
     * @param string $serviceNo 售后单号
     * @return bool
     */
    private function recoverGiveResources(array $orderData, string $serviceNo): bool
    {
        // 减少累计消费金额
        $userMoneyDb = new UserMoney();
        if ($orderData['pay_amount'] > 0) {
            if (!$userMoneyDb->decTotalMoney($orderData['pay_amount'], $orderData['user_id'])) {
                return $this->setError($userMoneyDb->getError());
            }
        }

        // 减少赠送积分
        if ($orderData['give_integral'] > 0) {
            if (!$userMoneyDb->setPoints(-$orderData['give_integral'], $orderData['user_id'])) {
                return $this->setError($userMoneyDb->getError());
            }

            $txLogData = [
                'user_id'    => $orderData['user_id'],
                'type'       => Transaction::TRANSACTION_EXPENDITURE,
                'amount'     => $orderData['give_integral'],
                'balance'    => $userMoneyDb->where('user_id', '=', $orderData['user_id'])->value('points'),
                'source_no'  => $serviceNo,
                'remark'     => '退回赠送',
                'module'     => 'points',
                'to_payment' => Payment::PAYMENT_CODE_USER,
            ];

            $txDb = new Transaction();
            if (!$txDb->addTransactionItem($txLogData)) {
                return $this->setError($txDb->getError());
            }
        }

        // 作废已赠送的优惠劵
        if (!empty($orderData['give_coupon'])) {
            $mapGive[] = ['coupon_give_id', 'in', $orderData['give_coupon']];
            $mapGive[] = ['user_id', '=', $orderData['user_id']];
            $mapGive[] = ['use_time', '=', 0];

            $couponGiveDb = new CouponGive();
            if (false === $couponGiveDb->where($mapGive)->save(['is_delete' => 1])) {
                return $this->setError($couponGiveDb->getError());
            }
        }

        return true;
    }

    /**
     * 退回用户余额或积分
     * @access private
     * @param string $type      余额或积分
     * @param float  $value     值
     * @param int    $userId    账号编号
     * @param string $serviceNo 售后单号
     * @return bool
     */
    private function refundUserMoney(string $type, float $value, int $userId, string $serviceNo): bool
    {
        if ($value <= 0 || !in_array($type, ['money_amount', 'integral_amount'])) {
            return true;
        }

        $userMoneyDb = new UserMoney();
        if ($type == 'money_amount') {
            $result = $userMoneyDb->setBalance($value, $userId);
        } else {
            $result = $userMoneyDb->setPoints($value, $userId);
        }

        if (false === $result) {
            return $this->setError($userMoneyDb->getError());
        }

        $type = 'money_amount' == $type ? 'balance' : 'points';
        $txLogData = [
            'user_id'    => $userId,
            'type'       => Transaction::TRANSACTION_INCOME,
            'amount'     => $value,
            'balance'    => UserMoney::where('user_id', '=', $userId)->value($type),
            'source_no'  => $serviceNo,
            'remark'     => '售后退款',
            'module'     => $type == 'balance' ? 'money' : 'points',
            'to_payment' => Payment::PAYMENT_CODE_USER,
        ];

        $txDb = new Transaction();
        if (!$txDb->addTransactionItem($txLogData)) {
            return $this->setError($txDb->getError());
        }

        return true;
    }

    /**
     * 退回购物卡可用余额
     * @access private
     * @param float  $value     值
     * @param object $orderDb   订单模型
     * @param string $serviceNo 售后单号
     * @return bool
     */
    private function refundCardUser(float $value, object $orderDb, string $serviceNo): bool
    {
        if ($value <= 0) {
            return true;
        }

        $userId = $orderDb->getAttr('user_id');
        $number = $orderDb->getAttr('card_number');

        $cardUserDb = new CardUse();
        if (!$cardUserDb->incCardUseMoney($number, $value, $userId)) {
            return $this->setError($cardUserDb->getError());
        }

        $txLogData = [
            'user_id'     => $userId,
            'type'        => Transaction::TRANSACTION_INCOME,
            'amount'      => $value,
            'balance'     => CardUse::where(['user_id' => $userId, 'number' => $number])->value('money'),
            'source_no'   => $serviceNo,
            'remark'      => '售后退款',
            'module'      => 'card',
            'to_payment'  => Payment::PAYMENT_CODE_CARD,
            'card_number' => $number,
        ];

        $txDb = new Transaction();
        if (!$txDb->addTransactionItem($txLogData)) {
            return $this->setError($txDb->getError());
        }

        return true;
    }

    /**
     * 原路退回在线支付
     * @access private
     * @param float  $value     值
     * @param array  $orderData 订单数据
     * @param object $serviceDb 售后单模型
     * @return bool
     */
    private function refundPayment(float $value, array $orderData, object $serviceDb): bool
    {
        if ($value <= 0 || $orderData['total_amount'] <= 0 || empty($orderData['payment_no'])) {
            return true;
        }

        $refundNo = '';
        $refundDb = new OrderRefund();

        if (!$refundDb->refundOrderPayment($orderData, $value, $refundNo)) {
            return $this->setError($refundDb->getError());
        }

        if (false === $serviceDb->save(['refund_no' => $refundNo])) {
            return false;
        }

        return true;
    }

    /**
     * 当仅退款或退款退货总金额到达订单金额时关闭订单
     * @access private
     * @param object $orderDb 订单模型
     * @return bool
     */
    private function isCancelOrder(object $orderDb): bool
    {
        // 查询是否已全部退款
        $map[] = ['order_no', '=', $orderDb->getAttr('order_no')];
        $map[] = ['user_id', '=', $orderDb->getAttr('user_id')];
        $map[] = ['type', 'in', '0,1'];
        $map[] = ['status', '=', 6];

        $sum = $this->where($map)->sum('refund_fee');
        $totalAmount = round($orderDb->getAttr('pay_amount') + $orderDb->getAttr('delivery_fee'), 2);

        if ($sum >= $totalAmount - 0.01 && $sum <= $totalAmount + 0.01) {
            // 修改订单数据
            if (false === $orderDb->save(['trade_status' => 4, 'payment_status' => 0])) {
                return $this->setError($orderDb->getError());
            }

            // 写入订单操作日志
            if (!$orderDb->addOrderLog($orderDb->toArray(), '退款已完成，订单关闭', '取消订单')) {
                return $this->setError($orderDb->getError());
            }
        }

        return true;
    }

    /**
     * 完成仅退款、退款退货售后服务单
     * @access private
     * @param array  $data      外部数据
     * @param object $serviceDb 售后单模型
     * @return bool
     */
    private function completeContainsFeeService(array $data, object $serviceDb): bool
    {
        if (!is_object($serviceDb)) {
            return $this->setError('参数异常');
        }

        if (!in_array($serviceDb->getAttr('status'), [1, 4])) {
            return $this->setError('售后服务单当前状态不允许设置');
        }

        // 避免无关数据
        unset($data['order_service_id']);

        // 开启事务
        $serviceDb::startTrans();

        try {
            // 更新主数据
            if (false === $serviceDb->allowField(['result', 'status'])->save($data)) {
                throw new \Exception($serviceDb->getError());
            }

            // 更新订单商品售后状态
            $goodsDb = $serviceDb->getAttr('getOrderGoods');
            if (false === $goodsDb->save(['is_service' => 3, 'status' => 3])) {
                throw new \Exception($goodsDb->getError());
            }

            // 获取订单数据
            $serviceNo = $serviceDb->getAttr('service_no');
            $orderDb = Order::where('order_no', '=', $serviceDb->getAttr('order_no'))->find();

            if (!$orderDb) {
                throw new \Exception(is_null($orderDb) ? '订单不存在' : $orderDb->getError());
            }

            // 检测是否需要追回赠送资源
            if ($orderDb->getAttr('is_give') === 1) {
                if ($orderDb->getAttr('trade_status') === 3) {
                    if (!$this->recoverGiveResources($orderDb->toArray(), $serviceNo)) {
                        throw new \Exception($this->getError());
                    }
                }

                if (false === $orderDb->save(['is_give' => 0])) {
                    throw new \Exception($orderDb->getError());
                }
            }

            // 日志详情数据准备
            $comment = '售后服务完成，合计退款：' . $serviceDb->getAttr('refund_fee') . PHP_EOL;

            // 根据退款结构退回款项
            if ($serviceDb->getAttr('refund_fee') > 0) {
                $refundDetail = $serviceDb->getAttr('refund_detail');
                foreach ($refundDetail as $key => $value) {
                    if ($value <= 0) {
                        continue;
                    }

                    if ('payment_amount' == $key) {
                        if (!$this->refundPayment($value, $orderDb->toArray(), $serviceDb)) {
                            throw new \Exception($orderDb->getError());
                        }

                        $comment .= '在线支付原路退回：' . $value . PHP_EOL;
                        continue;
                    }

                    if ('card_amount' == $key) {
                        if (!$this->refundCardUser($value, $orderDb, $serviceNo)) {
                            throw new \Exception($orderDb->getError());
                        }

                        $comment .= '购物卡退回：' . $value . PHP_EOL;
                        continue;
                    }

                    if (in_array($key, ['money_amount', 'integral_amount'])) {
                        if ('integral_amount' == $key) {
                            $refundDetail[$key] *= $orderDb->getAttr('integral_pct');
                            $value = $refundDetail[$key];
                        }

                        if (!$this->refundUserMoney($key, $value, $serviceDb->getAttr('user_id'), $serviceNo)) {
                            throw new \Exception($orderDb->getError());
                        }

                        $comment .= ('integral_amount' == $key ? '积分' : '余额') . '退回：' . $value . PHP_EOL;
                        continue;
                    }
                }
            }

            // 写入售后服务单日志
            if (!$this->addServiceLog($serviceDb->toArray(), $comment, '完成售后')) {
                throw new \Exception($orderDb->getError());
            }

            // 隐藏已存在的评价
            if ($goodsDb->getAttr('is_comment') > 0) {
                $map[] = ['order_goods_id', '=', $goodsDb->getAttr('order_goods_id')];
                GoodsComment::update(['is_show' => 0], $map);
            }

            // 当仅退款或退款退货总金额到达订单金额时关闭订单
            if (!$this->isCancelOrder($orderDb)) {
                throw new \Exception($orderDb->getError());
            }

            $serviceDb::commit();
            return true;
        } catch (\Exception $e) {
            $serviceDb::rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 完成换货、维修售后服务单
     * @access private
     * @param array  $data      外部数据
     * @param object $serviceDb 数据模型
     * @return bool
     */
    private function completeNotFeeService(array $data, object $serviceDb): bool
    {
        if (!is_object($serviceDb)) {
            return $this->setError('参数异常');
        }

        if ($serviceDb->getAttr('is_return') === 1) {
            if (!$this->validateData($data, 'logistic')) {
                return false;
            }
        }

        if ($serviceDb->getAttr('status') !== 4) {
            return $this->setError('售后服务单当前状态不允许设置');
        }

        // 避免无关数据
        unset($data['order_service_id']);

        // 开启事务
        $serviceDb::startTrans();

        try {
            // 添加一条配送记录
            if ($serviceDb->getAttr('is_return') === 1) {
                $distData = [
                    'client_id'        => $serviceDb->getAttr('user_id'),
                    'order_code'       => $serviceDb->getAttr('service_no'),
                    'delivery_item_id' => $data['delivery_item_id'],
                    'logistic_code'    => $data['logistic_code'],
                    'customer_name'    => Config::get('careyshop.service.mobile'),
                ];

                $distDb = new DeliveryDist();
                if (false === $distDb->addDeliveryDistItem($distData)) {
                    throw new \Exception($distDb->getError());
                }
            }

            // 更新主数据
            if (false === $serviceDb->allowField(['result', 'status'])->save($data)) {
                throw new \Exception($serviceDb->getError());
            }

            // 更新订单商品售后状态
            $goodsDb = $serviceDb->getAttr('getOrderGoods');
            if (false === $goodsDb->save(['is_service' => 2])) {
                throw new \Exception($goodsDb->getError());
            }

            // 写入售后服务单日志
            $comment = '商家已完成售后服务';
            $comment .= $serviceDb->getAttr('is_return') === 0 ? '。' : '，并已将售后商品寄出。';

            if (!$this->addServiceLog($serviceDb->toArray(), $comment, '完成售后')) {
                throw new \Exception($this->getError());
            }

            $serviceDb::commit();
            return true;
        } catch (\Exception $e) {
            $serviceDb::rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 完成一个售后服务单
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function setOrderServiceComplete(array $data)
    {
        if (!$this->validateData($data, 'complete')) {
            return false;
        }

        $result = $this->where('service_no', '=', $data['service_no'])->find();
        if (is_null($result)) {
            return $this->setError('售后服务单不存在');
        }

        // 完成实际业务
        $data['status'] = 6;
        $isSuccess = false;

        switch ($result->getAttr('type')) {
            case 0:
            case 1:
                $isSuccess = $this->completeContainsFeeService($data, $result);
                break;

            case 2:
            case 3:
                $isSuccess = $this->completeNotFeeService($data, $result);
                break;
        }

        if (true === $isSuccess) {
            return $result->toArray();
        }

        return false;
    }
}
