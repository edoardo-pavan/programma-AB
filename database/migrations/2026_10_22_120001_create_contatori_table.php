<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contatori', function (Blueprint $table) {
            $table->string('chiave', 50)->primary();
            $table->unsignedBigInteger('valore')->default(0);
            $table->timestamps();
        });

        DB::table('contatori')->insert([
            'chiave' => 'offerte',
            'valore' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contatori');
    }
};
