<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    资源上传控制器
 *
 * @author      zxm <252404501@qq.com>
 * @date        2018/1/19
 */

namespace app\api\controller\v1;

use app\api\controller\CareyShop;
use app\common\service\Upload as UploadService;

class Upload extends CareyShop
{
    /**
     * 方法路由器
     * @access protected
     * @return void
     */
    protected static function initMethod()
    {
        self::$route = [
            // 获取上传模块列表
            'get.upload.module'   => ['getUploadModule', UploadService::class],
            // 获取上传地址
            'get.upload.url'      => ['getUploadUrl', UploadService::class],
            // 获取上传Token
            'get.upload.token'    => ['getUploadToken', UploadService::class],
            // 替换上传资源
            'replace.upload.item' => ['replaceUploadItem', UploadService::class],
            // 资源上传请求(第三方OSS只能单文件直传方式上传)
            'add.upload.list'     => ['addUploadList', UploadService::class],
            // 接收第三方推送数据
            'put.upload.data'     => ['putUploadData', UploadService::class],
        ];
    }
}
