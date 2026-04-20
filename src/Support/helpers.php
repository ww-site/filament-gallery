<?php

declare(strict_types=1);

use WwGallery\FilamentGallery\Support\MediaSourceUrl;
use WwGallery\FilamentGallery\Support\MediaSourceImage;

if (! function_exists('media_source_url')) {
    function media_source_url(string $sourceSlug, ?string $path): ?string
    {
        return app(MediaSourceUrl::class)->make($sourceSlug, $path);
    }
}

if (! function_exists('media_source_image')) {
    /**
     * @param  array<string, mixed>  $options
     */
    function media_source_image(string $sourceSlug, ?string $path, array $options = []): ?string
    {
        return app(MediaSourceImage::class)->make($sourceSlug, $path, $options);
    }
}
