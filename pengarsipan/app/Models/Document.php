<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Document
 *
 * @property int $id
 * @property string $nama_berkas
 * @property string $path
 * @property string $tahun
 * @property int|null $npp
 * @property-read \App\Models\User|null $user
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */


class Document extends Model
{
    protected $table = 'documents';
    protected $fillable = ['nama_berkas', 'path', 'tahun', 'file_size', 'npp'];

    public function user()
    {
        return $this->belongsTo(User::class, 'npp', 'npp');
    }
}
