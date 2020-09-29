<?php

namespace careyshop;

use think\Config;
use think\Route;
use think\Service;

class CaptchaService extends Service
{
    public function boot()
    {
        $this->registerRoutes(function (Route $route) {
            $route->get('info/captcha', function (Config $config) {
                $data = $config->get('extra.product', []);
                $data['conposer'] = 'Captcha';
                $data['statement'] = base64_decode('5pys56uZ5oqA5pyv5Z+65LqOQ2FyZXlTaG9w5ZWG5Z+O5qGG5p6257O757uf');

                foreach ($data as $key => $value) {
                    echo $key . '：' . $value;
                    echo '<br>';
                }
            })->ext('cs');
        });
    }
}
