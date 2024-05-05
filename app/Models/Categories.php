<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;
    protected $table = "categories";
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];
    public function products()
    {
        return $this->hasMany(Products::class,"cate_id","id");
    }

}
