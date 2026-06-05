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
        Schema::create('lab_tests', function (Blueprint $table) {
            $table->id();

            // İlişkisel Kolonlar
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('machine_id')->nullable()->constrained('machines')->nullOnDelete();
            $table->foreignId('sample_id')->nullable()->constrained('samples')->nullOnDelete();

            // Statik Kolonlar
            $table->string('test_type')->index(); // Basınç Testi, IV Analizi vb.
            $table->string('status')->default('bekliyor')->index(); // bekliyor, test_ediliyor, onaylandi, red
            $table->dateTime('test_date');
            $table->text('result_summary')->nullable();

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
        Schema::dropIfExists('lab_tests');
    }
};
