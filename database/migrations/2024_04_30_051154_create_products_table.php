<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string("name",100);
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger("cate_id");
            $table->string("size",50);
            $table->string("flavor",50);
            $table->integer("servings")->nullable();
            $table->decimal("price",5);
            $table->integer("quantity")->default(20);
            $table->string("status",20)->default("Available");

            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreign('cate_id')->references('id')->on('categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
