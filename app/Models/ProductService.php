<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductService extends Model {

  protected $table = 'product_service';

  protected $primaryKey = 'id_product_service';

  public $timestamps = false;

  protected $fillable = [
    'product_service',
    'type',
    'price',
    'units'
  ];
}