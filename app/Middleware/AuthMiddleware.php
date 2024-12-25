<?php

namespace App\Middleware;

class AuthMiddleware extends Middleware {
  public function __innoke($request, $response, $next) {
      if(!$this->container->auth->check())
        return $response->withRedirect($this->container->router->pathFor('auth.login'));

        $response = $next($request, $response);
        return $response;
  }
}