<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    公众号模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/10/13
 */

namespace app\common\model;

class OfficialAccounts extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'official_accounts_id';

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'official_accounts_id',
        'code',
        'model',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'official_accounts_id' => 'integer',
        'code'                 => 'integer',
        'setting'              => 'array',
        'status'               => 'integer',
    ];

    /**
     * 生成唯一公众号code
     * @access private
     * @return string
     */
    private function getOfficialCode()
    {
        do {
            $code = rand_number(8);
        } while (self::checkUnique(['code' => $code]));

        return $code;
    }

    /**
     * 添加一个公众号
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function addOfficialItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 初始化部分数据
        $data['code'] = $this->getOfficialCode();
        !empty($data['setting']) ?: $data['setting'] = [];
        unset($data['official_accounts_id']);

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }

    /**
     * 编辑一个公众号
     * @access public
     * @param array $data 外部数据
     * @return array|false
     */
    public function setOfficialItem(array $data)
    {
        if (!$this->validateData($data, 'set', true)) {
            return false;
        }

        if (!empty($data['name'])) {
            $map[] = ['official_accounts_id', '<>', $data['official_accounts_id']];
            $map[] = ['name', '=', $data['name']];

            if (self::checkUnique($map)) {
                return $this->setError('公众号名称已存在');
            }
        }

        if (isset($data['setting']) && '' == $data['setting']) {
            $data['setting'] = [];
        }

        // 允许修改字段与条件
        $field = ['name', 'remark', 'setting', 'status'];
        $map = [['official_accounts_id', '=', $data['official_accounts_id']]];

        $result = self::update($data, $map, $field);
        return $result->toArray();
    }

    /**
     * 获取一个公众号
     * @access public
     * @param array $data 外部数据
     * @return array|false|null
     * @throws
     */
    public function getOfficialItem(array $data)
    {
        if (!$this->validateData($data, 'item')) {
            return false;
        }

        $result = $this->find($data['official_accounts_id']);
        return is_null($result) ? null : $result->toArray();
    }

    /**
     * 获取公众号列表
     * @access public
     * @param array $data 外部数据
     * @return array|false
     * @throws
     */
    public function getOfficialList(array $data)
    {
        if (!$this->validateData($data, 'list')) {
            return false;
        }

        // 搜索条件
        $map = [];
        empty($data['name']) ?: $map[] = ['name', 'like', '%' . $data['name'] . '%'];
        empty($data['code']) ?: $map[] = ['code', '=', $data['code']];
        empty($data['model']) ?: $map[] = ['model', '=', $data['model']];
        is_empty_parm($data['status']) ?: $map[] = ['status', '=', $data['status']];

        $result['total_result'] = $this->where($map)->count();
        if ($result['total_result'] <= 0) {
            return $result;
        }

        // 实际查询
        $result['items'] = $this->setDefaultOrder(['official_accounts_id' => 'desc'])
            ->where($map)
            ->withSearch(['page', 'order'], $data)
            ->select()
            ->toArray();

        return $result;
    }

    /**
     * 批量删除公众号
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function delOfficialList(array $data)
    {
        if (!$this->validateData($data, 'del')) {
            return false;
        }

        self::destroy($data['official_accounts_id']);
        return true;
    }

    /**
     * 批量设置公众号状态
     * @access public
     * @param array $data 外部数据
     * @return bool
     */
    public function setOfficialStatus(array $data)
    {
        if (!$this->validateData($data, 'status')) {
            return false;
        }

        $map[] = ['official_accounts_id', 'in', $data['official_accounts_id']];
        self::update(['status' => $data['status']], $map);

        return true;
    }
}
