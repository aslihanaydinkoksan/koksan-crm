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
        Schema::create('dynamic_fields', function (Blueprint $table) {
            $table->id();
            $table->string('model_type')->index(); // Örn: App\Models\Customer
            $table->string('name'); // JSON key: 'packaging_type'
            $table->string('label'); // Arayüz etiketi: 'Ambalaj Tipi'
            $table->string('type'); // text, number, select, date, boolean
            $table->boolean('is_required')->default(false);
            $table->json('options')->nullable(); // Select ise ['kg', 'ton']
            $table->integer('order_column')->default(0);
            $table->timestamps();

            // Aynı modele aynı JSON key'in ikinci kez eklenmesini veritabanı seviyesinde yasaklıyoruz.
            $table->unique(['model_type', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dynamic_fields');
    }
};
