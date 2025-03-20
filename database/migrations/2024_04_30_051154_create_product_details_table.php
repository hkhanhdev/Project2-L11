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
        Schema::create('product_details', function (Blueprint $table) {
            $table->id();
            $table->string("name",100);
            $table->integer("product_parent_id");
            $table->string("size",50);
            $table->string("flavor",50);
            $table->integer("servings")->nullable();
            $table->decimal("price",5);
            $table->integer("quantity")->default(20);
            $table->string("status",20)->default("Available");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_details');
    }
};
