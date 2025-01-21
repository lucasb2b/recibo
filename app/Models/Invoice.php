<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{

  protected $table = 'invoice';

  protected $primaryKey = 'id_invoice';

  public $timestamps = false;

  protected $fillable = [
    'company_id_company',
    'customer_id_client',
    'total',
    'discount',
    'payment_type',
    'observation',
    'hash',
    'qr_code_hash',
    'datetime'
  ];

  public function company()
  {
    return $this->belongsTo(Company::class, 'company_id_company', 'id_company');
  }

  public function customer()
  {
    return $this->belongsTo(Customer::class, 'customer_id_client', 'id_client');
  }

  public function items()
  {
    return $this->hasMany(ItemInvoice::class, 'invoice_id_invoice', 'id_invoice');
  }
}
