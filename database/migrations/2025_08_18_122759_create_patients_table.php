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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id')->unique();
            $table->string('name');
            $table->date('birth_date');
            $table->enum('gender', ['pria', 'wanita']);
            $table->string('phone');
            $table->unsignedBigInteger('district_id');
            $table->text('address');
            $table->text('allergies')->nullable();
            $table->timestamps();

            $table->foreign('district_id')->references('id')->on('districts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
