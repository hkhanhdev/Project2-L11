<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Orders extends Model
{
    use HasFactory;
    protected $primaryKey = 'cart_id';
    protected $fillable = [
        'customer_id',
        'status',
        'seller_id'
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
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id', 'id');
    }
    static function getTotalRevenue()
    {
        $totalRevenue = Orders::where("status", 'delivered')->with('items')->get()->sum(function ($order) {
            return $order->items->sum('subtotal');
        });
        return $totalRevenue;
    }

    static function getTotalRevenueByMY()
    {
        $revenueByMonth = Orders::where('status', 'delivered')
            ->with('items')
            ->select(
                DB::raw('YEAR(orders.created_at) AS year'),
                DB::raw('MONTH(orders.created_at) AS month'),
                DB::raw('SUM(cart_items.subtotal) AS revenue')
            )
            ->join('cart_items', 'orders.cart_id', '=', 'cart_items.cart_id')
            ->groupBy(DB::raw('YEAR(orders.created_at)'), DB::raw('MONTH(orders.created_at)'))
            ->get();
        return $revenueByMonth;
    }

}
