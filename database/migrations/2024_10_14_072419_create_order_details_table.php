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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade')->onUpdate('cascade')->nullable();
            $table->string('order_no',100)->unique();
            $table->string('product_no',100)->unique();
            $table->string('product_name');
            $table->string('company_name');
            $table->string('product_image');
            $table->string('product_description');
            $table->string('product_weight');
            $table->string('product_quantity');
            $table->integer('item_per_cred');
            $table->integer('amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
