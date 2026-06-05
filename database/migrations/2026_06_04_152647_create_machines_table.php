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
        Schema::create('machines', function (Blueprint $table) {
            $table->id();

            // İlişkisel Kolon
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();

            // Statik Kolonlar
            $table->string('ownership')->index(); // Örn: 'Müşteri Makinesi', 'KÖKSAN Makinesi'
            $table->string('brand')->index(); // Örn: Husky, Netstal
            $table->string('model_name');
            $table->string('serial_number')->nullable()->unique();
            $table->date('installed_at')->nullable();

            // EAV (Dinamik Alan Motoru) için JSON kolonu
            $table->json('custom_data')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machines');
    }
};
