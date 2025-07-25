<?php

namespace App\Models\User\Document;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User\User;

class DocumentModel extends Model
{
    use SoftDeletes;

    protected $table = 'documents';
    protected $primaryKey = 'id_document';

    protected $fillable = [
        'nomor',
        'tanggal',
        'tahun',
        'nama_document',
        'file',
        'direktory_document',
        'npp'
    ];

    /**
     * Relasi ke User
     * documents.npp => users.npp
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'npp', 'npp');
    }
}
