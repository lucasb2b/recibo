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

  private function getAllCustomer(){
    $customer = [
      'customers' => Customer::where('is_active', true)->get()
    ];

    return $customer;
  }

  public function managerCustomer($request, $response){
    if($request->isGet())
      $customers = $this->getAllCustomer();
      return $this->container->view->render($response, 'manager_customer.twig', $customers);
  }

  public function edit($request, $response, $params){
    $data = [
      'customer' => Customer::where('id_client', $params['id'])->get()
    ];

    return $this->container->view->render($response, 'edit_customer.twig', $data);
  }


  public function update($request, $response, $params){
    $customer = Customer::where('id_client', $params['id']);

    $validation = $this->container->validator->validate($request, [
      'name' => v::notEmpty(),
      'cpf_cnpj' => v::notEmpty()
    ]);

    if($validation->failed()){
      $this->container->flash->addMessage('error', 'erro ao editar o cliente');
      return $response->withRedirect($this->container->router->pathFor('customer.edit', ['id' => $customer->id_client]));
    }else{
      $customer->update([
        'customer_name' => $request->getParam('name'),
        'cpf_cnpj' => $request->getParam('cpf_cnpj'),
        'telephone' => $request->getParam('phone')
      ]);
      $this->container->flash->addMessage('success', 'Cliente atualizado com sucesso!');
      return $response->withRedirect($this->container->router->pathFor('customer.manager'));
    }
  }

  public function delete($request, $response, $params){
    $customer = Customer::where('id_client', $params['id'])->first();
    
    if($customer){
      $customer->update([
        'is_active' => false
      ]);
      $this->container->flash->addMessage('success', 'Cliente apagado com sucesso!');
      return $response->withRedirect($this->container->router->pathFor('customer.manager'));
    } else {
      $this->container->flash->addMessage('error', 'Erro ao apagar o cliente');
      return $response->withRedirect($this->container->router->pathFor('customer.manager'));
    }
  }

}