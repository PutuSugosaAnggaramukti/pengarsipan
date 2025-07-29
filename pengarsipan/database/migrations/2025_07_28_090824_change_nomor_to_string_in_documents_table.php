<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('nomor', 50)->change(); // ubah tipe kolom
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->integer('nomor')->change(); // rollback ke integer
        });
    }
};


