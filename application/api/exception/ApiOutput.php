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
     * 是否回调输出
     * @var boolean
     */
    public static $isCallback = false;

    /**
     * 默认响应头
     * @var array
     */
    public static $header = [];

    /**
     * 构造方法
     * @access public
     */
    public function __construct()
    {
        self::$header['X-Powered-By'] = 'CareyShop/' . get_version();
    }

    public static function setCrossDomain()
    {
    }

    public static function outJson($result, $code)
    {
        $data = !self::$isCallback ? $result : $result['is_callback'];
        return json($data, $code, self::$header);
    }

    public static function outXml($result, $code)
    {
        $options = ['root_node' => 'careyshop'];
        $data = !self::$isCallback ? $result : $result['is_callback'];
        return xml($data, $code, self::$header, $options);
    }

    public static function outJsonp($result, $code)
    {
        $data = !self::$isCallback ? $result : $result['is_callback'];
        return jsonp($data, $code, self::$header);
    }

    public static function outView($result)
    {
        $data = !self::$isCallback ?: $result['is_callback'];
        return view('common@/CareyShop', $data);
    }

    public static function outResponse($result)
    {
        $data = !self::$isCallback ?: $result['is_callback'];
        if ($data instanceof Response) {
        }

        return $data;
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
//        // 头部
//        $header = [];
//        $header = array_merge($header, self::$poweredBy);
//

//
//        // 数据
//        $result = [
//            'status'  => $code,
//            'message' => $error == true ? empty($message) ? '发生未知异常' : $message : 'success',
//        ];
//
//        if (!$error) {
//            $result['data'] = !empty($data) ? $data : Config::get('empty_result');
//        } else {
//            // 状态(非HTTPS始终为200状态,防止运营商劫持)
//            $code = Request::instance()->isSsl() ? $code : 200;
//        }
//
//        switch (self::$format) {
//            case 'jsonp':
//                return jsonp($result, $code, $header);
//
//            case 'xml':
//                return xml($result, $code, $header, $options);
//
//            case 'json':
//            default:
//                return json($result, $code, $header);
//        }
    }
}
