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
        Schema::create('narudzbina_stavkas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('narudzbina_id')
                ->constrained('narudzbinas')
                ->restrictOnDelete();

            $table->foreignId('proizvod_id')
                ->constrained('proizvods')
                ->restrictOnDelete();

            $table->unsignedInteger('kolicina');
            $table->unsignedInteger('neto_kolicina_g');
            $table->decimal('cena_po_jedinici', 10, 2)->unsigned();
            $table->timestamps();
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement(
                'ALTER TABLE narudzbina_stavkas ADD CONSTRAINT narudzbina_stavkas_kolicina_positive CHECK (kolicina > 0)'
            );
            DB::statement(
                'ALTER TABLE narudzbina_stavkas ADD CONSTRAINT narudzbina_stavkas_neto_kolicina_g_positive CHECK (neto_kolicina_g > 0)'
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('narudzbina_stavkas');
    }
};
