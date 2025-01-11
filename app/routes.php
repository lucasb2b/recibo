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
  $app->map(['GET', 'POST'], '', 'CompanyController:company')->setName('company.index');
  //$app->post('/createCompany', 'CompanyController:createCompany')->setName('company.create');
})->add(new AuthMiddleware($container));

$app->group('/customer', function($app){
  $app->map(['GET', 'POST'], '', 'CustomerController:customer')->setName('customer.index');
  $app->map(['GET', 'POST'], '/manager', 'CustomerController:managerCustomer')->setName('customer.manager');
  $app->get('/manager/edit/{id}', 'CustomerController:edit')->setName('customer.edit');
  $app->post('/manager/edit/{id}', 'CustomerController:update');
  $app->get('/manager/delete/{id}', 'CustomerController:delete')->setName('customer.delete');

});

$app->group('/productsServices', function($app){
  $app->map(['GET', 'POST'], '', 'ProductServiceController:productService')->setName('productService.index');
  $app->map(['GET', 'POST'], '/manager', 'ProductServiceController:managerProductService')->setName('productService.manager');
  $app->get('/manager/edit/{id}', 'ProductServiceController:edit')->setName('productService.edit');
  $app->post('/manager/edit/{id}', 'ProductServiceController:update');
  $app->get('/manager/delete/{id}', 'ProductServiceController:delete')->setName('productService.delete');
});

$app->group('/testlogin', function($app) {
  $app->map(['GET', 'POST'], '/test', 'TestController:index')->setName('test.index');
})->add(new AuthMiddleware($container));