<?php

namespace App\Controllers;

class AuthController extends Controller {

  public function login($request, $response){
    
    if($request->isGet())
      return $this->container->view->render($response, 'login.twig');

    if(!$this->container->auth->attempt(
      $request->getParam('username'),
      $request->getParam('password')
    )) {
      return $response->withRedirect($this->container->router->pathFor('auth.login'));
    }

    return $response->withRedirect($this->container->router->pathFor('home'));
  }
}