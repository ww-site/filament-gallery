<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery\Tests\Unit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use WwGallery\FilamentGallery\Models\MediaSource;
use WwGallery\FilamentGallery\Models\MediaSourceSetting;
use WwGallery\FilamentGallery\Services\MediaSourceBrowserService;
use WwGallery\FilamentGallery\Tests\TestCase;

final class MediaSourceBrowserServiceTest extends TestCase
{
    public function test_list_files_returns_sorted_entries_with_types(): void
    {
        Storage::fake('public');

        $source = $this->makeMediaSourceWithStorage('public', 'products');

        Storage::disk('public')->put('products/b.png', 'fake');
        Storage::disk('public')->put('products/a.jpg', 'fake');

        $service = new MediaSourceBrowserService();
        $files = $service->listFiles($source);

        $this->assertCount(2, $files);
        $this->assertSame('a.jpg', $files[0]['name']);
        $this->assertSame('products/a.jpg', $files[0]['path']);
        $this->assertSame('image', $files[0]['type']);
        $this->assertSame('b.png', $files[1]['name']);
    }

    public function test_list_image_files_filters_non_images(): void
    {
        Storage::fake('public');

        $source = $this->makeMediaSourceWithStorage('public', 'gallery');

        Storage::disk('public')->put('gallery/photo.webp', 'fake');
        Storage::disk('public')->put('gallery/readme.txt', 'x');

        $service = new MediaSourceBrowserService();
        $images = $service->listImageFiles($source);

        $this->assertCount(1, $images);
        $this->assertSame('photo.webp', $images[0]['name']);
    }

    public function test_upload_file_stores_under_configured_directory(): void
    {
        Storage::fake('public');

        $source = $this->makeMediaSourceWithStorage('public', 'uploads');

        $file = UploadedFile::fake()->image('new.jpg', 10, 10);

        $service = new MediaSourceBrowserService();
        $path = $service->uploadFile($source, $file);

        $this->assertStringStartsWith('uploads/', $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_delete_files_removes_only_paths_inside_source_directory(): void
    {
        Storage::fake('public');

        $source = $this->makeMediaSourceWithStorage('public', 'safe');

        Storage::disk('public')->put('safe/keep.jpg', 'x');
        Storage::disk('public')->put('safe/remove.jpg', 'x');
        Storage::disk('public')->put('other/nope.jpg', 'x');

        $service = new MediaSourceBrowserService();
        $service->deleteFiles($source, [
            'safe/remove.jpg',
            'other/nope.jpg',
        ]);

        Storage::disk('public')->assertExists('safe/keep.jpg');
        Storage::disk('public')->assertExists('other/nope.jpg');
        Storage::disk('public')->assertMissing('safe/remove.jpg');
    }

    public function test_delete_files_ignores_empty_paths(): void
    {
        Storage::fake('public');

        $source = $this->makeMediaSourceWithStorage('public', 'd');

        Storage::disk('public')->put('d/x.jpg', '1');

        $service = new MediaSourceBrowserService();
        $service->deleteFiles($source, ['', 'd/x.jpg']);

        Storage::disk('public')->assertMissing('d/x.jpg');
    }

    private function makeMediaSourceWithStorage(string $disk, string $directory): MediaSource
    {
        $source = MediaSource::query()->create([
            'name' => 'Browser',
            'slug' => 'browser-' . $directory,
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
}
