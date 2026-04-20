<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery\Tests\Unit;

use Illuminate\Support\Facades\Storage;
use WwGallery\FilamentGallery\Models\MediaSource;
use WwGallery\FilamentGallery\Models\MediaSourceSetting;
use WwGallery\FilamentGallery\Tests\TestCase;

final class MediaSourceImageTest extends TestCase
{
    public function test_media_source_image_creates_cached_webp_file(): void
    {
        $this->resetPublicDisk();

        $slug = 'slider-images-' . uniqid();
        $this->makeMediaSourceWithStorage($slug, 'public', 'sliders');
        Storage::disk('public')->put('sliders/hero.jpg', $this->makeImageContents(120, 60));

        $url = media_source_image($slug, 'hero.jpg', [
            'width' => 80,
            'height' => 80,
            'crop' => true,
        ]);

        $cacheFiles = Storage::disk('public')->allFiles('filament-gallery/cache/' . $slug);

        $this->assertNotNull($url);
        $this->assertCount(1, $cacheFiles);
        $this->assertStringEndsWith('.webp', $cacheFiles[0]);
        $this->assertStringEndsWith($cacheFiles[0], $url);
    }

    public function test_media_source_image_keeps_ratio_when_only_width_is_passed(): void
    {
        $this->resetPublicDisk();

        $slug = 'product-images-' . uniqid();
        $this->makeMediaSourceWithStorage($slug, 'public', 'products');
        Storage::disk('public')->put('products/box.jpg', $this->makeImageContents(100, 50));

        media_source_image($slug, 'box.jpg', [
            'width' => 50,
            'format' => 'jpg',
        ]);

        $cacheFiles = Storage::disk('public')->allFiles('filament-gallery/cache/' . $slug);
        $size = getimagesize(Storage::disk('public')->path($cacheFiles[0]));

        $this->assertSame([50, 25], [$size[0], $size[1]]);
    }

    public function test_media_source_image_does_not_duplicate_source_directory(): void
    {
        $this->resetPublicDisk();

        $slug = 'product-images-' . uniqid();
        $this->makeMediaSourceWithStorage($slug, 'public', 'products');
        Storage::disk('public')->put('products/box.jpg', $this->makeImageContents(100, 50));

        media_source_image($slug, 'products/box.jpg', [
            'width' => 50,
        ]);

        $this->assertCount(1, Storage::disk('public')->allFiles('filament-gallery/cache/' . $slug));
    }

    public function test_media_source_image_returns_original_url_when_file_is_missing(): void
    {
        $this->resetPublicDisk();

        $this->makeMediaSourceWithStorage('missing-images', 'public', 'missing');

        $this->assertSame(
            '/storage/missing/nope.jpg',
            media_source_image('missing-images', 'nope.jpg', ['width' => 50]),
        );
    }

    private function makeMediaSourceWithStorage(string $slug, string $disk, string $directory): MediaSource
    {
        $source = MediaSource::query()->create([
            'name' => $slug,
            'slug' => $slug,
            'description' => null,
            'is_active' => true,
        ]);

        MediaSourceSetting::query()->create([
            'media_source_id' => $source->id,
            'key' => 'disk',
            'value' => $disk,
            'sort_order' => 10,
        ]);

        MediaSourceSetting::query()->create([
            'media_source_id' => $source->id,
            'key' => 'directory',
            'value' => $directory,
            'sort_order' => 20,
        ]);

        $source->load('settings');

        return $source;
    }

    private function makeImageContents(int $width, int $height): string
    {
        $image = imagecreatetruecolor($width, $height);

        ob_start();
        imagejpeg($image);
        $contents = ob_get_clean();
        imagedestroy($image);

        return is_string($contents) ? $contents : '';
    }
}
