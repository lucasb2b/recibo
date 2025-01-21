<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemInvoice extends Model
{

  protected $table = 'item_invoice';

  protected $primaryKey = 'id_item_invoice';

  public $timestamps = false;

  protected $fillable = [
    'item_name',
    'quantity',
    'unit_price',
    'unit_type',
    'subtotal',
    'invoice_id_invoice'
  ];

  public function invoice()
  {
    return $this->belongsTo(Invoice::class, 'invoice_id_invoice', 'id_invoice');
  }
}
