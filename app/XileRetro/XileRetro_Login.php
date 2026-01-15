<?php

namespace App\XileRetro;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property int $account_id
 * @property string $userid
 * @property string $user_pass
 * @property string $sex
 * @property string $email
 * @property int $group_id
 * @property int $state
 * @property int $unban_time
 * @property int $expiration_time
 * @property int $logincount
 * @property string|null $lastlogin
 * @property string $last_ip
 * @property string|null $birthdate
 * @property int $character_slots
 * @property string $pincode
 * @property int $pincode_change
 * @property int $vip_time
 * @property int $old_group
 * @property string|null $web_auth_token
 * @property int $web_auth_token_enabled
 * @property int $last_unique_id
 * @property int $blocked_unique_id
 * @property-read string $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, XileRetro_Char> $chars
 *
 * @method static create(array $array)
 */
class XileRetro_Login extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    protected static function newFactory(): \Database\Factories\XileRetro\XileRetro_LoginFactory
    {
        return \Database\Factories\XileRetro\XileRetro_LoginFactory::new();
    }

    protected $connection = 'xileretro_main';

    protected $table = 'login';

    protected $primaryKey = 'account_id';

    public $timestamps = false;

    protected $fillable = [
        'userid',
        'user_pass',
        'sex',
        'email',
        'group_id',
        'state',
        'unban_time',
        'expiration_time',
        'logincount',
        'lastlogin',
        'last_ip',
        'birthdate',
        'character_slots',
        'pincode',
        'pincode_change',
        'vip_time',
        'old_group',
        'web_auth_token',
        'web_auth_token_enabled',
        'last_unique_id',
        'blocked_unique_id',
    ];

    protected $hidden = [
        'user_pass',
        'remember_token',
        'pincode',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Get the connection name based on environment (supports testing with SQLite).
     */
    public function getConnectionName(): ?string
    {
        if (app()->runningUnitTests()) {
            return config('database.default');
        }

        return $this->connection;
    }

    /**
     * Get the name for display (maps to userid).
     */
    public function getNameAttribute(): string
    {
        return $this->userid;
    }

    /**
     * Get the password for authentication.
     */
    public function getAuthPassword(): string
    {
        return $this->user_pass;
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->group_id === 99;
    }

    /**
     * Determine if the user can access the given Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->isAdmin();
        }

        return true;
    }

    public function chars(): HasMany
    {
        return $this->hasMany(XileRetro_Char::class, 'account_id', 'account_id');
    }
}
