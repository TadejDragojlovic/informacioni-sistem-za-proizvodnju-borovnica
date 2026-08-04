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
        Schema::create('narudzbina_stavkas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('narudzbina_id')
                ->constrained('narudzbinas')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('proizvod_id')
                ->constrained('proizvods')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->integer('kolicina');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('narudzbina_stavkas');
    }
};
