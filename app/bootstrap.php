<?php

use Illuminate\Contracts\Pagination\Paginator;

session_start();

date_default_timezone_set('America/Sao_Paulo');

require __DIR__ . "/../vendor/autoload.php";

$app = new Slim\App([
  'settings' => [
    'displayErrorDetails' => true,
    'db' => [
      'driver' => 'mysql',
      'host' => 'localhost',
      'database' => 'recibo',
      'username' => 'root',
      'password' => '123456',
      'charset' => 'utf8',
      'collation' => 'utf8_general_ci',
      'prefix' => '',
    ]
  ]
]);

$container = $app->getContainer();

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['capsule'] = function($container){
  $capsule = new \Illuminate\Database\Capsule\Manager;
  $capsule->addConnection($container['settings']['db']);
  $capsule->setAsGlobal();
  $capsule->bootEloquent();
  return $capsule;
};

$container['flash'] = function($container){
  return new Slim\Flash\Messages;
};

$container['validator'] = function($container) {
  return new App\Validation\Validator;
};

$container['auth'] = function($container){
  return new App\Auth\Auth($container);
} ;

$container['view'] = function($container) {
  
  $view = new Slim\Views\Twig(__DIR__ . "/../resources/views", [
    'cache' => false,
    'debug' => true,
  ]);

  $view->getEnvironment()->addExtension(new \Twig\Extension\DebugExtension());

  $view->addExtension(new \Slim\Views\TwigExtension(
    $container->router,
    $container->request->getUri()
  ));

  $view->getEnvironment()->addGlobal('flash', $container->flash);

  $view->getEnvironment()->addGlobal('auth', [
    'check' => $container->auth->check(),
    'user' => $container->auth->user(),
  ]);

  return $view;
};

$container['HomeController'] = function($container) {
  return new App\Controllers\HomeController($container);
};

$container['AuthController'] = function($container) {
  return new App\Controllers\AuthController($container);
};

$container['CompanyController'] = function($container){
  return new App\Controllers\CompanyController($container);
};

$container['CustomerController'] = function($container){
  return new App\Controllers\CustomerController($container);
};

$container['ProductServiceController'] = function($container) {
  return new App\Controllers\ProductServiceController($container);
};

$container['InvoiceController'] = function($container) {
  return new App\Controllers\InvoiceController($container);
};

$container['TestLoginController'] = function($container) {
  return new App\Controllers\TestLoginController($container);
};

$container['upload_directory'] = __DIR__ . '/../public/uploads';


$app->add(new App\Middleware\DisplayInputErrorsMiddleware($container));

require __DIR__ . "/routes.php";