<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品咨询模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/9/1
 */

namespace app\common\model;

class GoodsConsult extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'goods_consult_id';

    /**
     * 是否需要自动写入时间戳
     * @var bool|string
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
        'goods_id',
        'is_delete',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'goods_consult_id' => 'integer',
        'goods_id'         => 'integer',
        'parent_id'        => 'integer',
        'user_id'          => 'integer',
        'type'             => 'integer',
        'is_show'          => 'integer',
        'is_anon'          => 'integer',
        'status'           => 'integer',
        'is_delete'        => 'integer',
    ];

    /**
     * hasMany cs_goods_consult
     * @access public
     * @return mixed
     */
    public function getAnswer()
    {
        return $this->hasMany(GoodsConsult::class, 'parent_id');
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
     * hasOne cs_goods
     * @access public
     * @return mixed
     */
    public function getGoods()
    {
        return $this
            ->hasOne(Goods::class, 'goods_id', 'goods_id')
            ->joinType('left');
    }

    /**
     * 关联查询NULL处理
     * @param mixed $value
     * @return mixed|\stdClass
     */
    public function getGetUserAttr($value)
    {
        return is_null($value) ? new \stdClass : $value;
    }

    /**
     * 关联查询NULL处理
     * @param mixed $value
     * @return mixed|\stdClass
     */
    public function getGetGoodsAttr($value)
    {
        return is_null($value) ? new \stdClass : $value;
    }

    /**
     * 添加一个新的商品咨询
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addConsultItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 获取用户当前登录Id,并定义写入字段
        $data['user_id'] = get_client_id();
        $field = ['goods_id', 'user_id', 'type', 'content', 'is_show', 'is_anon'];

        if ($this->allowField($field)->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 批量删除商品咨询
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delConsultList(array $data)
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        // 允许删除所有Id,包括咨询与回答
        $map[] = ['goods_consult_id', 'in', $data['goods_consult_id']];
        $map[] = ['is_delete', '=', 0];

        self::update(['is_delete' => 1], $map);
        return true;
    }

    /**
     * 批量设置是否前台显示
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setConsultShow(array $data)
    {
        if (!$this->validateData($data, 'show')) {
            return false;
        }

        // 只允许将咨询主题设置是否显示,设置回答毫无意义
        $map[] = ['goods_consult_id', 'in', $data['goods_consult_id']];
        $map[] = ['parent_id', '=', 0];
        $map[] = ['is_delete', '=', 0];

        self::update(['is_show' => $data['is_show']], $map);
        return true;
    }

    /**
     * 回复一个商品咨询
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function replyConsultItem(array $data)
    {
        if (!$this->validateData($data, 'reply')) {
            return false;
        }

        // 搜索条件
        $map[] = ['goods_consult_id', '=', $data['goods_consult_id']];
        $map[] = ['parent_id', '=', 0];
        $map[] = ['is_delete', '=', 0];

        $result = $this->where($map)->find();
        if (is_null($result)) {
            return $this->setError('数据不存在');
        }

        // 开启事务
        $this->startTrans();

        try {
            // 保存主数据
            $result->save(['status' => 1]);

            // 准备回复的内容
            $newData = $result->getData();
            unset($newData['goods_consult_id'], $newData['create_time']);

            $newData['parent_id'] = $data['goods_consult_id'];
            $newData['content'] = $data['content'];
            $result = self::create($newData);

            $this->commit();
            return $result->toArray();
        } catch (\Exception $e) {
            $this->rollback();
            return $this->setError($e->getMessage());
        }
    }

    /**
     * 获取一个商品咨询问答明细
     * @access public
     * @param array $data 外部数据
     * @return false|mixed
     * @throws
     */
    public function getConsultItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        // 搜索条件
        $map[] = ['goods_consult.goods_consult_id', '=', $data['goods_consult_id']];
        $map[] = ['goods_consult.parent_id', '=', 0];
        $map[] = ['goods_consult.is_delete', '=', 0];
        is_client_admin() ?: $map[] = ['goods_consult.user_id', '=', get_client_id()];

        // 关联查询
        $withJoin['getUser'] = ['username', 'nickname', 'level_icon', 'head_pic'];
        $withJoin['getGoods'] = ['goods_id', 'name', 'attachment'];

        $with['get_answer'] = function ($query) {
            $query->withoutField('is_show,status,type')->where('is_delete', '=', 0);
        };

        $result = $this->withJoin($withJoin)->with($with)->where($map)->find();
        if ($result) {
            $temp = [$result->toArray()];
            self::keyToSnake(['getUser', 'getGoods'], $temp);

            return $temp[0];
        }

        return false;
    }

    /**
     * 获取商品咨询列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getConsultList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map[] = ['goods_consult.parent_id', '=', 0];
        $map[] = ['goods_consult.is_delete', '=', 0];

        // 管理组:为空表示获取所有的咨询列表
        // 客户组:为空表示获取当前客户自己的咨询列表
        empty($data['goods_id']) ?: $map[] = ['goods_consult.goods_id', '=', $data['goods_id']];

        // 区分前后台
        if (is_client_admin()) {
            // 允许管理组根据用户搜索商品咨询
            if (!empty($data['account'])) {
                $map[] = ['getUser.username|getUser.nickname', '=', $data['account']];
            }
        } else {
            // 当goods_id为空,表示顾客组想要获取属于他自己的咨询列表
            if (empty($data['goods_id'])) {
                $map[] = ['getUser.user_id', '=', get_client_id()];
                unset($data['is_show']);
            } else {
                // 否则表示获取指定商品下的咨询列表,所以需要加上is_show为条件
                $data['is_show'] = 1;
            }
        }

        is_empty_parm($data['type']) ?: $map[] = ['goods_consult.type', '=', $data['type']];
        is_empty_parm($data['status']) ?: $map[] = ['goods_consult.status', '=', $data['status']];
        is_empty_parm($data['is_show']) ?: $map[] = ['goods_consult.is_show', '=', $data['is_show']];
        empty($data['content']) ?: $map[] = ['goods_consult.content', 'like', '%' . $data['content'] . '%'];

        $result['total_result'] = $this->withJoin('getUser')->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 关联查询
        $withJoin['getUser'] = ['username', 'nickname', 'level_icon', 'head_pic'];

        // 判断是否需要关联Goods(当指定商品后不再关联"getGoods")
        !empty($data['goods_id']) ?: $withJoin['getGoods'] = ['goods_id', 'name', 'attachment'];

        // 是否需要提取答复列表
        $with = [];
        if (isset($data['is_answer']) && $data['is_answer'] == 1) {
            $with['get_answer'] = function ($query) {
                $query->withoutField('is_show,status')->where('is_delete', '=', 0);
            };
        }

        // 实际查询
        $temp = $this->setDefaultOrder(['goods_consult_id' => 'desc'])
            ->withJoin($withJoin)
            ->with($with)
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select();

        // 账号资料匿名处理
        if (!is_client_admin()) {
            foreach ($temp as $value) {
                if ($value['is_anon'] !== 0 && !empty($value['getUser'])) {
                    $value['getUser']->setAttr('username', auto_hid_substr($value['getUser']->getAttr('username')));
                    $value['getUser']->setAttr('nickname', auto_hid_substr($value['getUser']->getAttr('nickname')));
                }
            }
        }

        $result['items'] = $temp->toArray();
        self::keyToSnake(['getUser', 'getGoods'], $result['items']);

        return $result;
    }
}
