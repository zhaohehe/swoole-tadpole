<?php

/*
 * Sometime too hot the eye of heaven shines
 */

namespace Tadpole\Foundation;

use swoole_websocket_server;
use Tadpole\Stat;

class SocketServer
{
    private $gateWay;

    private $socketServer;

    public function __construct()
    {
        $socket_server = config('web_socket.server');

        $this->socketServer = new swoole_websocket_server($socket_server['host'], $socket_server['port']);
        $this->socketServer->set([
            'worker_num' => 8,
            'daemonize'  => false,
        ]);

        $this->gateWay = new Gateway($this->socketServer);

        $this->socketServer->on('open', [$this, 'onOpen']);
        $this->socketServer->on('message', [$this, 'message']);
        $this->socketServer->on('close', [$this, 'onClose']);
    }


    public function message($socketServer, $frame)
    {
        $client = $frame->fd;

        //get message
        $message = json_decode($frame->data, true);
        if (!$message) return '';

        //get message type
        $type = $message['type'];

        switch($type) {
            case 'login':
                break;

            //update location
            case 'update':
                $stat = new Stat();
                $status = [
                        'type' => 'update',
                        'id' => $client,
                        'angle' => $message["angle"] + 0,
                        'momentum' => $message["momentum"] + 0,
                        'x' => $message["x"] + 0,
                        'y' => $message["y"] + 0,
                        'life' => 1,
                        'size' => $stat->getGender($client) == 1 ? 20 : 4,
                        'name' => isset($message['name']) ? $message['name'] : 'Guest.' . $client,
                        'authorized' => false,
                ];
                return $this->gateWay->updateLocation($client, $status);

            // send message
            case 'message':
                $newMessage = [
                    'type' => 'message',
                    'id' => $client,
                    'message' => $message['message'],
                ];
                $this->gateWay->sendMessage($newMessage);
        }
    }



    public function onOpen($socketServer, $request)
    {
        $client = $request->fd;

        //将当前连接加入连接池
        $this->gateWay->join($client);

        //设置性别
        $stat = new Stat();
        $stat->setGender($client);

        //响应客户端的连接
        $welcome = [
            'type' => 'welcome',
            'id'   => $client
        ];
        $this->gateWay->sendTo($client, json_encode($welcome));
    }


    public function onClose($socketServer, $fd)
    {
        $this->gateWay->close($fd);    //todo : remove
        echo "client-{$fd} is closed\n";
    }


    public function start()
    {
        $this->socketServer->start();
    }
}