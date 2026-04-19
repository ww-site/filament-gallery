<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;
use WwGallery\FilamentGallery\Models\MediaSource;

final class MediaSourceBrowserService
{
    /**
     * @return list<array{name: string, path: string, url: string, type: string}>
     */
    public function listFiles(MediaSource $mediaSource): array
    {
        $disk = $mediaSource->getDisk();
        $directory = trim($mediaSource->getDirectory(), '/');

        return collect(Storage::disk($disk)->files($directory))
            ->filter(static fn (string $path): bool => $path !== '')
            ->sort()
            ->map(
                static fn (string $path): array => [
                    'name' => basename($path),
                    'path' => $path,
                    'url' => Storage::disk($disk)->url($path),
                    'type' => self::resolveFileType($path),
                ],
            )
            ->values()
            ->all();
    }

    /**
     * @return list<array{name: string, path: string, url: string, type: string}>
     */
    public function listImageFiles(MediaSource $mediaSource): array
    {
        return array_values(array_filter(
            $this->listFiles($mediaSource),
            static fn (array $file): bool => ($file['type'] ?? null) === 'image',
        ));
    }

    public function uploadFile(MediaSource $mediaSource, UploadedFile $file): string
    {
        $disk = $mediaSource->getDisk();
        $directory = trim($mediaSource->getDirectory(), '/');

        return $file->store($directory, $disk);
    }

    /**
     * @param array<int, UploadedFile> $files
     */
    public function uploadFiles(MediaSource $mediaSource, array $files): void
    {
        foreach ($files as $file) {
            $this->uploadFile($mediaSource, $file);
        }
    }

    /**
     * @param array<int, string> $paths
     */
    public function deleteFiles(MediaSource $mediaSource, array $paths): void
    {
        $disk = $mediaSource->getDisk();
        $directory = trim($mediaSource->getDirectory(), '/');

        $allowedPrefix = $directory === '' ? '' : $directory . '/';

        $filteredPaths = collect($paths)
            ->filter(static fn (string $path): bool => $path !== '')
            ->filter(
                static fn (string $path): bool => $allowedPrefix === ''
                    || $path === $directory
                    || str_starts_with($path, $allowedPrefix),
            )
            ->values()
            ->all();

        if ($filteredPaths === []) {
            return;
        }

        foreach ($filteredPaths as $path) {
            Storage::disk($disk)->delete($path);
            $this->deleteLocalFallback($disk, $path);
        }
    }

    private function deleteLocalFallback(string $disk, string $path): void
    {
        $storage = Storage::disk($disk);

        try {
            if (! $storage->exists($path)) {
                return;
            }

            $localPath = $storage->path($path);

            if (is_file($localPath)) {
                @unlink($localPath);
            }
        } catch (Throwable) {
            return;
        }
    }

    private static function resolveFileType(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($extension) {
            'jpg', 'jpeg', 'png', 'webp', 'gif' => 'image',
            'mp4' => 'video',
            default => 'file',
        };
    }
}
