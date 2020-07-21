<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    API异常类接管
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/21
 */

namespace app\api\exception;

use think\exception\Handle;
use think\exception\HttpException;
use think\Response;
use Throwable;

class ApiException extends Handle
{
    /**
     * Render an exception into an HTTP response.
     * @param \think\Request $request
     * @param Throwable      $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        if ($e instanceof HttpException) {
            $statusCode = $e->getStatusCode();
        }

        if (!isset($statusCode)) {
            $statusCode = 500;
        }

        // "is_debug"键名存在时将由系统处理(附带错误记录)
        if (!$request->has('is_debug')) {
            return ApiOutput::outPut([], $statusCode, true, $e->getMessage());
        }

        return parent::render($request, $e);
    }
}
