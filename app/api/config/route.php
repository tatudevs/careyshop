<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    API路由配置文件
 *
 * @author      zxm <252404501@qq.com>
 * @version     v1.1
 * @date        2020/7/20
 */

use think\facade\Route;

Route::rule(':version/:controller', ':version.:controller/index');
