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
        Schema::create('proizvods', function (Blueprint $table) {
            $table->id();
            $table->string('naziv', 100);
            $table->text('opis');
            $table->foreignId('sorta_id')
                ->constrained('sortas')
                ->restrictOnDelete();
            $table->unsignedInteger('neto_kolicina_g');
            $table->decimal('cena', 10, 2)->unsigned();
            $table->boolean('aktivan')->default(true);
            $table->timestamps();
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement(
                'ALTER TABLE proizvods ADD CONSTRAINT proizvods_neto_kolicina_g_positive '
                .'CHECK (neto_kolicina_g > 0)'
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proizvods');
    }
};
