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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();

            // İlişkisel Kolonlar
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Ziyareti Yapan Personel

            // Statik Kolonlar
            $table->dateTime('visit_date')->index();
            $table->string('reason')->default('ziyaret')->index(); // sikayet, urun_denemesi, ziyaret vb.
            $table->json('contact_persons')->nullable(); // Görüşülen kişiler (Array)
            $table->text('observations')->nullable(); // Tespitler
            $table->text('result')->nullable(); // Sonuç/Karar

            // EAV (Dinamik Alan Motoru) için JSON kolonu (Örn: Barkod No, Makine Tipi vb.)
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
        Schema::dropIfExists('visits');
    }
};
