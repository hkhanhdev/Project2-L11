<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItems extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'cart_id',
        'product_id',
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
        return $this->belongsTo(Orders::class, 'cart_id', 'cart_id');
    }
}
