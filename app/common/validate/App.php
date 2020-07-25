<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    应用管理验证器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/23
 */

namespace app\common\validate;

class App extends CareyShop
{
    /**
     * 验证规则
     * @var array
     */
    protected $rule = [
        'app_id'     => 'integer|gt:0',
        'app_name'   => 'require|max:30|unique:app,app_name,0,app_id',
        'captcha'    => 'in:0,1',
        'status'     => 'in:0,1',
        'exclude_id' => 'integer|gt:0',
    ];

    /**
     * 字段描述
     * @var array
     */
    protected $field = [
        'app_id'     => '应用编号',
        'app_name'   => '应用名称',
        'captcha'    => '应用验证码',
        'status'     => '应用状态',
        'exclude_id' => '应用排除Id',
    ];

    /**
     * 场景验证
     * @return App
     */
    public function sceneItem()
    {
        return $this->only(['app_id'])
            ->append('app_id', 'require');
    }

    public function sceneList()
    {
        return $this->only(['app_name', 'status'])
            ->remove('app_name', ['require', 'unique']);
    }

    public function sceneSet()
    {
        return $this->append('app_id', 'require')
            ->remove('app_name', 'unique');
    }

    public function sceneDel()
    {
        return $this->only(['app_id'])
            ->remove('app_id', ['integer', 'gt'])
            ->append('app_id', 'require|arrayHasOnlyInts');
    }

    public function sceneUnique()
    {
        return $this->only(['app_name', 'exclude_id'])
            ->remove('app_name', 'unique');
    }

    public function sceneCaptcha()
    {
        return $this->only(['app_id', 'captcha'])
            ->remove('app_id', ['integer', 'gt'])
            ->append('app_id', 'require|arrayHasOnlyInts')
            ->append('captcha', 'require');
    }

    public function sceneStatus()
    {
        return $this->only(['app_id', 'status'])
            ->remove('app_id', ['integer', 'gt'])
            ->append('app_id', 'require|arrayHasOnlyInts')
            ->append('status', 'require');
    }
}
