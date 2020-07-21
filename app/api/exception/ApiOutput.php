<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    API结果输出
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/20
 */

namespace app\api\exception;

use think\facade\Config;
use think\facade\Request;
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

    /**
     * 响应头
     */
    public static function setCrossDomain()
    {
        $isOrigin = Request::has('origin', 'header');
        $allowOrigin = json_decode(Config::get('careyshop.system_info.allow_origin'), true);
        self::$header['X-Powered-By'] = 'CareyShop/' . get_version();

        // 未配置跨域或"origin"不存在时不返回访问控制(CORS)
        if (empty($allowOrigin) || !$isOrigin) {
            return;
        }

        $origin = Request::header('origin');
        if (empty($origin)) {
            /**
             * "origin"键名存在,但缺少键值,可能是由于30x跳转或其他各方面原因
             * 所以在这里对"origin"键值进行模拟生成,但也有被游览器拦截的可能
             * 比如Chrome对30x跳转后"origin=null"处理,导致诸多问题
             * 使用SSL就没有那么多麻烦,或者指定url协议头不进行重定向.
             */
            $referer = Request::header('referer');
            if (empty($referer)) {
                return;
            }

            $url = parse_url($referer);
            $origin = sprintf('%s://%s', $url['scheme'], $url['host']);
            isset($url['port']) && $origin .= ':' . $url['port'];
        }

        if (!in_array('*', $allowOrigin) && !in_array($origin, $allowOrigin)) {
            return;
        }

        self::$header['Access-Control-Allow-Origin'] = $origin;
        self::$header['Access-Control-Allow-Methods'] = 'GET, POST, OPTIONS';
        self::$header['Access-Control-Allow-Credentials'] = 'true';
        self::$header['Access-Control-Allow-Headers'] = 'Content-Type, Accept';
        self::$header['Access-Control-Expose-Headers'] = '*';
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
        header('X-Powered-By: '. self::$header['X-Powered-By']);
        return view('common@/CareyShop', ['data' => $result], $code);
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
        // 区分返回数据类型
        if (isset($data['callback_return_type']) && array_key_exists('is_callback', $data)) {
            // 自定义回调接口返回
            self::$format = $data['callback_return_type'];
            $result = $data['is_callback'];
        } else {
            // 正常请求返回
            $result = [
                'status'  => $code,
                'message' => $error == true ? empty($message) ? '发生未知异常' : $message : 'success',
                'data' => Config::get('app.empty_result'),
            ];

            if (!$error) {
                $result['data'] = !empty($data) ? $data : Config::get('app.empty_result');
            } else {
                // 状态(非HTTPS始终为200状态,防止运营商劫持)
                $code = Request::isSsl() ? $code : 200;
            }
        }

        // 按请求格式返回
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
