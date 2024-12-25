<?php

session_start();

date_default_timezone_set('America/Sao_Paulo');

require __DIR__ . "/../vendor/autoload.php";

$app = new Slim\App();

$container = $app->getContainer();

$container['view'] = function($container) {
  
  $view = new Slim\Views\Twig(__DIR__ . "/../resources/views", [
    'cache' => false
  ]);

  $view->addExtension(new \Slim\Views\TwigExtension(
    $container->router,
    $container->request->getUri()
  ));

 // $view->getEnvironment()->addGlobal('flash', $container->flash);

  return $view;
};

$container['HomeController'] = function($container) {
  return new App\Controllers\HomeController($container);
};

$container['AuthController'] = function($container) {
  return new App\Controllers\AuthController($container);
};


$app->add(new App\Middleware\DisplayInputErrorsMiddleware($container));

require __DIR__ . "/routes.php";