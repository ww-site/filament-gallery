# WwGallery — Filament Gallery

Reusable Filament 5 gallery plugin built around two simple ideas:

- a `media_sources` entity describing where files live (disk + directory)
- a set of Filament UI primitives to upload, browse, pick and display those files

Install from [Packagist](https://packagist.org/packages/ww-site/filament-gallery) or via a Composer `path` repository during local development.

- **Package:** `ww-site/filament-gallery`
- **Namespace:** `WwGallery\FilamentGallery\`
- **Author:** Гончаров Степан — <https://wwsite.ru> — <in001@z1q.ru>

## What's included

| Layer      | Class                                                                            | Purpose                                       |
|------------|----------------------------------------------------------------------------------|-----------------------------------------------|
| Model      | `WwGallery\FilamentGallery\Models\MediaSource`                                   | Parent entity (`name`, `slug`, `is_active`)   |
| Model      | `WwGallery\FilamentGallery\Models\MediaSourceSetting`                            | Key/value settings (e.g. `disk`, `directory`) |
| Service    | `WwGallery\FilamentGallery\Services\MediaSourceBrowserService`                   | List / upload / delete files on a source      |
| Resource   | `WwGallery\FilamentGallery\Filament\Resources\MediaSources\MediaSourceResource`  | Admin CRUD for media sources and settings     |
| Page       | `WwGallery\FilamentGallery\Filament\Pages\GalleryContent`                        | Filament page to browse & manage files        |
| Form field | `WwGallery\FilamentGallery\Forms\Components\MediaPicker`                         | Upload + pick image from a media source       |
| Column     | `WwGallery\FilamentGallery\Tables\Columns\MediaPreviewColumn`                    | Table preview resolving disk from a source    |
| Entry      | `WwGallery\FilamentGallery\Infolists\Components\MediaPreviewEntry`               | Infolist preview resolving disk from a source |
| Plugin     | `WwGallery\FilamentGallery\FilamentGalleryPlugin`                                | Filament panel plugin entry point             |
| Provider   | `WwGallery\FilamentGallery\FilamentGalleryServiceProvider`                       | Loads migrations, config, views, langs        |

## Installation in a host project

1. Install via Composer from Packagist:
   ```bash
   composer require ww-site/filament-gallery
   ```
   Or, for local development from a path repository:
   ```json
   "repositories": {
       "ww-gallery": { "type": "path", "url": "../packages/ww-gallery" }
   },
   "require": {
       "ww-site/filament-gallery": "dev-master"
   }
   ```
2. Run `composer update ww-site/filament-gallery`.
3. Register the plugin inside the target panel provider:
   ```php
   ->plugin(\WwGallery\FilamentGallery\FilamentGalleryPlugin::make())
   ```
4. Run migrations: `php artisan migrate`.
5. (Optional) publish config/translations:
   ```
   php artisan vendor:publish --tag=filament-gallery-config
   php artisan vendor:publish --tag=filament-gallery-translations
   ```

### Filament admin theme & Tailwind (styling)

Filament 5 builds the panel CSS with **Vite** and **Tailwind CSS v4**. Your panel `theme.css` only includes utilities for files listed in `@source` directives. This package ships **Blade views** and **HTML built from PHP** (e.g. gallery thumbnails inside the MediaPicker modal). If you do not extend Tailwind’s content paths, those screens look unstyled (oversized images, missing layout).

In your host app, open the Filament theme file you pass to `->viteTheme(...)` (for example `resources/css/filament/admin/theme.css`) and add **both** lines (paths are relative to that file; adjust if your theme lives elsewhere):

```css
/* Package Blade views */
@source '../../../../vendor/ww-site/filament-gallery/resources/views/**/*';

/* Tailwind classes inside PHP (HtmlString labels, etc.) */
@source '../../../../vendor/ww-site/filament-gallery/src/**/*.php';
```

Then rebuild assets:

```bash
npm run build
# or during development:
npm run dev
```

After every `composer update ww-site/filament-gallery`, run the build again if the package added or changed Tailwind classes.

## Usage

### Seed a media source

```php
$source = \WwGallery\FilamentGallery\Models\MediaSource::create([
    'name' => 'Изображения категорий',
    'slug' => 'category-images',
    'is_active' => true,
]);
$source->settings()->create(['key' => 'disk',      'value' => 'public',     'sort_order' => 10]);
$source->settings()->create(['key' => 'directory', 'value' => 'categories', 'sort_order' => 20]);
```

### Pick an image inside a form

```php
use WwGallery\FilamentGallery\Forms\Components\MediaPicker;

MediaPicker::make('image')
    ->label('Изображение категории')
    ->mediaSourceSlug('category-images')
    ->imagesOnly();
```

### Render a preview in a table

```php
use WwGallery\FilamentGallery\Tables\Columns\MediaPreviewColumn;

MediaPreviewColumn::make('image')
    ->label('Изображение')
    ->mediaSourceSlug('category-images');
```

### Render a preview in an infolist

```php
use WwGallery\FilamentGallery\Infolists\Components\MediaPreviewEntry;

MediaPreviewEntry::make('image')
    ->label('Изображение')
    ->mediaSourceSlug('category-images');
```

## Configuration

Default config is in `config/filament-gallery.php`:

- `page.accepted_file_types` / `page.max_upload_size` — uploads on the gallery page
- `picker.accepted_file_types` / `picker.max_upload_size` — uploads via the MediaPicker
- `register_gallery_page` / `register_media_sources_resource` — toggles to hide pieces from a panel

Per-panel overrides live on the plugin:

```php
FilamentGalleryPlugin::make()
    ->registerGalleryPage(false)
    ->registerMediaSourcesResource();
```

## Translations

Package ships with `en` and `ru`. Add your own by publishing translations and
editing `lang/vendor/filament-gallery/<locale>/filament-gallery.php`.

## Testing

Automated tests live in this package (PHPUnit + [Orchestra Testbench](https://github.com/orchestral/testbench)). They are **not** installed in host applications — only `require-dev` when you work on the package itself.

From the package root (e.g. a git clone or your monorepo `packages/ww-gallery`):

```bash
composer update
composer test
```

`composer test` runs `phpunit` using `phpunit.xml.dist`.

**What is covered today:**

- `MediaSource` — settings, `getDisk()` / `getDirectory()`, defaults
- `MediaSourceBrowserService` — listing, image filter, upload, delete with path prefix rules
- `FilamentGalleryPlugin` — stable plugin id

Contributors: add cases under `tests/Unit/` and keep `declare(strict_types=1);` on new files.

## Roadmap

- lightbox/viewer for the gallery page
- multi-file MediaPicker mode
- per-source policies / authorization hooks
- PSR-4 autodiscovery inside the panel provider
- broader Filament/Livewire feature tests (optional)
