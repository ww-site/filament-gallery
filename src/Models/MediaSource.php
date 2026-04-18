<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class MediaSource extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function settings(): HasMany
    {
        return $this->hasMany(MediaSourceSetting::class)->orderBy('sort_order')->orderBy('id');
    }

    public function getSetting(string $key, ?string $default = null): ?string
    {
        $setting = $this->settings
            ->firstWhere('key', $key);

        return $setting?->value ?? $default;
    }

    public function getDisk(): string
    {
        return $this->getSetting('disk', 'public') ?? 'public';
    }

    public function getDirectory(): string
    {
        return $this->getSetting('directory', 'media-items') ?? 'media-items';
    }
}
