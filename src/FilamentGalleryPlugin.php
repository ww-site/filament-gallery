<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery;

use Filament\Contracts\Plugin;
use Filament\Panel;
use WwGallery\FilamentGallery\Filament\Pages\GalleryContent;
use WwGallery\FilamentGallery\Filament\Resources\MediaSources\MediaSourceResource;

final class FilamentGalleryPlugin implements Plugin
{
    private bool $registerGalleryPage = true;

    private bool $registerMediaSourcesResource = true;

    public static function make(): self
    {
        return app(self::class);
    }

    public function registerGalleryPage(bool $condition = true): self
    {
        $this->registerGalleryPage = $condition;

        return $this;
    }

    public function registerMediaSourcesResource(bool $condition = true): self
    {
        $this->registerMediaSourcesResource = $condition;

        return $this;
    }

    public function getId(): string
    {
        return 'filament-gallery';
    }

    public function register(Panel $panel): void
    {
        if ($this->shouldRegisterMediaSourcesResource()) {
            $panel->resources([
                MediaSourceResource::class,
            ]);
        }

        if ($this->shouldRegisterGalleryPage()) {
            $panel->pages([
                GalleryContent::class,
            ]);
        }
    }

    public function boot(Panel $panel): void
    {
        //
    }

    private function shouldRegisterGalleryPage(): bool
    {
        return $this->registerGalleryPage && (bool) config('filament-gallery.register_gallery_page', true);
    }

    private function shouldRegisterMediaSourcesResource(): bool
    {
        return $this->registerMediaSourcesResource && (bool) config('filament-gallery.register_media_sources_resource', true);
    }
}
