<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'discord_id',
        'discord_username',
        'discord_avatar',
        'discord_token',
        'discord_refresh_token',
        'registration_ip',
        'last_login_ip',
        'last_login_at',
    ];

    /**
     * Attributes that should never be mass assignable (security-sensitive).
     * These must be set explicitly via $user->attribute = value.
     */
    protected $guarded = [
        'is_admin',
        'uber_balance',
        'max_game_accounts',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'discord_token',
        'discord_refresh_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    public function gameAccounts(): HasMany
    {
        return $this->hasMany(GameAccount::class);
    }

    public function canCreateGameAccount(): bool
    {
        return $this->gameAccounts()->count() < $this->max_game_accounts;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->isAdmin();
        }

        return true;
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
