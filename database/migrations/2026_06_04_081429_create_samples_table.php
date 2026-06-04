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
        Schema::create('samples', function (Blueprint $table) {
            $table->id();
            $table->string('subject'); // Numune gönderim konusu
            $table->string('tracking_number')->nullable(); // Kargo takip no
            $table->date('sent_at')->nullable(); // Gönderim tarihi
            $table->string('status')->default('hazirlaniyor')->index(); // hazirlaniyor, kargoda, teslim_edildi
            
            // Polimorfik İlişki Kolonları (receivable_type ve receivable_id oluşturur)
            // Örn: type: 'App\Models\Customer', id: 5
            $table->morphs('receivable');
            
            // Dinamik numune detayları (kargo firması, miktar, birim vb.) için JSON
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
        Schema::dropIfExists('samples');
    }
};
