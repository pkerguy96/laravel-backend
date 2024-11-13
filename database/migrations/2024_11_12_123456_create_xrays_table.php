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
        Schema::create('xrays', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patient_id');
            $table->unsignedBigInteger('operation_id')->nullable();
            $table->unsignedBigInteger('xray_preference_id')->nullable();
            $table->string('xray_type')->nullable();
            $table->string('view_type')->nullable();
            $table->string('body_side')->nullable();
            $table->string('type')->default('xray');
            $table->text('note')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->timestamps();
            $table->foreign('patient_id')->references('id')->on('patients')->onDelete('cascade');
            $table->foreign('operation_id')->references('id')->on('operations')->onDelete('cascade');
            $table->foreign('xray_preference_id')->references('id')->on('xraypreferences')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xrays');
    }
};
