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
        Schema::create('resurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lot_id')
                ->constrained('lots')
                ->restrictOnDelete();
            $table->string('naziv');
            $table->decimal('kolicina', 10, 2)->unsigned();
            $table->string('jedinica_mere');
            $table->decimal('cena_po_jedinici', 10, 2)->unsigned();
            $table->date('datum_upotrebe');
            $table->foreignId('evidentirao_user_id')
                ->constrained('users')
                ->restrictOnDelete();
            $table->timestamps();
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement(
                'ALTER TABLE resurs ADD CONSTRAINT resurs_kolicina_positive CHECK (kolicina > 0)'
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resurs');
    }
};
