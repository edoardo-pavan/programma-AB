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
    Schema::create('documenti', function (Blueprint $table) {
        $table->id();
        $table->morphs('documentabile');
        $table->string('tipo', 50);
        $table->string('titolo', 150)->nullable();
        $table->string('numero', 100)->nullable();
        $table->date('data_emissione')->nullable();
        $table->date('data_scadenza')->nullable()->index();
        $table->string('file_path')->nullable();
        $table->string('file_nome')->nullable();
        $table->string('file_mime', 100)->nullable();
        $table->unsignedInteger('file_dimensione')->nullable();
        $table->text('note')->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('documenti');
}
};
