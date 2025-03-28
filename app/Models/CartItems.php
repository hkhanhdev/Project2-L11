<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItems extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'order_id',
        'product_id',
        'product_details_id',
        'cart_quantity',
        'subtotal'
    ];
    protected $guarded = [
      'item_id'
    ];
    protected $primaryKey = 'item_id';
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id', 'order_id');
    }

    public function product_details() {
        return $this->belongsTo(ProductDetails::class, 'product_details_id', 'id');
    }
}
