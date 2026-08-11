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
        Schema::create('lot_raspodela', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')
                ->constrained('lots')
                ->restrictOnDelete();
            $table->foreignId('narudzbina_stavka_id')
                ->constrained('narudzbina_stavkas')
                ->restrictOnDelete();
            $table->unsignedInteger('broj_pakovanja');
            $table->string('status')->default('rezervisano');
            $table->timestamps();

            $table->unique(['lot_id', 'narudzbina_stavka_id']);
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement(
                'ALTER TABLE lot_raspodela ADD CONSTRAINT lot_raspodela_broj_pakovanja_positive '
                .'CHECK (broj_pakovanja > 0)'
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lot_raspodela');
    }
};
