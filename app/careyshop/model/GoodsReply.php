<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    商品评价回复模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/9/1
 */

namespace app\careyshop\model;

use think\facade\Event;

class GoodsReply extends CareyShop
{
    /**
     * 主键
     * @var array|string
     */
    protected $pk = 'goods_reply_id';

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
     * @var string[]
     */
    protected $readonly = [
        'goods_reply_id',
        'goods_comment_id',
        'user_id',
        'create_time',
    ];

    /**
     * 隐藏属性
     * @var string[]
     */
    protected $hidden = [
        'user_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var string[]
     */
    protected $type = [
        'goods_reply_id'   => 'integer',
        'goods_comment_id' => 'integer',
        'reply_type'       => 'integer',
        'user_id'          => 'integer',
    ];

    /**
     * 对商品评价添加一个回复(管理组不参与评价回复)
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addReplyItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 搜索条件
        $mapComment[] = ['goods_comment_id', '=', $data['goods_comment_id']];

        // 获取被回复者Id,如果"goods_reply_id"空则默认获取主评价者Id
        if (empty($data['goods_reply_id'])) {
            $userId = GoodsComment::where($mapComment)->value('user_id');
        } else {
            $userId = $this->where('goods_reply_id', '=', $data['goods_reply_id'])->value('user_id');
        }

        // 避免无关字段及初始化数据
        unset($data['goods_reply_id']);
        $data['user_id'] = get_client_id();
        $data['nick_name'] = get_client_nickname();

        // 是否进行匿名处理
        if (!empty($data['is_anon'])) {
            $data['nick_name'] = auto_hid_substr($data['nick_name']);
        }

        if ($this->save($data)) {
            if (!empty($userId)) {
                (new Message())->inAddMessageItem([
                    'type'    => 0,
                    'member'  => 1,
                    'title'   => '您的商品评价收到了最新回复',
                    'content' => $data['nick_name'] . ' 对您的评价进行了回复：' . $data['content'],
                ], [$userId], 0);

                Event::trigger('CustomerAdvisoryComment', ['user_id' => $userId]);
            }

            GoodsComment::where($mapComment)->inc('reply_count')->update();
            return $this->hidden(['is_anon'])->toArray();
        }

        return false;
    }

    /**
     * 批量删除商品评价的回复
     * @access public
     * @param array $data 外部数据
     * @return bool
     * @throws
     */
    public function delReplyList(array $data): bool
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        $result = $this->select($data['goods_reply_id']);
        if (!$result->isEmpty()) {
            foreach ($result as $value) {
                $map = [['goods_comment_id', '=', $value->getAttr('goods_comment_id')]];
                GoodsComment::where($map)->dec('reply_count')->update();
                $value->delete();
            }
        }

        return true;
    }

    /**
     * 获取商品评价回复列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getReplyList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 判断商品评价是否存在
        $map[] = ['goods_comment_id', '=', $data['goods_comment_id']];
        $map[] = ['type', '=', GoodsComment::COMMENT_TYPE_MAIN];
        $map[] = ['is_delete', '=', 0];
        is_client_admin() ?: $map[] = ['is_show', '=', 1];

        $result['total_result'] = 0;
        if (!GoodsComment::checkUnique($map)) {
            return $result;
        }

        // 开始获取评价回复数据
        $map = [['goods_comment_id', '=', $data['goods_comment_id']]];
        $result['total_result'] = $this->where($map)->count();

        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['goods_reply_id' => 'asc'])
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }
}
