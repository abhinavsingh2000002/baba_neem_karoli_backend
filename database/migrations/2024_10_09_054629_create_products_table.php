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
            $table->string('product_no',100)->unique();
            $table->string('product_name');
            $table->string('company_name');
            $table->string('product_image');
            $table->string('product_description');
            $table->string('product_quantity');
            $table->integer('item_per_cred');
            $table->boolean('status')->default('1')->comment('Active:1,Inactive:0');
            $table->timestamps();
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
