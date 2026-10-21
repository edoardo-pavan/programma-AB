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
    Schema::create('mezzi', function (Blueprint $table) {
        $table->id();
        $table->string('tipo');
        $table->string('sottotipo')->nullable();
        $table->string('targa', 15)->unique();
        $table->string('marca')->nullable();
        $table->string('modello')->nullable();
        $table->unsignedSmallInteger('anno')->nullable();
        $table->string('telaio', 30)->nullable();
        $table->decimal('portata_t', 6, 2)->nullable();
        $table->boolean('adr')->default(false);
        $table->text('note')->nullable();
        $table->boolean('attivo')->default(true);
        $table->timestamps();
    });

    public function down(): void
    {
        Schema::dropIfExists('mezzi');
    }
};
