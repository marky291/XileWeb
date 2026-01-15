<?php

namespace App\XileRO;

/**
 * @property int $account_id
 * @property string $key
 * @property int $index
 * @property string $value
 */
class XileRO_AccRegStr extends XileRO_Model
{
    public const GAME_COMMAND_SECURITY_CODE = '#SecuCode$';

    protected $connection = 'xilero_main';

    protected $table = 'acc_reg_str';

    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = null;

    protected $fillable = [
        'account_id',
        'key',
        'index',
        'value',
    ];

    public function getKeyName(): array
    {
        return ['account_id', 'key', 'index'];
    }

    /**
     * Delete the security code for an account.
     */
    public static function deleteSecurityCode(int $accountId): int
    {
        return static::where('account_id', $accountId)
            ->where('key', static::GAME_COMMAND_SECURITY_CODE)
            ->delete();
    }

    /**
     * Check if an account has a security code set.
     */
    public static function hasSecurityCode(int $accountId): bool
    {
        return static::where('account_id', $accountId)
            ->where('key', static::GAME_COMMAND_SECURITY_CODE)
            ->exists();
    }
}
