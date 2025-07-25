<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
/**
 * @property int $npp
 * @property string $nama_user
 * @property string $username
 * @property string $password
 * @property string $role
 * @property int $id_divisi
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $documents
 * @property-read int|null $documents_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIdDivisi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNamaUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNpp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @mixin \Eloquent
 * @mixin IdeHelperUser
 */
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
