<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery\Tests\Unit;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use WwGallery\FilamentGallery\Models\MediaSource;
use WwGallery\FilamentGallery\Models\MediaSourceSetting;
use WwGallery\FilamentGallery\Services\MediaSourceBrowserService;
use WwGallery\FilamentGallery\Tests\TestCase;

final class MediaSourceBrowserServiceTest extends TestCase
{
    public function test_list_files_returns_sorted_entries_with_types(): void
    {
        $this->resetPublicDisk();

        $directory = 'products-' . uniqid();
        $source = $this->makeMediaSourceWithStorage('public', $directory);

        Storage::disk('public')->put($directory . '/b.png', 'fake');
        Storage::disk('public')->put($directory . '/a.jpg', 'fake');

        $service = new MediaSourceBrowserService();
        $files = $service->listFiles($source);

        $this->assertCount(2, $files);
        $this->assertSame('a.jpg', $files[0]['name']);
        $this->assertSame($directory . '/a.jpg', $files[0]['path']);
        $this->assertSame('image', $files[0]['type']);
        $this->assertSame('b.png', $files[1]['name']);
    }

    public function test_list_image_files_filters_non_images(): void
    {
        $this->resetPublicDisk();

        $directory = 'gallery-' . uniqid();
        $source = $this->makeMediaSourceWithStorage('public', $directory);

        Storage::disk('public')->put($directory . '/photo.webp', 'fake');
        Storage::disk('public')->put($directory . '/readme.txt', 'x');

        $service = new MediaSourceBrowserService();
        $images = $service->listImageFiles($source);

        $this->assertCount(1, $images);
        $this->assertSame('photo.webp', $images[0]['name']);
    }

    public function test_upload_file_stores_under_configured_directory(): void
    {
        $this->resetPublicDisk();

        $directory = 'uploads-' . uniqid();
        $source = $this->makeMediaSourceWithStorage('public', $directory);

        $file = $this->makeUploadedImage('new.jpg');

        $service = new MediaSourceBrowserService();
        $path = $service->uploadFile($source, $file);

        $this->assertStringStartsWith($directory . '/', $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_delete_files_removes_only_paths_inside_source_directory(): void
    {
        $this->resetPublicDisk();

        $directory = 'safe-' . uniqid();
        $source = $this->makeMediaSourceWithStorage('public', $directory);

        Storage::disk('public')->put($directory . '/keep.jpg', 'x');
        Storage::disk('public')->put($directory . '/remove.jpg', 'x');
        Storage::disk('public')->put('other/nope.jpg', 'x');

        $service = new MediaSourceBrowserService();
        $service->deleteFiles($source, [
            $directory . '/remove.jpg',
            'other/nope.jpg',
        ]);

        Storage::disk('public')->assertExists($directory . '/keep.jpg');
        Storage::disk('public')->assertExists('other/nope.jpg');
        Storage::disk('public')->assertMissing($directory . '/remove.jpg');
    }

    public function test_delete_files_ignores_empty_paths(): void
    {
        $this->resetPublicDisk();

        $directory = 'd-' . uniqid();
        $source = $this->makeMediaSourceWithStorage('public', $directory);

        Storage::disk('public')->put($directory . '/x.jpg', '1');

        $service = new MediaSourceBrowserService();
        $service->deleteFiles($source, ['', $directory . '/x.jpg']);

        Storage::disk('public')->assertMissing($directory . '/x.jpg');
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

    private function makeUploadedImage(string $name): UploadedFile
    {
        $directory = storage_path('framework/testing/uploads');

        File::ensureDirectoryExists($directory);

        $path = $directory . '/' . uniqid('image_', true) . '.jpg';

        file_put_contents($path, $this->makeImageContents());

        return new UploadedFile($path, $name, 'image/jpeg', null, true);
    }

    private function makeImageContents(): string
    {
        $image = imagecreatetruecolor(10, 10);

        ob_start();
        imagejpeg($image);
        $contents = ob_get_clean();
        imagedestroy($image);

        return is_string($contents) ? $contents : '';
    }
}
