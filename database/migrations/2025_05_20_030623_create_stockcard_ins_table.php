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
        Schema::create('stockcard_ins', function (Blueprint $table) {
            $table->id();
            // $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            // $table->foreign('lot_id')->references('id')->on('lot')->onDelete('cascade');
            // $table->foreign('serial_id')->references('id')->on('serial')->onDelete('cascade');
            // $table->foreign('approver_id')->references('id')->on('user')->onDelete('cascade');
            // $table->foreign('enduser_id')->references('id')->on('user')->onDelete('cascade');
            // $table->string('stocktype');
            // $table->bigInteger('quantity');
            // $table->bigInteger('balance');
            // $table->date('transaction_date');
            // $table->date('purchase_date');
            // $table->date('expire_date');
            // $table->string('oeno');
            // $table->string('proe');
            // $table->longText('warehouse');
            // $table->string('reference');
            // $table->longText('remarks');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stockcard_ins');
    }
};
