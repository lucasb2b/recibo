<?php

namespace App\Controllers;

class TestLoginController extends Controller {
  
  public function index($request, $response){
    return $this->container->view->render($response, 'test.twig');
  }
}