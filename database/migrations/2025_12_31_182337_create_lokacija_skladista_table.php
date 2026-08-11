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
        Schema::create('skladisna_lokacija', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skladiste_id')
                ->constrained('skladistes')
                ->restrictOnDelete();
            $table->string('naziv');
            $table->text('opis');
            $table->boolean('aktivna')->default(true);
            $table->timestamps();

            $table->unique(['skladiste_id', 'naziv']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skladisna_lokacija');
    }
};
