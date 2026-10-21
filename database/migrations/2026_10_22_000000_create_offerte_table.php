<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offerte', function (Blueprint $table) {
            $table->id();
            $table->foreignId('soggetto_id')->constrained('soggetti');
            $table->string('origine');
            $table->string('destinazione');
            $table->string('merce')->nullable();
            $table->string('tipo_mezzo')->nullable();
            $table->decimal('peso_min', 10, 2)->nullable();
            $table->decimal('peso_max', 10, 2)->nullable();
            $table->decimal('peso_minimo_fatturabile', 10, 2)->default(0);
            $table->decimal('prezzo_base', 10, 2);
            $table->enum('tipo_prezzo', ['viaggio', 'tonnellata']);
            $table->decimal('fermo_macchina_ore', 10, 2)->default(0);
            $table->decimal('prezzo_fermo_macchina_ora', 10, 2)->default(0);
            $table->decimal('supplementi', 10, 2)->default(0);
            $table->decimal('totale', 10, 2)->default(0);
            $table->date('data_validita');
            $table->enum('stato', ['bozza', 'inviata', 'accettata', 'rifiutata', 'scaduta'])->default('bozza');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offerte');
    }
};
