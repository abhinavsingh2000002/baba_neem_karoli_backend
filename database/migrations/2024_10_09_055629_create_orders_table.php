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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no',100)->unique();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade')->nullable();
            $table->unsignedBigInteger('total_amount');
            $table->integer('order_status')->default('1')->comment('failed:0,pending:1,confirmed:2,delivered:3');
            $table->date('order_date');
            $table->time('order_time');
            $table->date('order_failed_date')->nullable();
            $table->time('order_failed_time')->nullable();
            $table->date('order_confirm_date')->nullable();
            $table->time('order_confirm_time')->nullable();
            $table->date('order_deliverd_date')->nullable();
            $table->time('order_deliverd_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
