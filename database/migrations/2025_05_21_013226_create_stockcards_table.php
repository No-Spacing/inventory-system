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
            $table->bigInteger('lot_id')->references('id')->on('lots')->onDelete('cascade');
            $table->bigInteger('serial_id')->references('id')->on('serials')->onDelete('cascade');
            $table->bigInteger('approver_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('enduser_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('stocktype');
            $table->bigInteger('quantity');
            $table->bigInteger('balance');
            $table->date('transaction_date');
            $table->date('purchase_date');
            $table->date('expire_date');
            $table->string('oeno');
            $table->string('proe');
            $table->longText('warehouse');
            $table->string('reference');
            $table->longText('remarks');
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
