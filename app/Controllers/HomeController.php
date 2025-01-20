<?php

namespace App\Controllers;
use App\Models\Invoice;

class HomeController extends Controller {
  public function index($request, $response) {

    $invoices = [
      'invoices' => Invoice::with('customer')->get()
    ];
    
    return $this->container->view->render($response, 'index.twig', $invoices);
  }
}