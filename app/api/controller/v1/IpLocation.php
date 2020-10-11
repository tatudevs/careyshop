<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    IP地址查询控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2019/11/20
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;
use careyshop\Ip2Region;

class IpLocation extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return void
     */
    protected static function initMethod()
    {
        self::$route = [
            // 查询一条IPv4信息(支持数字地址查询)
            'get.ip.location' => ['getIpLocation', false],
        ];
    }

    /**
     * 查询一条IPv4信息
     * @access public
     * @return array|bool
     */
    public function getIpLocation()
    {
        $result = [];
        $data = $this->getParams();
        $ip2region = new Ip2Region();

        if (isset($data['ip']) && is_array($data['ip'])) {
            foreach ($data['ip'] as $key => $value) {
                try {
                    $result[$key] = $ip2region->btreeSearch($value);
                    $result[$key]['status'] = 200;
                } catch (\exception $e) {
                    $result[$key]['error'] = $e->getMessage();
                    $result[$key]['status'] = 500;
                }
            }
        } else {
            try {
                $result = $ip2region->btreeSearch(!empty($data['ip']) ? $data['ip'] : $this->request->ip());
            } catch (\exception $e) {
                return $this->setError($e->getMessage());
            }
        }

        return $result;
    }
}
