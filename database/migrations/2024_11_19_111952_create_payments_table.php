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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade')->nullable();
            $table->decimal('amount_paid',10,2);
            $table->unsignedBigInteger('scheme_category_id');
            $table->foreign('scheme_category_id')->references('id')->on('scheme_categorys')->onDelete('cascade')->onUpdate('cascade')->nullable();
            $table->boolean('payment_type')->default(1)->comment('1:Normal Payment, 0:Scheme Payment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
