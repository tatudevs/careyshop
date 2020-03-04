<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    Api结果输出
 *
 * @author      zxm <252404501@qq.com>
 * @date        2017/7/9
 */

namespace app\api\exception;

use think\Request;
use think\Config;
use think\Response;

class ApiOutput
{
    /**
     * 输出格式
     * @var string
     */
    public static $format = 'json';

    /**
     * 默认响应头
     * @var array
     */
    public static $header = [];

    public static function setCrossDomain()
    {
        self::$header['X-Powered-By'] = 'CareyShop/' . get_version();
//        $allowOrigin = json_decode(Config::get('allow_origin.value', 'system_info'), true);

        $origin = Request::instance()->header('origin');
        if (empty($origin)) {
            $origin = '*';
        }

        self::$header['Access-Control-Allow-Origin'] = $origin;
        self::$header['Access-Control-Allow-Methods'] = 'POST, GET, OPTIONS';
        self::$header['Access-Control-Allow-Credentials'] = 'true';
        self::$header['Access-Control-Allow-Headers'] = 'X-Requested-With, Content-Type, Accept';
        self::$header['Access-Control-Max-Age'] = '86400'; // 1天
    }

    /**
     * @param $result
     * @param $code
     * @return \think\response\Json
     */
    public static function outJson($result, $code)
    {
        return json($result, $code, self::$header);
    }

    /**
     * @param $result
     * @param $code
     * @return \think\response\Xml
     */
    public static function outXml($result, $code)
    {
        return xml($result, $code, self::$header, ['root_node' => 'careyshop']);
    }

    /**
     * @param $result
     * @param $code
     * @return \think\response\Jsonp
     */
    public static function outJsonp($result, $code)
    {
        return jsonp($result, $code, self::$header);
    }

    /**
     * @param $result
     * @param $code
     * @return \think\response\View
     */
    public static function outView($result, $code)
    {
        return view('common@/CareyShop', $result, [], $code);
    }

    /**
     * @param $result
     * @param $code
     * @return Response
     */
    public static function outResponse($result, $code)
    {
        if ($result instanceof Response) {
            $header = array_merge($result->getHeader(), self::$header);
            return $result->code($code)->header($header);
        }

        return $result;
    }

    /**
     * 数据输出
     * @access public
     * @param array  $data    数据
     * @param int    $code    状态码
     * @param bool   $error   正常或错误
     * @param string $message 提示内容
     * @return mixed
     */
    public static function outPut($data = [], $code = 200, $error = false, $message = '')
    {
        if (isset($data['callback_return_type']) && array_key_exists('is_callback', $data)) {
            // 自定义回调接口返回
            self::$format = $data['callback_return_type'];
            $result = $data['is_callback'];
        } else {
            // 正常请求返回
            $result = [
                'status'  => $code,
                'message' => $error == true ? empty($message) ? '发生未知异常' : $message : 'success',
            ];

            if (!$error) {
                $result['data'] = !empty($data) ? $data : Config::get('empty_result');
            } else {
                // 状态(非HTTPS始终为200状态,防止运营商劫持)
                $code = Request::instance()->isSsl() ? $code : 200;
            }
        }

        self::setCrossDomain();
        switch (self::$format) {
            case 'view':
                return self::outView($result, $code);

            case 'response':
                return self::outResponse($result, $code);

            case 'jsonp':
                return self::outJsonp($result, $code);

            case 'xml':
                return self::outXml($result, $code);

            case 'json':
            default:
                return self::outJson($result, $code);
        }
    }
}
