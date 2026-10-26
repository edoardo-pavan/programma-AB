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
        Schema::create('offerte', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('numero')->nullable()->unique();
            $table->date('data_offerta');
            $table->string('stato', 20)->default('bozza')->index();
            $table->foreignId('cliente_id')->constrained('soggetti')->restrictOnDelete();
            $table->foreignId('partenza_luogo_id')->constrained('luoghi')->restrictOnDelete();
            $table->foreignId('destinazione_luogo_id')->constrained('luoghi')->restrictOnDelete();
            $table->string('tipo_mezzo', 50);
            $table->string('tipo_prezzo', 20)->default('a_tonnellata');
            $table->decimal('prezzo', 10, 2);
            $table->decimal('prezzo_padroncini', 10, 2)->nullable();
            $table->decimal('minimo_fatturabile', 8, 2)->nullable();
            $table->decimal('fermo_orario', 10, 2)->default(75);
            $table->decimal('fermo_franchigia_ore', 4, 2)->default(1);
            $table->boolean('iva_esclusa')->default(true);
            $table->string('pagamento', 200)->default('RB 60+10 gg. d.f.f.m.');
            $table->string('codici_cer', 500)->nullable();
            $table->string('descrizione_materiale', 500)->nullable();
            $table->string('tonnellaggio_stimato', 100)->nullable();
            $table->text('note')->nullable();
            $table->foreignId('offerta_origine_id')->nullable()->constrained('offerte')->nullOnDelete();
            $table->text('note_copia')->nullable();
            $table->foreignId('creata_da')->constrained('users')->restrictOnDelete();
            $table->dateTime('pubblicata_il')->nullable();
            $table->timestamps();

            $table->index(
                ['cliente_id', 'partenza_luogo_id', 'destinazione_luogo_id', 'stato'],
                'offerte_ricerca_tratta'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offerte');
    }
};
