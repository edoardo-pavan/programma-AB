<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('viaggi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offerta_id')->nullable()->constrained('offerte')->nullOnDelete();
            $table->foreignId('mezzo_id')->nullable()->constrained('mezzi')->nullOnDelete();
            $table->foreignId('dipendente_id')->nullable()->constrained('dipendenti')->nullOnDelete();
            $table->foreignId('soggetto_id')->nullable()->constrained('soggetti')->nullOnDelete();
            $table->date('data_viaggio')->nullable();
            $table->string('origine')->nullable();
            $table->string('destinazione')->nullable();
            $table->integer('km_previsti')->nullable();
            $table->integer('km_effettivi')->nullable();
            $table->decimal('peso_trasportato', 10, 2)->nullable();
            $table->enum('stato', ['bozza', 'confermato', 'in_corso', 'completato', 'fatturato', 'annullato'])->default('bozza');
            $table->enum('tipo_prezzo', ['viaggio', 'tonnellata'])->nullable();
            $table->decimal('prezzo_concordato', 10, 2)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('viaggi');
    }
};
