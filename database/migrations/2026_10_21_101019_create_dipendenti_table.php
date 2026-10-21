<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('dipendenti', function (Blueprint $table) {
        $table->id();
        $table->string('nome');
        $table->string('cognome');
        $table->string('codice_fiscale', 16)->nullable()->unique();
        $table->date('data_nascita')->nullable();
        $table->string('luogo_nascita')->nullable();
        $table->string('indirizzo')->nullable();
        $table->string('cap', 10)->nullable();
        $table->string('comune')->nullable();
        $table->string('provincia', 2)->nullable();
        $table->string('telefono')->nullable();
        $table->string('email')->nullable();
        $table->string('mansione')->default('autista');
        $table->date('data_assunzione')->nullable();
        $table->date('data_cessazione')->nullable();
        $table->foreignId('trattore_id')->nullable()->constrained('mezzi')->nullOnDelete();
        $table->foreignId('rimorchio_id')->nullable()->constrained('mezzi')->nullOnDelete();
        $table->text('note')->nullable();
        $table->boolean('attivo')->default(true);
        $table->timestamps();
    });

    public function down(): void
    {
        Schema::dropIfExists('dipendenti');
    }
};
