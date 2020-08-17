<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    购物卡使用模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/14
 */

namespace app\common\model;

use think\facade\Db;

class CardUse extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'card_use_id';

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'card_use_id',
        'card_id',
        'number',
        'password',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'money'       => 'float',
        'active_time' => 'timestamp',
    ];

    /**
     * belongsTo cs_card
     * @access public
     * @return mixed
     */
    public function getCard()
    {
        return $this->belongsTo(Card::class, 'card_id');
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
            ->joinType('left')
            ->field('user_id,username,nickname,level_icon,head_pic')
            ->hidden(['user_id']);
    }

    /**
     * 绑定购物卡
     * @access public
     * @param  array $data 外部数据
     * @return bool
     * @throws
     */
    public function bindCardUseItem($data)
    {
        if (!$this->validateData($data, 'bind')) {
            return false;
        }

        // 搜索条件
        $map[] = ['getCard.status', '=', 1];
        $map[] = ['getCard.is_delete', '=', 0];
        $map[] = ['card_use.number', '=', $data['number']];

        /*
         * 获取购物卡与购物卡使用数据
         * 卡号查找可能存在重复的情况,而重复的卡号可能导致对应的购物卡不同等问题.
         * 所以卡号和卡密必须全部对应,但不能使用AND查询SQL,返回的错误信息需要准确.
         */
        $result = $this->where($map)->withJoin('getCard')->select();
        if ($result->isEmpty()) {
            return $this->setError('购物卡无效或卡号不存在');
        }

        // 根据卡密查找购物卡使用
        while (!$result->isEmpty()) {
            $tmpResult = $result->shift();
            if (hash_equals(mb_strtolower($tmpResult->getAttr('password')), mb_strtolower($data['password']))) {
                $cardResult = $tmpResult;
                break;
            }
        }

        // 上几步卡号找到,而到此步时却是空的,则表示卡密错误
        if (!isset($cardResult)) {
            return $this->setError('购物卡卡密错误');
        }

        // 验证购物卡使用是否已使用
        if ($cardResult->getAttr('user_id') > 0) {
            return $this->setError('购物卡已被使用');
        }

        // 开启事务
        $cardResult::startTrans();

        try {
            // 修改购物卡使用数据
            $cardResult->setAttr('user_id', get_client_id());
            $cardResult->setAttr('is_active', 1);
            $cardResult->setAttr('active_time', time());

            // 修改购物卡数据
            $cardResult->getAttr('get_card')->inc('active_num')->update();
            $cardResult->save();

            $cardResult::commit();
            return true;
        } catch (\Exception $e) {
            $cardResult::rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 批量设置购物卡是否有效
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setCardUseInvalid($data)
    {
        if (!$this->validateData($data, 'invalid')) {
            return false;
        }

        $map[] = ['card_use_id', 'in', $data['card_use_id']];
        unset($data['card_use_id']);

        self::update($data, $map);
        return true;
    }

    /**
     * 增加可用余额
     * @access public
     * @param string $number   卡号
     * @param float  $value    数值
     * @param int    $clientId 账号编号
     * @return bool
     * @throws
     */
    public function incCardUseMoney($number = '', $value = 0.0, $clientId = 0)
    {
        if (empty($number)) {
            return $this->setError('卡号不能为空');
        }

        if ($value <= 0 || $clientId == 0) {
            return $this->setError('数值或账号编号错误');
        }

        // 搜索条件
        $map[] = ['card_use.user_id', '=', $clientId];
        $map[] = ['card_use.number', '=', $number];
        $map[] = ['card_use.is_invalid', '=', 1];

        $result = $this->where($map)->withJoin('getCard')->find();
        if (is_null($result)) {
            return $this->setError('卡号 ' . $number . ' 已失效或不存在');
        }

        // 判断是否在有效期内
        $end_time = $result->getAttr('get_card')->getData('end_time');
        if (time() > $end_time && $end_time != 0) {
            return $this->setError(sprintf('卡号 %s 已过使用截止日期 %s', $number, date('Y-m-d H:i:s', $end_time)));
        }

        $result->inc('money', $value)->update();
        return true;
    }

    /**
     * 减少可用余额
     * @access public
     * @param string $number   卡号
     * @param float  $value    数值
     * @param int    $clientId 账号编号
     * @return bool
     * @throws
     */
    public function decCardUseMoney($number = '', $value = 0.0, $clientId = 0)
    {
        if (empty($number)) {
            return $this->setError('卡号不能为空');
        }

        if ($value <= 0 || $clientId == 0) {
            return $this->setError('数值或账号编号错误');
        }

        // 搜索条件
        $map[] = ['card_use.user_id', '=', $clientId];
        $map[] = ['card_use.number', '=', $number];
        $map[] = ['card_use.is_invalid', '=', 1];

        $result = $this->where($map)->withJoin('getCard')->find();
        if (is_null($result)) {
            return $this->setError('卡号 ' . $number . ' 已失效或不存在');
        }

        if (bccomp($result->getAttr('money'), $value, 2) === -1) {
            return $this->setError('卡号 ' . $number . ' 可用余额不足');
        }

        // 判断是否在有效期内
        $end_time = $result->getAttr('get_card')->getData('end_time');
        if (time() > $end_time && $end_time != 0) {
            return $this->setError(sprintf('卡号 %s 已过使用截止日期 %s', $number, date('Y-m-d H:i:s', $end_time)));
        }

        $result->dec('money', $value)->update();
        return true;
    }

    /**
     * 获取可合并的购物卡列表
     * @access public
     * @param array $data 外部数据
     * @return false|array
     */
    public function getCardUseMerge($data)
    {
        if (!$this->validateData($data, 'merge_list')) {
            return false;
        }

        $map[] = ['u.user_id', '=', get_client_id()];
        $map[] = ['u.money', '>', 0];
        $map[] = ['u.is_invalid', '=', 1];
        $map[] = ['c.end_time', 'exp', Db::raw('= 0 OR `c`.`end_time` >= ' . time())];

        // 处理排除的购物卡使用
        if (!empty($data['exclude_number'])) {
            $excludeMap[] = ['user_id', '=', get_client_id()];
            $excludeMap[] = ['number', '=', $data['exclude_number']];
            $sameCardId = $this->where($excludeMap)->value('card_id');

            if (!is_null($sameCardId)) {
                $map[] = ['u.number', '<>', $data['exclude_number']];
                $map[] = ['c.card_id', '=', $sameCardId];
            } else {
                return [];
            }
        }

        return $this->alias('u')
            ->field('u.number,u.money,c.name,c.description')
            ->join('card c', 'c.card_id = u.card_id')
            ->where($map)
            ->order(['u.money' => 'desc'])
            ->select()
            ->toArray();
    }

    /**
     * 相同购物卡进行余额合并
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function setCardUseMerge($data)
    {
        if (!$this->validateData($data, 'merge')) {
            return false;
        }

        // 卡号相同不做处理
        if ($data['number'] == $data['src_number']) {
            return true;
        }

        // 检测是否属于同一类型卡
        $clientId = get_client_id();
        $map[] = ['user_id', '=', $clientId];
        $map[] = ['number', 'in', [$data['number'], $data['src_number']]];

        // todo 到此处,number条件需要额外处理
        if ($this->where($map)->group('card_id')->count() > 1) {
            return $this->setError('不同类型的购物卡不能进行合并');
        }

        // 合并金额不存在则需要获取来源卡金额
        if (empty($data['money'])) {
            $map['number'] = ['eq', $data['src_number']];
            $data['money'] = $this->where($map)->value('money', 0, true);
        }

        if ($data['money'] <= 0) {
            return $this->setError('金额未变动');
        }

        // 开启事务
        self::startTrans();

        try {
            // 减少来源卡可用金额
            if (!$this->decCardUseMoney($data['src_number'], $data['money'], $clientId)) {
                throw new \Exception($this->getError());
            }

            // 增加目标卡可用金额
            if (!$this->incCardUseMoney($data['number'], $data['money'], $clientId)) {
                throw new \Exception($this->getError());
            }

            self::commit();
            return true;
        } catch (\Exception $e) {
            self::rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 隐藏购物卡卡密
     * @access private
     * @param array $data 购物卡数据
     * @return mixed
     */
    private function hidePassword($data)
    {
        $cardAuth = json_decode(Config::get('card_auth.value', 'system_info'), true);
        if (!is_client_admin() || in_array(get_client_id(), $cardAuth)) {
            return $data;
        }

        foreach ($data as &$value) {
            $value['password'] = auto_hid_substr($value['password'], 6);
        }

        return $data;
    }

    /**
     * 导出生成的购物卡
     * @access public
     * @param  array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getCardUseExport($data)
    {
        if (!$this->validateData($data, 'CardUse.export')) {
            return false;
        }

        $result = self::all(function ($query) use ($data) {
            $query
                ->field('card_id,user_id', true)
                ->where(['card_id' => ['eq', $data['card_id']]]);
        });

        if ($result !== false) {
            return $this->hidePassword($result->toArray());
        }

        return false;
    }

    /**
     * 获取已绑定的购物卡
     * @access public
     * @param  array $data 外部数据
     * @return false|array
     * @throws
     */
    public function getCardUseList($data)
    {
        if (!$this->validateData($data, 'CardUse.list')) {
            return false;
        }

        // 搜索条件
        $map = $mapOr = [];

        if (is_client_admin()) {
            empty($data['card_id']) ?: $map['card_use.card_id'] = ['eq', $data['card_id']];
            empty($data['number']) ?: $map['card_use.number'] = ['eq', $data['number']];
            empty($data['name']) ?: $map['getCard.name'] = ['like', '%' . $data['name'] . '%'];
            empty($data['account']) ?: $map['getUser.username|getUser.nickname'] = ['eq', $data['account']];

            // empty($data['is_active']) ?: $map['card_use.user_id'] = ['neq', 0];
            // 以上条件被替换成下行
            if (!is_empty_parm($data['is_active'])) {
                $map['card_use.user_id'] = empty($data['is_active']) ? ['eq', 0] : ['neq', 0];
            }
        } else {
            $map['card_use.user_id'] = ['eq', get_client_id()];
        }

        if (!is_empty_parm($data['type'])) {
            // 正常状态
            if ($data['type'] == 'normal') {
                $map['getCard.end_time'] = [['eq', 0], ['egt', time()], 'OR'];
                $map['card_use.money'] = ['gt', 0];
                $map['card_use.is_invalid'] = ['eq', 1];
            }

            // 无效状态
            if ($data['type'] == 'invalid') {
                $mapOr['getCard.end_time'] = [['neq', 0], ['lt', time()], 'AND'];
                $mapOr['card_use.money'] = ['elt', 0];
                $mapOr['card_use.is_invalid'] = ['eq', 0];
            }
        }

        // 关联查询
        $with = ['getCard'];
        !is_client_admin() ?: $with[] = 'getUser';

        $totalResult = $this
            ->with($with)
            ->where($map)
            ->where(function ($query) use ($mapOr) {
                $query->whereOr($mapOr);
            })
            ->count();

        if ($totalResult <= 0) {
            return ['total_result' => 0];
        }

        $result = self::all(function ($query) use ($data, $with, $map, $mapOr) {
            // 翻页页数
            $pageNo = isset($data['page_no']) ? $data['page_no'] : 1;

            // 每页条数
            $pageSize = isset($data['page_size']) ? $data['page_size'] : config('paginate.list_rows');

            $query
                ->with($with)
                ->where($map)
                ->where(function ($query) use ($mapOr) {
                    $query->whereOr($mapOr);
                })
                ->order(['card_use.card_use_id' => 'desc'])
                ->page($pageNo, $pageSize);
        });

        if (false !== $result) {
            return ['items' => $this->hidePassword($result->toArray()), 'total_result' => $totalResult];
        }

        return false;
    }

    /**
     * 根据商品Id列出可使用的购物卡
     * @access public
     * @param  array $data 外部数据
     * @return false|array
     * @throws
     */
    public function getCardUseSelect($data)
    {
        if (!$this->validateData($data, 'CardUse.select')) {
            return false;
        }

        // 获取所有有效的购物卡使用
        $cardResult = self::all(function ($query) use ($data) {
            $map['getCard.end_time'] = [['eq', 0], ['egt', time()], 'OR'];
            $map['card_use.user_id'] = ['eq', get_client_id()];
            $map['card_use.money'] = ['gt', 0];
            $map['card_use.is_invalid'] = ['eq', 1];

            $query->with('getCard')->where($map)->order(['card_use.money' => 'desc']);
        });

        if ($cardResult->isEmpty()) {
            return [];
        }

        // 获取商品分类
        $result = [];
        $goodsResult = Goods::where(['goods_id' => ['in', $data['goods_id']]])->column('goods_category_id');

        foreach ($cardResult as $value) {
            $tempCard = $value->toArray();
            $tempData['number'] = $tempCard['number'];
            $tempData['money'] = $tempCard['money'];
            $tempData['name'] = $tempCard['get_card']['name'];
            $tempData['description'] = $tempCard['get_card']['description'];
            $tempData['is_use'] = (int)$this->checkCard($tempCard, $goodsResult);
            $tempData['not_use_error'] = 0 == $tempData['is_use'] ? $this->getError() : '';

            $result[] = $tempData;
            unset($tempCard, $tempData);
        }

        return $result;
    }

    /**
     * 验证购物卡是否可使用
     * @access private
     * @param  array $card          购物卡数据
     * @param  array $goodsCategory 商品分类集合
     * @param  float $decMoney      准备减少的可用金额
     * @return bool
     */
    private function checkCard($card, $goodsCategory, $decMoney = 0.0)
    {
        if ($decMoney > 0 && bccomp($decMoney, $card['money'], 2) === 1) {
            return $this->setError('卡号 ' . $card['number'] . ' 可用余额不足');
        }

        // 达到条件可直接返回
        if (empty($card['get_card']['category']) && empty($card['get_card']['exclude_category'])) {
            return true;
        }

        if (!empty($card['get_card']['category'])) {
            $categoryList = GoodsCategory::getCategorySon(['goods_category_id' => $card['get_card']['category']]);
            $categoryList = array_column($categoryList, 'goods_category_id');
        }

        if (!empty($card['get_card']['exclude_category'])) {
            $excludeList = GoodsCategory::getCategorySon(['goods_category_id' => $card['get_card']['exclude_category']]);
            $excludeList = array_column($excludeList, 'goods_category_id');
        }

        foreach ($goodsCategory as $value) {
            if (isset($categoryList) && !in_array($value, $categoryList)) {
                return $this->setError('卡号 ' . $card['number'] . ' 只能在指定商品分类中使用');
            }

            if (isset($excludeList) && in_array($value, $excludeList)) {
                return $this->setError('卡号 ' . $card['number'] . ' 不能在限制商品分类中使用');
            }
        }

        return true;
    }

    /**
     * 验证购物卡是否可使用
     * @access public
     * @param  array $data 外部数据
     * @return false|array
     * @throws
     */
    public function getCardUseCheck($data)
    {
        if (!$this->validateData($data, 'CardUse.check')) {
            return false;
        }

        // 初始化部分数据
        !is_empty_parm($data['money']) ?: $data['money'] = 0;

        // 获取购物卡
        $cardResult = self::get(function ($query) use ($data) {
            $map['getCard.end_time'] = [['eq', 0], ['egt', time()], 'OR'];
            $map['card_use.user_id'] = ['eq', get_client_id()];
            $map['card_use.number'] = ['eq', $data['number']];
            $map['card_use.money'] = ['gt', 0];
            $map['card_use.is_invalid'] = ['eq', 1];

            $query->with('getCard')->where($map);
        });

        if (!$cardResult) {
            return is_null($cardResult) ? $this->setError('卡号 ' . $data['number'] . ' 已失效或不存在') : false;
        }

        // 获取订单商品分类并进行筛选
        $result = $cardResult->toArray();
        $goodsResult = Goods::where(['goods_id' => ['in', $data['goods_id']]])->column('goods_category_id');

        return $this->checkCard($result, $goodsResult, $data['money']);
    }
}
