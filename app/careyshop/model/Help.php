<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    帮助文档模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/3
 */

namespace app\careyshop\model;

class Help extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'help_id';

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
     * @param array $data 外部数据
     * @return bool
     */
    public function uniqueHelpItem(array $data): bool
    {
        if (!$this->validateData($data, 'unique')) {
            return false;
        }

        $map[] = ['router', '=', $data['router']];
        $map[] = ['ver', '=', $data['ver']];
        $map[] = ['module', '=', $data['module']];
        !isset($data['exclude_id']) ?: $map[] = ['help_id', '<>', $data['exclude_id']];

        if (self::checkUnique($map)) {
            return $this->setError('帮助文档特征已存在');
        }

        return true;
    }

    /**
     * 添加一条帮助文档
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addHelpItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 验证帮助文档是否已存在
        if (!$this->uniqueHelpItem($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['help_id']);

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一条帮助文档
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setHelpItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        // 验证帮助文档是否已存在
        if (!$this->uniqueHelpItem($data)) {
            return false;
        }

        $map[] = ['help_id', '=', $data['help_id']];
        $result = self::update($data, $map);

        return $result->toArray();
    }

    /**
     * 获取一条帮助文档
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getHelpItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $result = $this->find($data['help_id']);
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 根据路由获取帮助文档
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getHelpRouter(array $data)
    {
        if (!$this->validateData($data, 'router')) {
            return false;
        }

        $map[] = ['router', '=', $data['router']];
        $map[] = ['ver', '=', $data['ver']];
        $map[] = ['module', '=', $data['module']];

        $result = $this->field('content,url')->where($map)->find();
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 获取帮助文档列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getHelpList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['router']) ?: $map[] = ['router', '=', $data['router']];
        empty($data['ver']) ?: $map[] = ['ver', '=', $data['ver']];
        empty($data['module']) ?: $map[] = ['module', '=', $data['module']];
        empty($data['content']) ?: $map[] = ['content', 'like', '%' . $data['content'] . '%'];
        empty($data['url']) ?: $map[] = ['url', 'like', '%' . $data['url'] . '%'];

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        $result['items'] = $this->setDefaultOrder(['help_id' => 'desc'])
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }
}
