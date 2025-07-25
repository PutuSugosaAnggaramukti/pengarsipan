<?php

namespace App\Models\Agunan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agunan extends Model
{
    use SoftDeletes;

    protected $table = 'agunan';
    protected $primaryKey = 'id_agunan';

    protected $fillable = [
        'nomor', 'tanggal', 'tahun', 'nama_agunan', 'direktori_agunan', 'npp'
    ];

    protected $casts = [
        'nomor' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'npp', 'npp');
    }
}
