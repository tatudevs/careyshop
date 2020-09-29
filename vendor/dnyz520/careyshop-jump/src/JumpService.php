<?php

namespace careyshop;

use think\Config;
use think\Route;
use think\Service;

class JumpService extends Service
{
    public function boot()
    {
        $this->registerRoutes(function (Route $route) {
            $route->get('info/jump', function (Config $config) {
                $data = $config->get('extra.product', []);
                $data['conposer'] = 'Jump';
                $data['statement'] = base64_decode('5pys56uZ5Z+65LqOY2FyZXlzaG9w5ZWG5Z+O5qGG5p625pCt5bu6');

                foreach ($data as $key => $value) {
                    echo $key . '：' . $value;
                    echo '<br>';
                }
            })->ext('cs');
        });
    }
}
