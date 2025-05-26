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
            $table->bigInteger('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->bigInteger('lot_id')->references('id')->on('lots')->onDelete('cascade')->nullable();
            $table->bigInteger('serial_id')->references('id')->on('serials')->onDelete('cascade')->nullable();
            $table->bigInteger('approver_id')->references('id')->on('users')->onDelete('cascade')->nullable();
            $table->bigInteger('enduser_id')->references('id')->on('users')->onDelete('cascade')->nullable();
            $table->string('transaction_type')->nullable();
            $table->bigInteger('quantity')->nullable();
            $table->bigInteger('item_balance')->nullable();
            $table->date('transaction_date')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->string('oeno')->nullable();
            $table->string('proe')->nullable();
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
