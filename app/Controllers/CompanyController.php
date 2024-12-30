<?php

namespace App\Controllers;

class CompanyController extends Controller {
  
  public function company($request, $response){
    return $this->container->view->render($response, 'company.twig');
  }
}