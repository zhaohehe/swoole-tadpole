<?php

/*
 * Sometime too hot the eye of heaven shines
 */

namespace Tadpole\Controllers;

use Interop\Container\ContainerInterface;

class tadpoleController
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function home($request, $response, $args)
    {
        if (is_null($_COOKIE['todpole_gender'])) {
            $gender = rand(0, 1);
            setcookie('todpole_gender', $gender);
        }

        return $this->container->view->render($response, 'index.twig');
    }
}