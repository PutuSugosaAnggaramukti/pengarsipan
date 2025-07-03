<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property \App\Models\User $user
 * @property int $id
 * @property string $nama_berkas
 * @property string $path
 * @property string $tahun
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $npp
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereNamaBerkas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereNpp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereTahun($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperDocument {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $nama_berkas
 * @property string $tahun
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $path
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereNamaBerkas($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereTahun($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperFile {}
}

namespace App\Models{
/**
 * 
 *
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
 */
	#[\AllowDynamicProperties]
	class IdeHelperUser {}
}

namespace App\Models\User\Document{
/**
 * 
 *
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
 */
	#[\AllowDynamicProperties]
	class IdeHelperDocumentModel {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Users newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Users newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Users query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Users whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Users whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Users whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Users whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Users whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Users wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Users whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Users whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	#[\AllowDynamicProperties]
	class IdeHelperUsers {}
}

