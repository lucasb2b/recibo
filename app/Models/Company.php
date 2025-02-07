<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

  protected $table = 'company';

  protected $primaryKey = 'id_company';

  public $timestamps = false;

  protected $fillable = [
    'company_name',
    'cnpj',
    'ie_cfdf',
    'address',
    'postal_code',
    'number',
    'district',
    'city',
    'state',
    'phone',
    'image'
  ];

  public function invoices()
  {
    return $this->hasMany(Invoice::class, 'company_id_company', 'id_company');
  }
}
