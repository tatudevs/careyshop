<?php

namespace careyshop;

use think\Config;
use think\Route;
use think\Service;

class Ip2RegionService extends Service
{
    public function boot()
    {
        $this->registerRoutes(function (Route $route) {
            $route->get('info/ip2region', function (Config $config) {
                $data = $config->get('extra.product', []);
                $data['conposer'] = 'Ip2Region';
                $data['statement'] = base64_decode('5pys56uZ5Z+65LqOQ2FyZXlTaG9w5ZWG5Z+O5qGG5p6257O757uf5p6E5bu6');

                foreach ($data as $key => $value) {
                    echo $key . '：' . $value;
                    echo '<br>';
                }
            })->ext('cs');
        });
    }
}
