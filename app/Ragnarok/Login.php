<?php

namespace App\Ragnarok;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $account_id
 * @property string $userid
 * @property string $user_pass
 * @property string $email
 * @property boolean $group_id
 * @property int $state
 * @property int $unban_time
 * @property int $expiration_time
 * @property int $logincount
 * @property string $lastlogin
 * @property string $last_ip
 * @property string $birthdate
 * @property boolean $character_slots
 * @property string $pincode
 * @property int $pincode_change
 * @property int $vip_time
 * @property boolean $old_group
 * @property string $web_auth_token
 * @property boolean $web_auth_token_enabled
 * @property int $last_unique_id
 * @property int $blocked_unique_id
 */
class Login extends Model
{
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
     * @var array
     */
    protected $fillable = ['userid', 'user_pass', 'email', 'group_id', 'state', 'unban_time', 'expiration_time', 'logincount', 'lastlogin', 'last_ip', 'birthdate', 'character_slots', 'pincode', 'pincode_change', 'vip_time', 'old_group', 'web_auth_token', 'web_auth_token_enabled', 'last_unique_id', 'blocked_unique_id'];

}
