<?php

namespace careyshop;

class System
{
    /**
     * 加载二次发开公共函数
     * @access public static
     * @return void
     */
    public static function loadFunction()
    {
        $appFunction = app_path() . 'function.php';
        if (is_file($appFunction)) {
            include_once $appFunction;
        }
    }
}
