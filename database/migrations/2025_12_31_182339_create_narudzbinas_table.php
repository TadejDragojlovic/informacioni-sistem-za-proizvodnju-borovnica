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
        Schema::create('narudzbinas', function (Blueprint $table) {
            $table->id();
            $table->date('datum_narudzbine');
            $table->enum('status', ["kreirana","potvrdjena","u_obradi","otpremljena","isporucena","otkazana","vracena"]);
            $table->foreignId('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('narudzbinas');
    }
};
