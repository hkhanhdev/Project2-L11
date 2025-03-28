<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;
    protected $table = 'products';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'brand_id',
        'cate_id'
    ];
    protected $guarded = ['id'];
    public function brand()
    {
        return $this->belongsTo(Brands::class);
    }
    public function cate()
    {
        return $this->belongsTo(Categories::class);
    }
    public function details()
    {
        return $this->hasMany(ProductDetails::class, 'product_parent_id');
    }
    public function cartItems()
    {
        return $this->hasMany(CartItems::class, 'product_id', 'id');
    }

    


}
