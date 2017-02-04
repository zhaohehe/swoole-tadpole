<?php
/*
 * Sometime too hot the eye of heaven shines
 */

namespace Tadpole\Foundation;

use swoole_websocket_server;

class Gateway
{
    private $redis;

    private $server;

    private $set_name = 'pool';    //name of redis set

    public function __construct(swoole_websocket_server $server)
    {
        $redis = new Redis();
        $this->redis  = $redis->getClient();

        $this->server = $server;
    }

    public function join($client)
    {
        $this->redis->sadd($this->set_name, 'member:'.$client);
    }

    public function close($client)
    {
        $this->redis->srem($this->set_name, 'member:'.$client);
        $this->sendMessage(json_encode(['type'=>'closed', 'id' => $client]));
    }

//    public function closeAll()
//    {
//        $this->redis->srem($this->set_name, 'member:2');
//    }

    public function getAllTadpole()
    {
        return str_replace('member:', '', $this->redis->smembers($this->set_name));
    }

    public function getExcept($client)
    {
        $all = $this->getAllTadpole();

        foreach ($all as $key => $value) {
            if ($value == $client) unset($all[$key]);
        }
        return $all;
    }

    public function getNum()
    {
        return count($this->redis->smembers($this->set_name));
    }

    public function sendTo($client, $message)
    {
        $this->server->push($client, $message);
    }

    public function updateLocation($client, $message)
    {
        $allTadpole = $this->getAllTadpole();
        //$allTadpole = $this->getExcept($client);

        foreach ($allTadpole as $item) {
            $this->server->push($item, json_encode($message));
        }
    }

    public function sendMessage($message)
    {
        $allTadpole = $this->getAllTadpole();

        foreach ($allTadpole as $item) {
            $this->server->push($item, json_encode($message));
        }
    }
}