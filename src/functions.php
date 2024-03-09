<?php declare(strict_types=1);
// +----------------------------------------------------------------------
// | houoole [ 厚匠科技 https://www.houjit.com/ ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2024 https://www.houjit.com/hou-swoole All rights reserved.
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
if (!function_exists('check_ip_allowed')) {
    /**
     * 检测IP是否允许
     * @param string $ip IP地址
     */
    function check_ip_allowed($ip = null)
    {
        $ips = '127.0.0.1';
        $ip = is_null($ip) ? $ips : $ip;
        $forbiddenipArr = ['127.0.0.1','58.39.98.132','47.104.8.8'];
        $forbiddenipArr = !$forbiddenipArr ? [] : $forbiddenipArr;
        $forbiddenipArr = is_array($forbiddenipArr) ? $forbiddenipArr : array_filter(explode("\n", str_replace("\r\n", "\n", $forbiddenipArr)));
        if ($forbiddenipArr && !in_array($ip,$forbiddenipArr))
        {
            return ['请求无权访问', 'html', 403];
        }
    }
}