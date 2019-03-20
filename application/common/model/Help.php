<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    帮助文档模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2019/3/19
 */

namespace app\common\model;

class Help extends CareyShop
{
    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'help_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'help_id' => 'integer',
    ];

    /**
     * 验证帮助文档是否唯一
     * @access public
     * @param  array $data 外部数据
     * @return bool
     */
    public function uniqueHelpItem($data)
    {
        if (!$this->validateData($data, 'Help.unique')) {
            return false;
        }

        $map['router'] = ['eq', $data['router']];
        $map['ver'] = ['eq', $data['ver']];
        $map['module'] = ['eq', $data['module']];
        !isset($data['exclude_id']) ?: $map['help_id'] = ['neq', $data['exclude_id']];

        if (self::checkUnique($map)) {
            return $this->setError('帮助文档特征已存在');
        }

        return true;
    }

    /**
     * 添加一条帮助文档
     * @access public
     * @param  array $data 外部数据
     * @return bool/array
     */
    public function addHelpItem($data)
    {
        if (!$this->validateData($data, 'Help')) {
            return false;
        }

        // 验证帮助文档是否已存在
        if (!$this->uniqueHelpItem($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['help_id']);

        if (false !== $this->allowField(true)->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一条帮助文档
     * @access public
     * @param  array $data 外部数据
     * @return bool/array
     */
    public function setHelpItem($data)
    {
        if (!$this->validateData($data, 'Help.set')) {
            return false;
        }

        // 验证帮助文档是否已存在
        if (!$this->uniqueHelpItem($data)) {
            return false;
        }

        $map['help_id'] = ['eq', $data['help_id']];
        if (false !== $this->allowField(true)->save($data, $map)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 获取一条帮助文档
     * @access public
     * @param  array $data 外部数据
     * @return bool/array
     */
    public function getHelpItem($data)
    {
        if (!$this->validateData($data, 'Help.item')) {
            return false;
        }

        $result = self::get($data['help_id']);
        if (false !== $result) {
            return is_null($result) ? null : $result->toArray();
        }

        return false;
    }

    /**
     * 根据路由获取帮助文档
     * @access public
     * @param  array $data 外部数据
     * @return bool/array
     */
    public function getHelpRouter($data)
    {
        if (!$this->validateData($data, 'Help.router')) {
            return false;
        }

        $map['router'] = ['eq', $data['router']];
        $map['ver'] = ['eq', $data['ver']];
        $map['module'] = ['eq', $data['module']];

        $result = self::get(function ($query) use ($map) {
            $query->field('content,url')->where($map);
        });

        if (false !== $result) {
            return is_null($result) ? null : $result->toArray();
        }

        return false;
    }

    /**
     * 获取帮助文档列表
     * @access public
     * @param  array $data 外部数据
     * @return bool/array
     */
    public function getHelpList($data)
    {
        if (!$this->validateData($data, 'Help.list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['router']) ?: $map['router'] = ['eq', $data['router']];
        empty($data['ver']) ?: $map['ver'] = ['eq', $data['ver']];
        empty($data['module']) ?: $map['module'] = ['eq', $data['module']];
        empty($data['content']) ?: $map['content'] = ['like', '%' . $data['content'] . '%'];
        empty($data['url']) ?: $map['url'] = ['like', '%' . $data['url'] . '%'];

        $totalResult = $this->where($map)->count();
        if ($totalResult <= 0) {
            return ['total_result' => 0];
        }

        $result = self::all(function ($query) use ($data, $map) {
            // 翻页页数
            $pageNo = isset($data['page_no']) ? $data['page_no'] : 1;

            // 每页条数
            $pageSize = isset($data['page_size']) ? $data['page_size'] : config('paginate.list_rows');

            // 排序方式
            $orderType = !empty($data['order_type']) ? $data['order_type'] : 'desc';

            // 排序的字段
            $orderField = !empty($data['order_field']) ? $data['order_field'] : 'help_id';

            $query
                ->where($map)
                ->order([$orderField => $orderType])
                ->page($pageNo, $pageSize);
        });

        if (false !== $result) {
            return ['items' => $result->toArray(), 'total_result' => $totalResult];
        }

        return false;
    }
}
