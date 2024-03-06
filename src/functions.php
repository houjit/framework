<?php declare(strict_types=1);
// +----------------------------------------------------------------------
// | houoole [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: amos <amos@houjit.com>
// +----------------------------------------------------------------------

if (! function_exists('getInstance')) {
    function getInstance($class)
    {
        return ($class)::getInstance();
    }
}
if (! function_exists('config')) {
    function config($name, $default = null)
    {
        return getInstance('\houoole\Config')->get($name, $default);
    }
}
