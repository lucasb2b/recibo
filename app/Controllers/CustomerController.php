<?php

namespace App\Controllers;

use App\Models\Customer;
use Error;
use Respect\Validation\Validator as v;


class CustomerController extends Controller {
  
  public function customer($request, $response){
    if($request->isGet())
      return $this->container->view->render($response, 'customer.twig');

    $validation = $this->container->validator->validate($request, [
      'name' => v::notEmpty(),
      'cpf_cnpj' => v::notEmpty()
    ]);

    if($validation->failed()){
      $this->container->flash->addMessage('error', 'Houve um erro');
      return $response->withRedirect($this->container->router->pathFor('customer.index'));
    }else{
      Customer::create([
        'customer_name' => $request->getParam('name'),
        'cpf_cnpj' => $request->getParam('cpf_cnpj'),
        'telephone' => $request->getParam('phone')
      ]);
    $this->container->flash->addMessage('success', 'Cliente adicionado com sucesso!');
    }

    return $response->withRedirect($this->container->router->pathFor('customer.index'));
  }
}