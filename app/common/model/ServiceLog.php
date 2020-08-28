<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    售后日志模型
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/8/3
 */

namespace app\common\model;

class ServiceLog extends CareyShop
{
    /**
     * 主键
     * @var string
     */
    protected $pk = 'service_log_id';

    /**
     * 是否需要自动写入时间戳
     * @var bool
     */
    protected $autoWriteTimestamp = true;

    /**
     * 更新日期字段
     * @var bool/string
     */
    protected $updateTime = false;

    /**
     * 只读属性
     * @var array
     */
    protected $readonly = [
        'service_log_id',
        'order_service_id',
    ];

    /**
     * 字段类型或者格式转换
     * @var array
     */
    protected $type = [
        'service_log_id'   => 'integer',
        'order_service_id' => 'integer',
        'client_type'      => 'integer',
    ];

    /**
     * 添加售后操作日志
     * @access public
     * @param array $data 外部数据
     * @return false|array
     */
    public function addServiceLogItem(array $data)
    {
        if (!$this->validateData($data)) {
            return false;
        }

        // 避免无关字段
        unset($data['service_log_id']);
        $data['action'] = get_client_name();
        $data['client_type'] = get_client_type();

        if ($this->save($data)) {
            return $this->toArray();
        }

        return false;
    }
}
