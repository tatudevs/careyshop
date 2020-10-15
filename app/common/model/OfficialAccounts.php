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

        // 允许修改字段与条件
        $field = ['name', 'remark', 'setting', 'status'];
        $map = [['official_accounts_id', '=', $data['official_accounts_id']]];

        $result = self::update($data, $map, $field);
        return $result->toArray();
    }

    /**
     * 获取一个公众号
     * @access public
     * @param array $data
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
}
