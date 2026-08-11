<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->string('oznaka')->unique();
            $table->foreignId('sorta_id')
                ->constrained('sortas')
                ->restrictOnDelete();
            $table->foreignId('parcela_id')
                ->constrained('parcelas')
                ->restrictOnDelete();
            $table->foreignId('trenutna_skladisna_lokacija_id')
                ->nullable()
                ->constrained('skladisna_lokacija')
                ->restrictOnDelete();
            $table->date('datum_berbe');
            $table->unsignedBigInteger('pocetna_kolicina_g');
            $table->unsignedBigInteger('raspoloziva_kolicina_g');
            $table->string('status')->default('kreiran');
            $table->string('klasa_kvaliteta')->nullable();
            $table->string('broj_dokumenta_kvaliteta')->nullable();
            $table->text('napomena')->nullable();
            $table->timestamps();

            $table->index(['sorta_id', 'status', 'datum_berbe']);
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement(
                'ALTER TABLE lots ADD CONSTRAINT lots_pocetna_kolicina_g_positive '
                .'CHECK (pocetna_kolicina_g > 0)'
            );
            DB::statement(
                'ALTER TABLE lots ADD CONSTRAINT lots_raspoloziva_kolicina_g_valid '
                .'CHECK (raspoloziva_kolicina_g <= pocetna_kolicina_g)'
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};
