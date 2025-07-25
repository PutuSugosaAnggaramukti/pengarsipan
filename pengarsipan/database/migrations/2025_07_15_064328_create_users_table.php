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
            Schema::create('user', function (Blueprint $table) {
        $table->unsignedBigInteger('npp')->primary();
        $table->string('username')->unique();
        $table->string('password');
        $table->string('nama_user');
        $table->string('role');
        $table->unsignedBigInteger('id_divisi')->nullable();
        $table->rememberToken();
        // Tidak pakai created_at & updated_at karena kamu bilang timestamps false
    });

        // Jika ada foreign key untuk id_divisi, bisa ditambahkan di sini
        // $table->foreign('id_divisi')->references('id')->on('divisions')->onDelete('cascade');

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
