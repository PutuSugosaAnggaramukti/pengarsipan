<?php

namespace App\Models\User\Document;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

/**
 * @property int $id_document
 * @property string $nomor
 * @property string $tanggal
 * @property string $tahun
 * @property string $nama_document
 * @property string $direktory_document
 * @property int $npp
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 */
class DocumentModel extends Model
{
    use SoftDeletes;

    protected $table = 'documents';
    protected $primaryKey = 'id_document';
    public $timestamps = true;

    protected $fillable = [
        'nomor',
        'tanggal',
        'tahun',
        'nama_document',
        'direktory_document',
        'npp'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Relasi ke tabel users berdasarkan NPP
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'npp', 'npp');
    }
}
