<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class MediaSourceSetting extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'media_source_id',
        'key',
        'value',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function mediaSource(): BelongsTo
    {
        return $this->belongsTo(MediaSource::class);
    }
}
