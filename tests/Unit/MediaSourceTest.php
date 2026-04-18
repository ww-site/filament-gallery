<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery\Tests\Unit;

use WwGallery\FilamentGallery\Models\MediaSource;
use WwGallery\FilamentGallery\Models\MediaSourceSetting;
use WwGallery\FilamentGallery\Tests\TestCase;

final class MediaSourceTest extends TestCase
{
    public function test_it_resolves_disk_and_directory_from_settings(): void
    {
        $source = MediaSource::query()->create([
            'name' => 'Test',
            'slug' => 'test-source',
            'description' => null,
            'is_active' => true,
        ]);

        MediaSourceSetting::query()->create([
            'media_source_id' => $source->id,
            'key' => 'disk',
            'value' => 'public',
            'sort_order' => 10,
        ]);

        MediaSourceSetting::query()->create([
            'media_source_id' => $source->id,
            'key' => 'directory',
            'value' => 'custom-dir',
            'sort_order' => 20,
        ]);

        $source->load('settings');

        $this->assertSame('public', $source->getDisk());
        $this->assertSame('custom-dir', $source->getDirectory());
        $this->assertSame('public', $source->getSetting('disk'));
        $this->assertSame('custom-dir', $source->getSetting('directory'));
    }

    public function test_it_falls_back_when_settings_missing(): void
    {
        $source = MediaSource::query()->create([
            'name' => 'Empty',
            'slug' => 'empty-source',
            'description' => null,
            'is_active' => true,
        ]);

        $source->load('settings');

        $this->assertSame('public', $source->getDisk());
        $this->assertSame('media-items', $source->getDirectory());
    }

    public function test_get_setting_returns_default_when_key_missing(): void
    {
        $source = MediaSource::query()->create([
            'name' => 'X',
            'slug' => 'x-source',
            'description' => null,
            'is_active' => true,
        ]);

        $source->load('settings');

        $this->assertSame('fallback', $source->getSetting('missing', 'fallback'));
    }
}
