<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Download extends Model
{
    use HasFactory;

    const TYPE_FULL = 'full';

    const TYPE_ANDROID = 'android';

    const TYPES = [
        self::TYPE_FULL => 'Full Client',
        self::TYPE_ANDROID => 'Android',
    ];

    const BUTTON_STYLE_PRIMARY = 'primary';

    const BUTTON_STYLE_SECONDARY = 'secondary';

    const BUTTON_STYLES = [
        self::BUTTON_STYLE_PRIMARY => 'Primary',
        self::BUTTON_STYLE_SECONDARY => 'Secondary',
    ];

    protected $fillable = [
        'name',
        'type',
        'link',
        'file',
        'file_name',
        'version',
        'button_style',
        'display_order',
        'enabled',
    ];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    public function getDownloadUrlAttribute(): ?string
    {
        if ($this->file) {
            return Storage::disk('android_apk')->url($this->file);
        }

        return $this->link;
    }

    public function getButtonClassAttribute(): string
    {
        return match ($this->button_style) {
            self::BUTTON_STYLE_PRIMARY => 'btn-primary',
            self::BUTTON_STYLE_SECONDARY => 'btn-secondary',
            default => 'btn-primary',
        };
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->version) {
            return 'v'.$this->version.' - '.$this->name;
        }

        return $this->name;
    }

    public function scopeFull(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_FULL)
            ->where('enabled', true)
            ->orderBy('display_order');
    }

    public function scopeAndroid(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_ANDROID)
            ->where('enabled', true)
            ->orderBy('display_order');
    }
}
