<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model {

  protected $table = 'company';

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
}