<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;
use WwGallery\FilamentGallery\Models\MediaSource;

final readonly class MediaSourceImage
{
    /**
     * @param  array<string, mixed>  $options
     */
    public function make(string $sourceSlug, ?string $path, array $options = []): ?string
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
            return media_source_url($sourceSlug, $path);
        }

        $sourceDisk = $source->getDisk();
        $sourcePath = $this->normalizeSourcePath($source, $path);

        if (! Storage::disk($sourceDisk)->exists($sourcePath)) {
            return media_source_url($sourceSlug, $path);
        }

        $format = $this->stringOption($source, $options, 'format', 'default_format', 'webp');

        if (! in_array($format, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return media_source_url($sourceSlug, $path);
        }

        $cacheDisk = $this->stringOption($source, $options, 'cache_disk', 'cache_disk', 'public');
        $cacheDirectory = trim($this->stringOption($source, $options, 'cache_directory', 'cache_directory', 'filament-gallery/cache'), '/');
        $quality = $this->intOption($source, $options, 'quality', 'default_quality', 85);
        $crop = $this->boolOption($source, $options, 'crop', 'default_crop', false);
        $background = $this->stringOption($source, $options, 'background', 'default_background', 'ffffff');
        $width = $this->nullableIntOption($options, 'width');
        $height = $this->nullableIntOption($options, 'height');
        $extension = $format === 'jpeg' ? 'jpg' : $format;
        $cachePath = $this->makeCachePath(
            $cacheDirectory,
            $sourceSlug,
            $sourcePath,
            $extension,
            [
                'width' => $width,
                'height' => $height,
                'quality' => $quality,
                'crop' => $crop,
                'background' => $background,
                'modified' => $this->lastModified($sourceDisk, $sourcePath),
            ],
        );

        if (! Storage::disk($cacheDisk)->exists($cachePath)) {
            try {
                $this->writeCacheFile(
                    $sourceDisk,
                    $sourcePath,
                    $cacheDisk,
                    $cachePath,
                    $extension,
                    $quality,
                    $crop,
                    $background,
                    $width,
                    $height,
                );
            } catch (Throwable) {
                return media_source_url($sourceSlug, $path);
            }
        }

        return Storage::disk($cacheDisk)->url($cachePath);
    }

    private function normalizeSourcePath(MediaSource $source, string $path): string
    {
        $directory = trim($source->getDirectory(), '/');
        $normalizedPath = ltrim($path, '/');

        if ($directory !== '' && ! Str::startsWith($normalizedPath, $directory . '/')) {
            return $directory . '/' . $normalizedPath;
        }

        return $normalizedPath;
    }

    /**
     * @param  array<string, mixed>  $cacheOptions
     */
    private function makeCachePath(
        string $cacheDirectory,
        string $sourceSlug,
        string $sourcePath,
        string $extension,
        array $cacheOptions,
    ): string {
        $hash = hash('sha256', $sourceSlug . '|' . $sourcePath . '|' . json_encode($cacheOptions, JSON_THROW_ON_ERROR));

        return trim($cacheDirectory . '/' . $sourceSlug . '/' . $hash . '.' . $extension, '/');
    }

    private function writeCacheFile(
        string $sourceDisk,
        string $sourcePath,
        string $cacheDisk,
        string $cachePath,
        string $extension,
        int $quality,
        bool $crop,
        string $background,
        ?int $width,
        ?int $height,
    ): void {
        $contents = Storage::disk($sourceDisk)->get($sourcePath);
        $image = ImageManager::withDriver(Driver::class)->read($contents);

        if ($width !== null && $height !== null) {
            if ($crop) {
                $image->cover($width, $height);
            } else {
                $image->contain($width, $height, $background);
            }
        } elseif ($width !== null || $height !== null) {
            $image->scale($width, $height);
        }

        $encoded = match ($extension) {
            'jpg' => $image->toJpeg($quality),
            'png' => $image->toPng(),
            'webp' => $image->toWebp($quality),
            default => $image->encode(),
        };

        Storage::disk($cacheDisk)->put($cachePath, (string) $encoded);
    }

    /**
     * @param  array<string, mixed>  $options
     */
    private function stringOption(MediaSource $source, array $options, string $optionKey, string $settingKey, string $default): string
    {
        $value = $options[$optionKey]
            ?? $source->getSetting($settingKey)
            ?? config('filament-gallery.image.' . $settingKey)
            ?? $default;

        return (string) $value;
    }

    /**
     * @param  array<string, mixed>  $options
     */
    private function intOption(MediaSource $source, array $options, string $optionKey, string $settingKey, int $default): int
    {
        $value = $options[$optionKey]
            ?? $source->getSetting($settingKey)
            ?? config('filament-gallery.image.' . $settingKey)
            ?? $default;

        return max(1, min(100, (int) $value));
    }

    /**
     * @param  array<string, mixed>  $options
     */
    private function boolOption(MediaSource $source, array $options, string $optionKey, string $settingKey, bool $default): bool
    {
        $value = $options[$optionKey]
            ?? $source->getSetting($settingKey)
            ?? config('filament-gallery.image.' . $settingKey)
            ?? $default;

        return filter_var($value, FILTER_VALIDATE_BOOL);
    }

    /**
     * @param  array<string, mixed>  $options
     */
    private function nullableIntOption(array $options, string $key): ?int
    {
        if (! array_key_exists($key, $options) || $options[$key] === null || $options[$key] === '') {
            return null;
        }

        return max(1, (int) $options[$key]);
    }

    private function lastModified(string $disk, string $path): ?int
    {
        try {
            return Storage::disk($disk)->lastModified($path);
        } catch (Throwable) {
            return null;
        }
    }
}
