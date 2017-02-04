<?php
/*
 * Sometime too hot the eye of heaven shines
 */

require __DIR__.'/../../vendor/autoload.php';

$config = [
    config('settings')
];

$app = new \Slim\App($config);

$container = $app->getContainer();    //get container

$container['view'] = function ($container) {    // register template on container
    $view = new \Slim\Views\Twig(__DIR__ .'/../../views/', [
        'cache' => false
    ]);
    $view->addExtension(new \Slim\Views\TwigExtension(
        $container['router'],
        $container['request']->getUri()
    ));

    return $view;
};

return $app;