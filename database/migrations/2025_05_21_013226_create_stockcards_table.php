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
        Schema::create('stockcards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('cascade')->nullable();
            $table->foreignId('lot_id')->references('id')->on('lots')->onUpdate('cascade')->onDelete('cascade')->nullable();
            $table->longText('serial_no')->nullable();
            $table->foreignId('approver_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade')->nullable();
            $table->foreignId('enduser_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade')->nullable();
            $table->string('transaction_type')->nullable();
            $table->bigInteger('quantity')->nullable();
            $table->bigInteger('item_balance')->nullable();
            $table->date('transaction_date')->nullable();
            $table->date('expiration_date')->nullable();
            $table->longText('warehouse')->nullable();
            $table->string('reference')->nullable();
            $table->longText('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stockcards');
    }
};
