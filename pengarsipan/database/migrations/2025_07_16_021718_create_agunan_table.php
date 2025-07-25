<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agunan', function (Blueprint $table) {
            $table->id('id_agunan');
            $table->integer('nomor');
            $table->date('tanggal');
            $table->string('tahun', 4);
            $table->string('nama_agunan', 255);
            $table->string('direktori_agunan', 255);
            $table->unsignedBigInteger('npp');
            $table->timestamps();
            $table->softDeletes(); // supaya support recycle bin
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agunan');
    }
};
