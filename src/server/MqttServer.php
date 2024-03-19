<?php declare(strict_types=1);
// +----------------------------------------------------------------------
// | Houoole [ 厚匠科技 https://www.houjit.com/ ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2024 https://www.houjit.com/hou-swoole All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: amos <amos@houjit.com>
// +----------------------------------------------------------------------
namespace houoole\server;
use houoole\App;
use houoole\Listener;
use houoole\server\protocol\Mqtt;
use Swoole\Server;

class MqttServer
{
    protected $_server;

    protected $_config;

    /**
     * Server constructor.
     */
    public function __construct()
    {
        $config = config('servers');
        $mqttConfig = $config['mqtt'];
        $this->_config = $mqttConfig;
        $this->_server = new Server($mqttConfig['ip'], $mqttConfig['port'], $config['mode']);
        $this->_server->set($mqttConfig['settings']);

        $this->_server->on('Start', [$this, 'onStart']);
        $this->_server->on('workerStart', [$this, 'onWorkerStart']);
        $this->_server->on('Receive', [$this, 'onReceive']);
        foreach ($mqttConfig['callbacks'] as $eventKey => $callbackItem) {
            [$class, $func] = $callbackItem;
            $this->_server->on($eventKey, [$class, $func]);
        }
        $this->_server->start();
    }

    public function onStart($server)
    {
        App::echoSuccess("Swoole Mqtt Server running：mqtt://{$this->_config['ip']}:{$this->_config['port']}");
        Listener::getInstance()->listen('start', $server);
    }

    public function onWorkerStart(Server $server, int $workerId)
    {
        Listener::getInstance()->listen('workerStart', $server, $workerId);
    }

    public function onReceive($server, $fd, $fromId, $data)
    {
        try {
            $data = Mqtt::decode($data);
            if (is_array($data) && isset($data['cmd'])) {
                switch ($data['cmd']) {
                    case Mqtt::PINGREQ: // 心跳请求
                        [$class, $func] = $this->_config['receiveCallbacks'][Mqtt::PINGREQ];
                        $obj = new $class();
                        if ($obj->{$func}($server, $fd, $fromId, $data)) {
                            // 返回心跳响应
                            $server->send($fd, Mqtt::getAck(['cmd' => 13]));
                        }
                        break;
                    case Mqtt::DISCONNECT: // 客户端断开连接
                        [$class, $func] = $this->_config['receiveCallbacks'][Mqtt::DISCONNECT];
                        $obj = new $class();
                        if ($obj->{$func}($server, $fd, $fromId, $data)) {
                            if ($server->exist($fd)) {
                                $server->close($fd);
                            }
                        }
                        break;
                    case Mqtt::CONNECT: // 连接
                    case Mqtt::PUBLISH: // 发布消息
                    case Mqtt::SUBSCRIBE: // 订阅
                    case Mqtt::UNSUBSCRIBE: // 取消订阅
                        [$class, $func] = $this->_config['receiveCallbacks'][$data['cmd']];
                        $obj = new $class();
                        $obj->{$func}($server, $fd, $fromId, $data);
                        break;
                }
            } else {
                $server->close($fd);
            }
        } catch (\Throwable $e) {
            $server->close($fd);
        }
    }
}
