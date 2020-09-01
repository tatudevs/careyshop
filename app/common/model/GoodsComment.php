<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品评价模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/31
 */

namespace app\common\model;

use careyshop\Ip2Region;

class GoodsComment extends CareyShop
{
    /**
     * 主评论
     * @var int
     */
    const COMMENT_TYPE_MAIN = 0;

    /**
     * 主评论回复
     * @var int
     */
    const COMMENT_TYPE_MAIN_REPLY = 1;

    /**
     * 追加评论
     * @var int
     */
    const COMMENT_TYPE_ADDITION = 2;

    /**
     * 追加评论回复
     * @var int
     */
    const COMMENT_TYPE_ADDITION_REPLY = 3;

    /**
     * 主键
     * @var string
     */
    protected $pk = 'goods_comment_id';

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
        'parent_id',
        'user_id',
        'is_anon',
        'is_image',
        'is_delete',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'goods_comment_id' => 'integer',
        'parent_id'        => 'integer',
        'goods_id'         => 'integer',
        'order_goods_id'   => 'integer',
        'user_id'          => 'integer',
        'is_anon'          => 'integer',
        'type'             => 'integer',
        'is_image'         => 'integer',
        'is_append'        => 'integer',
        'score'            => 'integer',
        'praise'           => 'integer',
        'reply_count'      => 'integer',
        'is_show'          => 'integer',
        'is_top'           => 'integer',
        'status'           => 'integer',
        'is_delete'        => 'integer',
        'image'            => 'array',
    ];

    /**
     * 获取器ip地址
     * @access public
     * @param $value
     * @param $data
     * @return string
     */
    public function getIpAddressRegionAttr($value, $data)
    {
        if (empty($data['ip_address'])) {
            return '';
        }

        try {
            $ip2region = new Ip2Region();
            $result = $ip2region->btreeSearch($data['ip_address']);

            if ($result) {
                $value = get_ip2region_str($result['region']);
            }
        } catch (\Exception $e) {
            return '';
        }

        return $value;
    }

    /**
     * hasOne cs_goods_comment
     * @access public
     * @return mixed
     */
    public function getAddition()
    {
        return $this->hasOne(GoodsComment::class, 'parent_id');
    }

    /**
     * hasMany cs_goods_comment
     * @access public
     * @return mixed
     */
    public function getMainReply()
    {
        return $this->hasMany(GoodsComment::class, 'parent_id');
    }

    /**
     * hasMany cs_goods_comment
     * @access public
     * @return mixed
     */
    public function getAdditionReply()
    {
        return $this->hasMany(GoodsComment::class, 'parent_id');
    }

    /**
     * hasOne cs_order_goods
     * @access public
     * @return mixed
     */
    public function getOrderGoods()
    {
        return $this
            ->hasOne(OrderGoods::class, 'order_goods_id', 'order_goods_id')
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
     * 添加一条新的商品评价
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function addCommentItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 搜索条件
        $map[] = ['order_goods_id', '=', $data['order_goods_id']];
        $map[] = ['order_no', '=', $data['order_no']];
        $map[] = ['user_id', '=', get_client_id()];
        $map[] = ['type', '=', self::COMMENT_TYPE_MAIN];

        if (self::checkUnique($map)) {
            return $this->setError('订单商品评价已存在');
        }

        // 检测订单商品是否允许评价
        $orderGoodsDb = new OrderGoods();
        if (!$orderGoodsDb->isComment($data['order_no'], $data['order_goods_id'])) {
            return $this->setError($orderGoodsDb->getError());
        }

        // 允许外部写入字段
        $field = [
            'order_no', 'goods_id', 'order_goods_id', 'user_id', 'is_anon', 'type',
            'content', 'image', 'is_image', 'score', 'ip_address', 'is_show',
        ];

        // 系统数据内容
        $data['user_id'] = get_client_id();
        $data['type'] = self::COMMENT_TYPE_MAIN;
        $data['ip_address'] = request()->ip();
        empty($data['image']) ? $data['image'] = [] : $data['is_image'] = 1;

        // 开启事务
        $this->startTrans();

        try {
            // 添加订单商品评价
            if (!$this->allowField($field)->save($data)) {
                throw new \Exception($this->getError());
            }

            // 修改订单商品数据
            array_pop($map);
            OrderGoods::update(['is_comment' => 1], $map);

            // 累计增加商品评价数
            Goods::where('goods_id', '=', $data['goods_id'])->inc('comment_sum')->update();

            $this->commit();
            return $this->toArray();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 追加一条商品评价
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function addAdditionItem(array $data)
    {
        if (!$this->validateData($data, 'addition')) {
            return false;
        }

        $map = [
            ['parent_id', '<>', 0],
            ['order_goods_id', '=', $data['order_goods_id']],
            ['order_no', '=', $data['order_no']],
            ['user_id', '=', get_client_id()],
            ['type', '=', self::COMMENT_TYPE_ADDITION],
        ];

        if (self::checkUnique($map)) {
            return $this->setError('订单商品追加评价已存在');
        }

        // 获取主评价
        $map = [
            ['parent_id', '=', 0],
            ['type', '=', self::COMMENT_TYPE_MAIN],
            ['is_delete', '=', 0],
        ];

        $result = $this->where($map)->find();
        if (is_null($result)) {
            return $this->setError('主评价不存在');
        }

        // 开启事务
        $this->startTrans();

        try {
            // 设置主评为未读,并且判断追评是否存在图片
            $update['status'] = 0;
            $update['is_append'] = 1;
            empty($data['image']) ?: $update['is_image'] = 1;

            if (!$result->save($update)) {
                throw new \Exception($this->getError());
            }

            // 准备插入数据
            $newData = $result->getData();
            unset($newData['goods_comment_id']);

            $newData['score'] = 0;
            $newData['praise'] = 0;
            $newData['reply_count'] = 0;
            $newData['is_append'] = 0;
            $newData['is_show'] = 0;
            $newData['is_top'] = 0;
            $newData['status'] = 0;
            $newData['ip_address'] = request()->ip();
            $newData['parent_id'] = $result->getAttr('goods_comment_id');
            $newData['type'] = self::COMMENT_TYPE_ADDITION;
            $newData['content'] = $data['content'];
            $newData['is_image'] = !empty($data['image']) ? 1 : 0;
            $newData['image'] = !empty($data['image']) ? $data['image'] : [];
            $newData['create_time'] = time();
            $result = self::create($newData);

            // 修改订单商品数据
            $mapGoods[] = ['order_goods_id', '=', $data['order_goods_id']];
            $mapGoods[] = ['order_no', '=', $data['order_no']];
            $mapGoods[] = ['user_id', '=', get_client_id()];
            OrderGoods::update(['is_comment' => 2], $mapGoods);

            $this->commit();
            return $result->hidden(['is_show', 'is_top', 'status', 'praise', 'reply_count'])->toArray();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 回复或追评一条商品评价
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function replyCommentItem(array $data)
    {
        if (!$this->validateData($data, 'reply')) {
            return false;
        }

        $map[] = ['goods_comment_id', '=', $data['goods_comment_id']];
        $map[] = ['type', 'in', [self::COMMENT_TYPE_MAIN, self::COMMENT_TYPE_ADDITION]];
        $map[] = ['is_delete', '=', 0];

        $result = $this->where($map)->find();
        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        // 设置状态为已读
        $readId = $result->getAttr('parent_id');
        if (0 >= $readId) {
            $readId = $result->getAttr('goods_comment_id');
        }

        // 修改主数据
        $this->batchSetting(['status'], ['goods_comment_id' => [$readId], 'status' => 1]);

        // 准备插入数据
        $newData = $result->getData();
        unset($newData['goods_comment_id']);

        $newData['score'] = 0;
        $newData['praise'] = 0;
        $newData['reply_count'] = 0;
        $newData['ip_address'] = request()->ip();
        $newData['is_append'] = 0;
        $newData['is_show'] = 0;
        $newData['is_top'] = 0;
        $newData['status'] = 0;
        $newData['content'] = $data['content'];
        $newData['is_image'] = !empty($data['image']) ? 1 : 0;
        $newData['image'] = !empty($data['image']) ? $data['image'] : [];
        $newData['create_time'] = time();

        // 回复和追加共用数据结构
        if ($newData['type'] === self::COMMENT_TYPE_MAIN) {
            $newData['type'] = self::COMMENT_TYPE_MAIN_REPLY;
            $newData['parent_id'] = $data['goods_comment_id'];
        } else {
            $newData['type'] = self::COMMENT_TYPE_ADDITION_REPLY;
            $newData['parent_id'] = $result->getAttr('parent_id');
        }

        $result = self::create($newData);
        return $result->hidden(['is_show', 'is_top', 'status', 'reply_count'])->toArray();
    }

    /**
     * 删除任意一条商品评价(主评,主回,追评,追回)
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function delCommentItem(array $data)
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        $map[] = ['goods_comment_id', '=', $data['goods_comment_id']];
        $map[] = ['is_delete', '=', 0];

        if (!is_client_admin()) {
            $map[] = ['user_id', '=', get_client_id()];
            $map[] = ['type', 'in', [self::COMMENT_TYPE_MAIN, self::COMMENT_TYPE_ADDITION]];
        }

        $result = $this->where($map)->find();
        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        // 软删除评价
        $result->save(['is_delete' => 1]);

        // 如果是追评需要处理主评是否有图
        if ($result->getAttr('type') === self::COMMENT_TYPE_ADDITION) {
            $map = [
                ['goods_comment_id', '=', $result->getAttr('parent_id')],
                ['is_delete', '=', 0],
            ];

            $parent = $this->where($map)->find();
            if (!is_null($parent)) {
                $setData = ['is_append' => 0];
                if ($result->getAttr('is_image') === 1 && empty($parent->getAttr('image'))) {
                    $setData['is_image'] = 0;
                }

                $parent->save($setData);
            }
        }

        return true;
    }

    /**
     * 点赞任意一条商品评价(主评,主回,追评,追回)
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function addPraiseItem(array $data)
    {
        if (!$this->validateData($data, 'praise')) {
            return false;
        }

        $map = [
            ['p.user_id', '=', get_client_id()],
            ['c.goods_comment_id', '=', $data['goods_comment_id']],
        ];

        $count = $this
            ->alias('c')
            ->join('praise p', 'p.goods_comment_id = c.goods_comment_id')
            ->where($map)
            ->count();

        if ($count > 0) {
            return $this->setError('您已经点过赞了');
        }

        $map = [
            ['goods_comment_id', '=', $data['goods_comment_id']],
            ['is_delete', '=', 0],
        ];

        // 更新成功则添加记录到praise表
        $this->where($map)->inc('praise')->update();
        Praise::insert(['user_id' => get_client_id(), 'goods_comment_id' => $data['goods_comment_id']]);

        return true;
    }

    /**
     * 获取一个商品评价得分
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getCommentScore(array $data)
    {
        if (!$this->validateData($data, 'score')) {
            return false;
        }

        // 初始化数据
        $result = [
            'count'         => 0,
            'good_count'    => 0,
            'good_rate'     => 0,
            'general_count' => 0,
            'general_rate'  => 0,
            'poor_count'    => 0,
            'poor_rate'     => 0,
        ];

        // 公共查询条件
        $map[] = ['parent_id', '=', 0];
        $map[] = ['goods_id', '=', $data['goods_id']];
        $map[] = ['type', '=', self::COMMENT_TYPE_MAIN];
        $map[] = ['is_show', '=', 1];
        $map[] = ['is_delete', '=', 0];

        // 1~2=差评 3~4=中评 5=好评
        $result['poor_count'] = $this->where($map)->where('score', '<=', 2)->count();
        $result['general_count'] = $this->where($map)->where('score', 'between', '3,4')->count();
        $result['good_count'] = $this->where($map)->where('score', '=', 5)->count();
        $result['count'] = $result['poor_count'] + $result['general_count'] + $result['good_count'];

        if ($result['count'] > 0) {
            $result['good_rate'] = round(($result['good_count'] / $result['count']) * 100, 2);
            $result['general_rate'] = round(($result['general_count'] / $result['count']) * 100, 2);
            $result['poor_rate'] = round(($result['poor_count'] / $result['count']) * 100, 2);
        }

        return $result;
    }

    /**
     * 批量设置某个字段值
     * @access private
     * @param array $field 修改的字段
     * @param array $data  原始数据
     */
    private function batchSetting(array $field, array $data)
    {
        $map[] = ['goods_comment_id', 'in', $data['goods_comment_id']];
        $map[] = ['parent_id', '=', 0];
        $map[] = ['type', '=', self::COMMENT_TYPE_MAIN];
        $map[] = ['is_delete', '=', 0];

        unset($data['goods_comment_id']);
        self::update($data, $map, $field);
    }

    /**
     * 批量设置是否前台显示
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setCommentShow(array $data)
    {
        if (!$this->validateData($data, 'show')) {
            return false;
        }

        $this->batchSetting(['is_show'], $data);
        return true;
    }

    /**
     * 批量设置评价是否置顶
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setCommentTop(array $data)
    {
        if (!$this->validateData($data, 'top')) {
            return false;
        }

        $this->batchSetting(['is_top'], $data);
        return true;
    }

    /**
     * 批量设置评价是否已读
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setCommentStatus(array $data)
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $this->batchSetting(['status'], $data);
        return true;
    }

    /**
     * 获取一个商品"全部"、"晒图"、"追评"、"好评"、"中评"、差评"的数量
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function getCommentCount(array $data)
    {
        if (!$this->validateData($data, 'count')) {
            return false;
        }

        // 初始化基础数据
        $result = [
            'all_count'      => 0,
            'image_count'    => 0,
            'addition_count' => 0,
            'good_count'     => 0,
            'general_count'  => 0,
            'poor_count'     => 0,
        ];

        // 公共筛选条件
        $map[] = ['goods_id', '=', $data['goods_id']];
        $map[] = ['type', '=', self::COMMENT_TYPE_MAIN];
        $map[] = ['is_show', '=', 1];
        $map[] = ['is_delete', '=', 0];

        $result['all_count'] = $this->where($map)->count();
        $result['image_count'] = $this->where($map)->where('is_image', '=', 1)->count();
        $result['poor_count'] = $this->where($map)->where('score', '<=', 2)->count();
        $result['general_count'] = $this->where($map)->where('score', 'between', '3,4')->count();
        $result['good_count'] = $this->where($map)->where('score', '=', 5)->count();

        // 带有追加评论的
        $map[] = ['type', '=', self::COMMENT_TYPE_ADDITION];
        $result['addition_count'] = $this->where($map)->count();

        return $result;
    }

    /**
     * 获取某个评价的明细("是否已读"不关联,关联不代表看完,所以需手动设置)
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getCommentItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        // 获取条件
        $map[] = ['goods_comment.goods_comment_id', '=', $data['goods_comment_id']];
        $map[] = ['goods_comment.type', '=', self::COMMENT_TYPE_MAIN];
        is_client_admin() ?: $map[] = ['goods_comment.is_show', '=', 1];
        $map[] = ['goods_comment.is_delete', '=', 0];

        // 过滤不返回的字段
        $field = 'goods_id,order_goods_id,is_append,is_show,is_top';
        is_client_admin() ?: $field .= ',status,order_no';

        // 关联表不返回的字段
        $replyField = 'goods_id,order_goods_id,order_no,score,is_top,is_append,status,is_show,reply_count';

        // 关联数据
        $withJoin['getUser'] = ['username', 'nickname', 'level_icon', 'user_level_id', 'head_pic'];
        $withJoin['getOrderGoods'] = ['goods_id', 'goods_name', 'goods_image', 'key_value'];

        // 返回主评回复
        $with['get_main_reply'] = function ($query) use ($replyField) {
            $query->withoutField($replyField . ',ip_address')
                ->where('type', '=', self::COMMENT_TYPE_MAIN_REPLY)
                ->where('is_delete', '=', 0);
        };

        // 返回追加评价
        $with['get_addition'] = function ($query) use ($replyField) {
            $query->withoutField($replyField)
                ->where('type', '=', self::COMMENT_TYPE_ADDITION)
                ->where('is_delete', '=', 0);
        };

        // 返回追评回复
        $with['get_addition_reply'] = function ($query) use ($replyField) {
            $query->withoutField($replyField . ',ip_address')
                ->where('type', '=', self::COMMENT_TYPE_ADDITION_REPLY)
                ->where('is_delete', '=', 0);
        };

        $result = $this->withoutField($field)
            ->withJoin($withJoin)
            ->with($with)
            ->where($map)
            ->find();

        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        // 处理客户信息是否匿名
        if ($result->getAttr('is_anon') !== 0 && !is_client_admin()) {
            $result['getUser']->setAttr('username', auto_hid_substr($result['getUser']->getAttr('username')));
            $result['getUser']->setAttr('nickname', auto_hid_substr($result['getUser']->getAttr('nickname')));
        }

        if ($result['get_addition']) {
            $result['get_addition']->append(['ip_address_region']);
        }

        $temp = [$result->append(['ip_address_region'])->toArray()];
        self::keyToSnake(['getUser', 'getOrderGoods'], $temp);

        return $temp[0];
    }

    /**
     * 获取商品评价列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getCommentList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map[] = ['goods_comment.type', '=', self::COMMENT_TYPE_MAIN];
        $map[] = ['goods_comment.is_delete', '=', 0];

        empty($data['goods_id']) ?: $map[] = ['goods_comment.goods_id', '=', $data['goods_id']];
        is_empty_parm($data['is_image']) ?: $map[] = ['goods_comment.is_image', '=', $data['is_image']];
        is_empty_parm($data['is_append']) ?: $map[] = ['goods_comment.is_append', '=', $data['is_append']];
        is_client_admin() ?: $map[] = ['goods_comment.is_show', '=', 1];

        // 处理"好中差"评价搜索(0=好评 1=中评 其他=差评)
        if (isset($data['score'])) {
            switch ($data['score']) {
                case 0:
                    $map[] = ['goods_comment.score', '=', 5];
                    break;
                case 1:
                    $map[] = ['goods_comment.score', 'between', '3,4'];
                    break;
                default:
                    $map[] = ['goods_comment.score', '<=', 2];
            }
        }

        // 后台搜索条件
        if (is_client_admin()) {
            is_empty_parm($data['is_show']) ?: $map[] = ['goods_comment.is_show', '=', $data['is_show']];
            is_empty_parm($data['is_top']) ?: $map[] = ['goods_comment.is_top', '=', $data['is_top']];
            is_empty_parm($data['status']) ?: $map[] = ['goods_comment.status', '=', $data['status']];
            empty($data['order_no']) ?: $map[] = ['goods_comment.order_no', '=', $data['order_no']];
            empty($data['content']) ?: $map[] = ['goods_comment.content', 'like', '%' . $data['content'] . '%'];
            empty($data['account']) ?: $map[] = ['getUser.username|getUser.nickname', '=', $data['account']];
        }

        // 查看指定商品规格评价
        if (!empty($data['goods_id']) && !empty($data['goods_spec'])) {
            $data['goods_spec'] = implode('_', $data['goods_spec']);
            $map[] = ['getOrderGoods.key_name', '=', $data['goods_spec']];
        }

        // Join关联查询
        $withJoin['getUser'] = ['username', 'nickname', 'level_icon', 'user_level_id', 'head_pic'];
        $withJoin['getOrderGoods'] = ['goods_id', 'goods_name', 'goods_image', 'key_value'];

        // 数量统计
        $result['total_result'] = $this->withJoin($withJoin)->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 关联表不返回的字段
        $replyField = 'goods_id,order_goods_id,order_no,score,is_top,status,is_show,reply_count';

        // 过滤不需要返回的字段
        $field = 'goods_id,order_goods_id';
        is_client_admin() ?: $field .= ',status,order_no';

        // 获取追加评价
        $with['get_addition'] = function ($query) use ($replyField) {
            $query->withoutField($replyField)
                ->where('type', '=', self::COMMENT_TYPE_ADDITION)
                ->where('is_delete', '=', 0);
        };

        // 列表模式的区分(当"goods_id"为空表示简洁列表,否则为明细列表)
        if (!empty($data['goods_id'])) {
            // 获取主评回复
            $with['getMainReply'] = function ($query) use ($replyField) {
                $query->withoutField($replyField . ',ip_address')
                    ->where('type', '=', self::COMMENT_TYPE_MAIN_REPLY)
                    ->where('is_delete', '=', 0);
            };

            // 获取追评回复
            $with['getAdditionReply'] = function ($query) use ($replyField) {
                $query->withoutField($replyField . ',ip_address')
                    ->where('type', '=', self::COMMENT_TYPE_ADDITION_REPLY)
                    ->where('is_delete', '=', 0);
            };
        }

        // 实际查询
        $temp = $this->setDefaultOrder(['goods_comment_id' => 'desc'], ['goods_comment_id' => 'desc'])
            ->withoutField($field)
            ->withJoin($withJoin)
            ->with($with)
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select();

        // 账号资料匿名处理
        if (!is_client_admin()) {
            foreach ($temp as $value) {
                if ($value->getAttr('is_anon') !== 0) {
                    $value['getUser']->setAttr('username', auto_hid_substr($value['getUser']->getAttr('username')));
                    $value['getUser']->setAttr('nickname', auto_hid_substr($value['getUser']->getAttr('nickname')));
                }
            }
        }

        $result['items'] = $temp->append(['ip_address_region'])->toArray();
        self::keyToSnake(['getUser', 'getOrderGoods'], $result['items']);

        return $result;
    }
}
