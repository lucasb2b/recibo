<?php

namespace App\Controllers;

use App\Models\Invoice;

class HomeController extends Controller
{
  public function index($request, $response)
  {
    $page = (int) ($request->getQueryParams()['page'] ?? 1);
    $limit = 5;


    $invoices = Invoice::with('customer')->paginate($limit, ['*'], 'page', $page);

    return $this->container->view->render($response, 'index.twig', [
      'invoices' => $invoices,
    ]);
  }
}
