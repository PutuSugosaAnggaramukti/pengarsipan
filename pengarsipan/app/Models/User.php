<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = "user"; // sesuai tabel

    public $timestamps = false;

    protected $primaryKey = 'npp';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'username',
        'password',
        'nama_user',
        'role',
        'id_divisi',
        'npp',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [];
    }

    /**
     * Override supaya Auth pakai kolom 'username'
     */
    public function getAuthIdentifierName()
    {
        return 'username';
    }
}