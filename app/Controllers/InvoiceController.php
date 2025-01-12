<?php

namespace App\Controllers;

use App\Models\Invoice;
use Respect\Validation\Validator as v;

class InvoiceController extends Controller {

  public function invoice($request, $response){
    if($request->isGet())
      return $this->container->view->render($response, 'invoice.twig');
  }

}