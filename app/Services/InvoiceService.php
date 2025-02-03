<?php

namespace App\Services;

use App\Models\Invoice;
use Illuminate\Pagination\Paginator;

class InvoiceService {
  public function listInvoices($page, $limit){
    Paginator::useBootstrapThree();
    return Invoice::with('customer')->paginate($limit, ['*'], 'page', $page);
  }
}
