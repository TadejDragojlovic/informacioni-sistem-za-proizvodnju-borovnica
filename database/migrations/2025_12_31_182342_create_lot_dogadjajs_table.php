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
        Schema::create('lot_dogadjajs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')
                ->constrained('lots')
                ->restrictOnDelete();
            $table->foreignId('lot_raspodela_id')
                ->nullable()
                ->constrained('lot_raspodela')
                ->restrictOnDelete();
            $table->string('tip');
            $table->integer('kolicina_g')->nullable();
            $table->timestamp('vreme_dogadjaja');
            $table->foreignId('evidentirao_user_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();
            $table->string('prethodni_status')->nullable();
            $table->string('novi_status')->nullable();
            $table->foreignId('prethodna_skladisna_lokacija_id')
                ->nullable()
                ->constrained('skladisna_lokacija')
                ->restrictOnDelete();
            $table->foreignId('nova_skladisna_lokacija_id')
                ->nullable()
                ->constrained('skladisna_lokacija')
                ->restrictOnDelete();
            $table->text('razlog')->nullable();
            $table->timestamps();

            $table->index(['lot_id', 'vreme_dogadjaja']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lot_dogadjajs');
    }
};
