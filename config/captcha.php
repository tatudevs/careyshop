<?php
/**
 * @copyright   Copyright (c) http://careyshop.cn All rights reserved.
 *
 * CareyShop    验证码设置
 *
 * @author      zxm <252404501@qq.com>
 * @date        2020/7/22
 */

return [
    //验证码位数
    'length'   => 4,
    // 验证码字符集合
    'codeSet'  => '0123456789',
    // 验证码过期时间
    'expire'   => 120,
    // 是否使用中文验证码
    'useZh'    => false,
    // 是否使用算术验证码
    'math'     => false,
    // 是否使用背景图
    'useImgBg' => false,
    //验证码字符大小
    'fontSize' => 25,
    // 是否使用混淆曲线
    'useCurve' => false,
    //是否添加杂点
    'useNoise' => true,
    // 验证码字体 不设置则随机
    'fontttf'  => '1.ttf',
    //背景颜色
    'bg'       => [255, 255, 255],
    // 验证码图片高度
    'imageH'   => 0,
    // 验证码图片宽度
    'imageW'   => 0,

    // 添加额外的验证码设置
    // verify => [
    //     'length'=>4,
    //    ...
    //],
];
