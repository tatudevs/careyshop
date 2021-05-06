<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    AES
 *
 * @author      zxm <252404501@qq.com>
 * @date        2021/1/12
 */

namespace util;

class Aes
{
    /**
     * 加密
     * @access public
     * @param string $input  需要加密的字符串
     * @param string $key    密钥
     * @param bool   $isWrap 是否切段换行
     * @return string
     */
    public static function encrypt(string $input, string $key = 'careyshop', bool $isWrap = true): string
    {
        $key = substr(openssl_digest(openssl_digest($key, 'sha1', true), 'sha1', true), 0, 16);
        $data = openssl_encrypt($input, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        $result = base64_encode($data);

        return $isWrap ? preg_replace('/(.{64})/', '$1' . PHP_EOL, $result) : $result;
    }

    /**
     * 解密
     * @access public
     * @param string $input  需要解密的字符串
     * @param string $key    密钥
     * @param bool   $isWrap 是否去除切段换行
     * @return string
     */
    public static function decrypt(string $input, string $key = 'careyshop', bool $isWrap = true): string
    {
        $key = substr(openssl_digest(openssl_digest($key, 'sha1', true), 'sha1', true), 0, 16);
        $encrypted = base64_decode($isWrap ? str_replace(PHP_EOL, '', $input) : $input);
        $result = openssl_decrypt($encrypted, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);

        return $result === false ? '' : $result;
    }
}
