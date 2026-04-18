<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use WwGallery\FilamentGallery\Models\MediaSource;

final class MediaSourceUrl
{
    public function make(string $sourceSlug, ?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        $source = MediaSource::query()
            ->with('settings')
            ->where('slug', $sourceSlug)
            ->first();

        if ($source === null) {
            return Storage::disk('public')->url($path);
        }

        $directory = trim($source->getDirectory(), '/');
        $normalizedPath = ltrim($path, '/');

        if ($directory !== '' && ! Str::startsWith($normalizedPath, $directory . '/')) {
            $normalizedPath = $directory . '/' . $normalizedPath;
        }

        return Storage::disk($source->getDisk())->url($normalizedPath);
    }
}
