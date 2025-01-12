<?php

namespace App\Controllers;

use App\Models\ProductService;
use Respect\Validation\Validator as v;

class ProductServiceController extends Controller {
  
  public function productService($request, $response){
    if($request->isGet())
      return $this->container->view->render($response, 'product_service.twig');

    $validation = $this->container->validator->validate($request, [
      'product_service' => v::notEmpty(),
      'price' => v::notEmpty(),
      'units' => v::notEmpty(),
    ]);

    if($validation->failed()){
      $this->container->flash->addMessage('error', 'Houve um erro');
      return $response->withRedirect($this->container->router->pathFor('customer.index'));
    }else{
      ProductService::create([
        'product_service' => $request->getParam('product_service'),
        'type' => $request->getParam('item-type'),
        'price' => $request->getParam('price'),
        'units' => $request->getParam('units')
      ]);
    $this->container->flash->addMessage('success', 'Produto ou serviço adicionado com sucesso!');
    }

    return $response->withRedirect($this->container->router->pathFor('productService.index'));
  }

  private function getAllProductsServices(){
    $productsServices = [
      'productsServices' => ProductService::where('is_active', true)->get()
    ];

    return $productsServices;
  }

  public function managerProductService($request, $response){
    if($request->isGet())
      $productsServices = $this->getAllProductsServices();
      return $this->container->view->render($response, 'manager_product_service.twig', $productsServices);
  }

  public function edit($request, $response, $params){
    $data = [
      'productService' => ProductService::where('id_product_service', $params['id'])->get()
    ];

    return $this->container->view->render($response, 'edit_product_service.twig', $data);
  }

  public function update($request, $response, $params){
    $productService = ProductService::find($params['id']);

    $validation = $this->container->validator->validate($request, [
      'product_service' => v::notEmpty(),
      'price' => v::notEmpty(),
      'units' => v::notEmpty(),
    ]);

    if($validation->failed()){
      $this->container->flash->addMessage('error', 'Erro ao editar produto ou serviço!');
      return $response->withRedirect($this->container->router->pathFor('productService.edit', ['id' => $productService->id_product_service]));
    }else{
      $productService->update([
        'product_service' => $request->getParam('product_service'),
        'type' => $request->getParam('item-type'),
        'price' => $request->getParam('price'),
        'units' => $request->getParam('units')
      ]);
      $this->container->flash->addMessage('success', 'Produto ou serviço atualizado com sucesso!');
      return $response->withRedirect($this->container->router->pathFor('productService.manager'));
    }
  }

  public function delete($request, $response, $params){
    $productService = ProductService::find($params['id']);
    
    if($productService){
      $productService->update([
        'is_active' => false
      ]);
      $this->container->flash->addMessage('success', 'Produto ou serviço apagado com sucesso!');
      return $response->withRedirect($this->container->router->pathFor('productService.manager'));
    } else {
      $this->container->flash->addMessage('error', 'Erro ao apagar o produto ou serviço');
      return $response->withRedirect($this->container->router->pathFor('productService.manager'));
    }
  }

  public function allProductsServices($request, $response){
    $productsServices = [
      'productsServices' => ProductService::where('is_active', true)->get()
    ];

    return $response->withJson($productsServices);
  }
}