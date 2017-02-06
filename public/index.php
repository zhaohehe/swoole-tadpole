<?php
/*
 * Sometime too hot the eye of heaven shines
 */

$app = require '../tadpole/bootstrap.php';

$tadpoleController = 'Tadpole\Controllers\tadpoleController';


//tadpole chat room
$app->get('/home', $tadpoleController.':home');


$app->run();