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
      'productsServices' => ProductService::all()
    ];

    return $productsServices;
  }

  public function managerProductService($request, $response){
    if($request->isGet())
      $productsServices = $this->getAllProductsServices();
      return $this->container->view->render($response, 'manager_product_service.twig', $productsServices);
  }
}