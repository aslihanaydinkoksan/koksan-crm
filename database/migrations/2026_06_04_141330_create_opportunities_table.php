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
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            
            // Fırsatın bağlı olduğu müşteri (Foreign Key)
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            
            $table->string('title');
            $table->decimal('amount', 15, 2)->default(0); // Para birimi işlemleri için Decimal şarttır
            $table->string('currency', 3)->default('TRY'); // TRY, USD, EUR vb.
            $table->date('expected_decision_date')->nullable();
            
            // Satış/Duyum Aşaması
            $table->string('stage')->default('duyum')->index(); 
            
            $table->string('competitor')->nullable();
            $table->text('details')->nullable();
            
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
        Schema::dropIfExists('opportunities');
    }
};
