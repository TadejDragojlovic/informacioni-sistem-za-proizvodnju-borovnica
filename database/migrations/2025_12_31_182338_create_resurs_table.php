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
        Schema::create('resurs', function (Blueprint $table) {
            $table->id();
            $table->string('naziv');
            $table->decimal('kolicina', 10, 2);
            $table->decimal('trosak', 12, 2);
            $table->foreignId('proizvod_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resurs');
    }
};
