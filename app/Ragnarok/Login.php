<?php

namespace App\Ragnarok;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * @property int $account_id
 * @property string $userid
 * @property string $user_pass
 * @property string $email
 * @property bool $group_id
 * @property int $state
 * @property int $unban_time
 * @property int $expiration_time
 * @property int $logincount
 * @property string $lastlogin
 * @property string $last_ip
 * @property string $birthdate
 * @property bool $character_slots
 * @property string $pincode
 * @property int $pincode_change
 * @property int $vip_time
 * @property bool $old_group
 * @property string $web_auth_token
 * @property bool $web_auth_token_enabled
 * @property int $last_unique_id
 * @property int $blocked_unique_id
 *
 * @property int $total_online
 */
class Login extends Authenticatable
{
    use Notifiable;

    /**
     * The connection name for the model.
     *
     * @var string|null
     */
    protected $connection = 'main';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'login';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'account_id';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['user_pass'];

    /**
     * The attributes that should be fillable using forms.
     *
     * @var array
     */
    protected $fillable = ['userid', 'email', 'user_pass'];

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->user_pass;
    }
}
