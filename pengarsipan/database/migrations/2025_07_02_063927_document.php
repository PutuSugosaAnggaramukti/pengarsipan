<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
            Schema::create('documents', function (Blueprint $table) {
            $table->id('id_document');
            $table->integer('nomor');
            $table->date('tanggal');
            $table->string('tahun', 4);
            $table->string('nama_document', 255);
            $table->string('direktory_document', 255);
            $table->unsignedBigInteger('npp');
            $table->timestamps(); // otomatis created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document');
    }
};
