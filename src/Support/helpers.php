<?php

declare(strict_types=1);

use WwGallery\FilamentGallery\Support\MediaSourceUrl;

if (! function_exists('media_source_url')) {
    function media_source_url(string $sourceSlug, ?string $path): ?string
    {
        return app(MediaSourceUrl::class)->make($sourceSlug, $path);
    }
}
