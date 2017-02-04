<?php
/*
 * Sometime too hot the eye of heaven shines
 */

require 'vendor/autoload.php';

use Tadpole\Foundation\SocketServer;

$socket = new SocketServer();
$socket->start();