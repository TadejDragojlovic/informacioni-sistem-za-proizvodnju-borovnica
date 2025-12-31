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
        Schema::create('skladistes', function (Blueprint $table) {
            $table->id();
            $table->string('lokacija');
            $table->integer('kapacitet');
            $table->decimal('temperatura', 5, 2);
            $table->decimal('trosak', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skladistes');
    }
};
