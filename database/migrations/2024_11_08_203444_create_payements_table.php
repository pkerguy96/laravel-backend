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
        Schema::create('payements', function (Blueprint $table) {
           $table->id();
            $table->unsignedBigInteger('operation_id');
            $table->decimal('total_cost', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->foreign('operation_id')->references('id')->on('operations')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payements');
    }
};
