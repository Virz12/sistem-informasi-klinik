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
        Schema::create('medicine_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('medical_record_id');
            $table->unsignedBigInteger('medicine_id');
            $table->integer('quantity');
            $table->bigInteger('total_price');
            $table->timestamps();

            $table->foreign('patient_id')->references('id')->on('patients');
            $table->foreign('medical_record_id')->references('id')->on('medical_records');
            $table->foreign('medicine_id')->references('id')->on('medicines');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicine_records');
    }
};
