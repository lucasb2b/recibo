<?php

use App\Middleware\AuthMiddleware;

//$app->get('/', 'HomeController:index')->setName('home');

$app->get('/', function($request, $response){
  return $this->view->render($response, 'index.twig');
})->setName('home')->add(new \App\Middleware\AuthMiddleware($container));

$app->group('/auth', function($app){
  $app->map(['GET', 'POST'], '/login', 'AuthController:login')->setName('auth.login');
  $app->get('/logout', 'AuthController:logout')->setName('auth.logout');
});

$app->group('/company', function($app){
  $app->get('', 'CompanyController:company')->setName('company.index');
})->add(new AuthMiddleware($container));

$app->group('/testlogin', function($app) {
  $app->map(['GET', 'POST'], '/test', 'TestController:index')->setName('test.index');
})->add(new AuthMiddleware($container));