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
            $table->foreignId('product_id')->nullable()->references('id')->on('products')->onUpdate('cascade');
            $table->foreignId('lot_id')->nullable()->references('id')->on('lots')->onUpdate('cascade');
            $table->foreignId('serial_id')->nullable()->references('id')->on('serials')->onUpdate('cascade');
            $table->foreignId('approver_id')->nullable()->references('id')->on('users')->onUpdate('cascade');
            $table->foreignId('enduser_id')->nullable()->references('id')->on('users')->onUpdate('cascade');
            $table->string('transaction_type')->nullable();
            $table->bigInteger('quantity')->nullable();
            $table->date('transaction_date')->nullable();
            $table->longText('document_no')->nullable();
            $table->longText('warehouse_id')->nullable();
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
