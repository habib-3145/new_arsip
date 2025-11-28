<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('ijazahs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
        $table->string('nama', 100);
        $table->string('nisn', 20)->unique();
        $table->date('tanggal');
        $table->string('kelas', 10);
        $table->string('foto')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ijazahs');
    }
};