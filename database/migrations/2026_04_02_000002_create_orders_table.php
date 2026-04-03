<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('table_number');
            $table->integer('total_amount');
            $table->enum('payment_method', ['cash', 'card', 'qr']);
            $table->enum('cash_payment_mode', ['pre', 'post'])->nullable(); // 선불/후불 기록
            $table->enum('status', ['pending', 'approved', 'cooking', 'served', 'paid', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};