<?php

namespace App\Models;

use App\XileRetro\XileRetro_AccRegStr;
use App\XileRetro\XileRetro_Char;
use App\XileRetro\XileRetro_Login;
use App\XileRO\XileRO_AccRegStr;
use App\XileRO\XileRO_Char;
use App\XileRO\XileRO_Login;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'server',
        'ragnarok_account_id',
        'userid',
        'user_pass',
        'email',
        'sex',
        'group_id',
        'state',
        'uber_balance',
        'has_security_code',
    ];

    protected $hidden = [
        'user_pass',
    ];

    protected function casts(): array
    {
        return [
            'has_security_code' => 'boolean',
        ];
    }

    public const SERVER_XILERO = 'xilero';

    public const SERVER_XILERETRO = 'xileretro';

    public const SERVERS = [
        self::SERVER_XILERO => 'XileRO (MidRate)',
        self::SERVER_XILERETRO => 'XileRetro (HighRate)',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function serverName(): string
    {
        return self::SERVERS[$this->server] ?? $this->server;
    }

    /**
     * Get the corresponding Login record from the game database.
     */
    public function ragnarokLogin(): XileRO_Login|XileRetro_Login|null
    {
        if (! $this->ragnarok_account_id) {
            return null;
        }

        $loginClass = $this->server === self::SERVER_XILERETRO ? XileRetro_Login::class : XileRO_Login::class;

        return $loginClass::find($this->ragnarok_account_id);
    }

    /**
     * Get the Char model class based on server.
     */
    protected function getCharClass(): string
    {
        return $this->server === self::SERVER_XILERETRO ? XileRetro_Char::class : XileRO_Char::class;
    }

    /**
     * Get characters via the ragnarok_account_id (queries game database)
     */
    public function chars(): HasMany
    {
        $charClass = $this->getCharClass();

        return $this->hasMany($charClass, 'account_id', 'ragnarok_account_id');
    }

    /**
     * Get synced characters from local database
     */
    public function syncedCharacters(): HasMany
    {
        return $this->hasMany(SyncedCharacter::class);
    }

    /**
     * Get the AccRegStr model class based on server.
     */
    protected function getAccRegStrClass(): string
    {
        return $this->server === self::SERVER_XILERETRO ? XileRetro_AccRegStr::class : XileRO_AccRegStr::class;
    }

    /**
     * Check if any characters on this account are currently online.
     */
    public function hasOnlineCharacters(): bool
    {
        if (! $this->ragnarok_account_id) {
            return false;
        }

        return $this->chars()->where('online', '>', 0)->exists();
    }

    /**
     * Check if this account has a security code set.
     */
    public function hasSecurityCode(): bool
    {
        if (! $this->ragnarok_account_id) {
            return false;
        }

        $accRegStrClass = $this->getAccRegStrClass();

        return $accRegStrClass::hasSecurityCode($this->ragnarok_account_id);
    }

    /**
     * Reset the security code for this account.
     */
    public function resetSecurityCode(): bool
    {
        if (! $this->ragnarok_account_id) {
            return false;
        }

        $accRegStrClass = $this->getAccRegStrClass();

        return $accRegStrClass::deleteSecurityCode($this->ragnarok_account_id) > 0;
    }
}
