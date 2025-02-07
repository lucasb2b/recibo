<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

  protected $table = 'customer';

  protected $primaryKey = 'id_client';

  public $timestamps = false;

  protected $fillable = [
    'customer_name',
    'cpf_cnpj',
    'telephone',
    'is_active'
  ];

  public function invoices()
  {
    return $this->hasMany(Invoice::class, 'customer_id_client', 'id_client');
  }
}
