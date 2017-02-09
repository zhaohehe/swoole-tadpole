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

    protected $name = '混世魔王,蛟魔王,大鹏魔王,狮驼王,猕猴王,獝狨王,熊山君,
                       特处士,寅将军,黑熊精,白衣秀士,凌虚子,黄风怪,白骨夫人,
                       黄袍怪,金角大王,银角大王,九尾狐,青狮道人,红孩儿,虎力大仙,
                       鹿力大仙,羊力大仙,灵感大王,老鼋,独角王,如意真仙,蝎女妖,
                       六耳猕猴,铁扇公主,牛魔王,玉面公主,九头虫,黄眉老祖,大蟒精,
                       赛太岁,蜘蛛精,蜈蚣精,青狮魔王,白象魔王,大鹏魔王,虎威魔王,
                       狮吼魔王,狮毛怪,美后,国丈,半截观音,金钱豹王,黄狮精,九灵元圣,
                       辟寒大王,辟暑大王,辟尘大王';

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
                $nameArray = explode(',', $this->name);
                $name = $nameArray[rand(0, count($nameArray))];
                $status = [
                        'type' => 'update',
                        'id' => $client,
                        'angle' => $message["angle"] + 0,
                        'momentum' => $message["momentum"] + 0,
                        'x' => $message["x"] + 0,
                        'y' => $message["y"] + 0,
                        'life' => 1,
                        'size' => $stat->getGender($client) == 1 ? 20 : 4,
                        'name' => isset($message['name']) ? $message['name'] : $name,
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