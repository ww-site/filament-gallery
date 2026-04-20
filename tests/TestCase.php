<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use WwGallery\FilamentGallery\FilamentGalleryServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        File::deleteDirectory($this->publicDiskRoot());
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            FilamentGalleryServiceProvider::class,
        ];
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('filesystems.disks.public', [
            'driver' => 'local',
            'root' => getcwd() . '/.test-storage/public',
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
        ]);
    }

    protected function resetPublicDisk(): void
    {
        Storage::forgetDisk('public');
        File::deleteDirectory($this->publicDiskRoot());
        File::ensureDirectoryExists($this->publicDiskRoot());
    }

    private function publicDiskRoot(): string
    {
        return getcwd() . '/.test-storage/public';
    }
}
