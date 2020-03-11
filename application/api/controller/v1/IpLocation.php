<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    IP地址查询控制器
 *
 * @author      zxm <252404501@qq.com>
 * @version     v1.1
 * @date        2019/11/20
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;
use util\Ip2Region;

class IpLocation extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return array
     */
    protected static function initMethod()
    {
        return [
            // 查询一条IPv4信息
            'get.ip.location' => ['getIpLocation', false],
        ];
    }

    /**
     * 查询一条IPv4信息
     * @access protected
     * @return array|bool
     */
    protected function getIpLocation()
    {
        $data = $this->getParams();
        $validate = $this->validate($data, 'IpLocation');

        if (true !== $validate) {
            return $this->setError($validate);
        }

        $result = [];
        $ip2region = new Ip2Region();

        foreach ($data['ip'] as $key => $value) {
            try {
                $result[$key] = $ip2region->btreeSearch($value);
                $result[$key]['status'] = 200;
            } catch (\exception $e) {
                $result[$key]['error'] = $e->getMessage();
                $result[$key]['status'] = 500;
            }
        }

        return $result;
    }
}
