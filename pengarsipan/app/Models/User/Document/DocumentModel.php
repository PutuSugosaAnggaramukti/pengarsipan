<?php

namespace App\Models\User\Document;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User\User;

/**
 * @property int $id_berkas
 * @property string $jenis_berkas
 * @property string $tanggal
 * @property string $tahun
 * @property string $nama_berkas
 * @property string $direktory_berkas
 * @property string $create_at
 * @property string $update_at
 * @property int $npp
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentModel whereCreateAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentModel whereDirektoryBerkas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentModel whereIdBerkas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentModel whereJenisBerkas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentModel whereNamaBerkas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentModel whereNpp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentModel whereTahun($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentModel whereTanggal($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentModel whereUpdateAt($value)
 * @mixin \Eloquent
 * @mixin IdeHelperDocumentModel
 */
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
