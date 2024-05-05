<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brands extends Model
{
    use HasFactory;
    protected $table = "brands";
    public $timestamps = false;
    protected $fillable = ['name'];
    protected $guarded = ['id'];
    public function products()
    {
        return $this->hasMany(Products::class,"brand_id","id");
    }
}
