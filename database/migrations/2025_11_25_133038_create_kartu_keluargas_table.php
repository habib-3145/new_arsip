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
        Schema::create('kartu_keluargas', function (Blueprint $table) {
            $table->id();
            $table->string('no_kk', 16)->unique();
            $table->text('alamat');
            $table->string('rt', 3);
            $table->string('rw', 3);
            $table->string('kode_pos', 5)->nullable();
            $table->string('desa_kelurahan');
            $table->string('kecamatan');
            $table->string('kabupaten_kota');
            $table->string('provinsi');
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kartu_keluargas');
    }
};
