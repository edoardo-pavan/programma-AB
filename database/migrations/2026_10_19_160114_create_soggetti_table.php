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
    Schema::create('soggetti', function (Blueprint $table) {
        $table->id();
        $table->string('ragione_sociale');
        $table->string('partita_iva', 20)->nullable()->unique();
        $table->string('codice_fiscale', 20)->nullable();
        $table->string('codice_sdi', 10)->nullable();
        $table->string('pec')->nullable();
        $table->string('indirizzo')->nullable();
        $table->string('cap', 10)->nullable();
        $table->string('comune')->nullable();
        $table->string('provincia', 2)->nullable();
        $table->string('telefono')->nullable();
        $table->string('email')->nullable();
        $table->string('condizioni_pagamento')->nullable();
        $table->boolean('is_cliente')->default(false);
        $table->boolean('is_fornitore')->default(false);
        $table->string('categoria_fornitore')->nullable();
        $table->text('note')->nullable();
        $table->boolean('attivo')->default(true);
        $table->timestamps();
    });

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soggetti');
    }
};
