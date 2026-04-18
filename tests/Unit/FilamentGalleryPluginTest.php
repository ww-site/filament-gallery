<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery\Tests\Unit;

use WwGallery\FilamentGallery\FilamentGalleryPlugin;
use WwGallery\FilamentGallery\Tests\TestCase;

final class FilamentGalleryPluginTest extends TestCase
{
    public function test_plugin_exposes_stable_identifier(): void
    {
        $plugin = $this->app->make(FilamentGalleryPlugin::class);

        $this->assertSame('filament-gallery', $plugin->getId());
    }
}
