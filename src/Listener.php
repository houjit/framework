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
namespace houoole;

class Listener
{
    private static $instance;

    private static $config;

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
            self::$config = Config::getInstance()->get('listeners');
        }
        return self::$instance;
    }

    public function listen($listener, ...$args)
    {
        $listeners = isset(self::$config[$listener]) ? self::$config[$listener] : [];
        while ($listeners) {
            [$class, $func] = array_shift($listeners);
            try {
                $class::getInstance()->{$func}(...$args);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }
    }
}
