<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetails extends Model
{
    use HasFactory;
    protected $table = 'product_details';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'product_parent_id',
        'size',
        'flavor',
        'servings',
        'price',
        'status',
        'quantity'
    ];
    protected $guarded = ['id'];
    public function product()
    {
        return $this->belongsTo(Products::class,'product_parent_id','id');
    }

    public function cartItems()
    {
        return $this->hasMany(CartItems::class, 'product_details_id', 'id');
    }
}
