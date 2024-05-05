<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;
    protected $primaryKey = 'cart_id';
    public $timestamps = false;
    protected $fillable = [
        'customer_id',
        'status'
    ];
    protected $guarded = [
        'cart_id'
    ];
    public function items()
    {
        return $this->hasMany(CartItems::class, 'cart_id', 'cart_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }
}
