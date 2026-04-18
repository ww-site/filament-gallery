<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use WwGallery\FilamentGallery\Models\MediaSource;
use WwGallery\FilamentGallery\Services\MediaSourceBrowserService;
use UnitEnum;

final class GalleryContent extends Page implements HasForms
{
    use InteractsWithForms;
    use WithFileUploads;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected string $view = 'filament-gallery::filament.pages.gallery-content';

    public ?int $mediaSourceId = null;

    /**
     * @var array<int, TemporaryUploadedFile>
     */
    public array $uploads = [];

    /**
     * @var array<int, string>
     */
    public array $selectedFiles = [];

    /**
     * @var list<array{name: string, path: string, url: string, type: string}>
     */
    public array $files = [];

    protected function getForms(): array
    {
        return [
            'sourceForm',
            'form',
        ];
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('filament-gallery::filament-gallery.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-gallery::filament-gallery.navigation.gallery_content');
    }

    public function getTitle(): string
    {
        return __('filament-gallery::filament-gallery.navigation.gallery_content');
    }

    public function mount(MediaSourceBrowserService $browser): void
    {
        $this->mediaSourceId = MediaSource::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->value('id');

        $this->sourceForm->fill([
            'mediaSourceId' => $this->mediaSourceId,
        ]);

        $this->form->fill([
            'uploads' => [],
        ]);

        $this->reloadFiles($browser);
    }

    public function sourceForm(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('mediaSourceId')
                    ->label(__('filament-gallery::filament-gallery.gallery_page.source_label'))
                    ->options(fn (): array => $this->getMediaSourceOptions())
                    ->native(false)
                    ->searchable()
                    ->live(),
            ])
            ->statePath('');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                FileUpload::make('uploads')
                    ->label(__('filament-gallery::filament-gallery.picker.upload_field'))
                    ->multiple()
                    ->acceptedFileTypes($this->getAcceptedFileTypes())
                    ->maxSize($this->getMaxUploadSize())
                    ->storeFiles(false),
            ])
            ->statePath('');
    }

    public function updatedMediaSourceId(): void
    {
        $this->selectedFiles = [];
        $this->reloadFiles(app(MediaSourceBrowserService::class));
    }

    public function saveUploads(MediaSourceBrowserService $browser): void
    {
        $this->validate([
            'mediaSourceId' => ['required', 'integer', 'exists:media_sources,id'],
            'uploads' => ['required', 'array', 'min:1'],
            'uploads.*' => ['file', 'mimetypes:' . implode(',', $this->getAcceptedFileTypes()), 'max:' . $this->getMaxUploadSize()],
        ]);

        $mediaSource = $this->getSelectedMediaSource();

        if ($mediaSource === null) {
            Notification::make()
                ->title(__('filament-gallery::filament-gallery.gallery_page.notifications.source_not_found'))
                ->danger()
                ->send();

            return;
        }

        $browser->uploadFiles($mediaSource, $this->uploads);

        $this->uploads = [];
        $this->form->fill([
            'uploads' => [],
        ]);
        $this->reloadFiles($browser);

        Notification::make()
            ->title(__('filament-gallery::filament-gallery.gallery_page.notifications.files_uploaded'))
            ->success()
            ->send();
    }

    public function refreshGallery(MediaSourceBrowserService $browser): void
    {
        $this->reloadFiles($browser);
    }

    public function deleteSelectedFiles(MediaSourceBrowserService $browser): void
    {
        $mediaSource = $this->getSelectedMediaSource();

        if ($mediaSource === null) {
            Notification::make()
                ->title(__('filament-gallery::filament-gallery.gallery_page.notifications.source_not_found'))
                ->danger()
                ->send();

            return;
        }

        if ($this->selectedFiles === []) {
            Notification::make()
                ->title(__('filament-gallery::filament-gallery.gallery_page.notifications.select_files_first'))
                ->warning()
                ->send();

            return;
        }

        $browser->deleteFiles($mediaSource, $this->selectedFiles);

        $this->selectedFiles = [];
        $this->reloadFiles($browser);

        Notification::make()
            ->title(__('filament-gallery::filament-gallery.gallery_page.notifications.selected_files_deleted'))
            ->success()
            ->send();
    }

    /**
     * @return array<int, string>
     */
    public function getMediaSourceOptions(): array
    {
        return MediaSource::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public function getSelectedMediaSource(): ?MediaSource
    {
        if ($this->mediaSourceId === null) {
            return null;
        }

        return MediaSource::query()
            ->with('settings')
            ->find($this->mediaSourceId);
    }

    /**
     * @return array<int, string>
     */
    public function getAcceptedFileTypes(): array
    {
        /** @var array<int, string> $types */
        $types = config('filament-gallery.page.accepted_file_types', []);

        return $types;
    }

    public function getMaxUploadSize(): int
    {
        return (int) config('filament-gallery.page.max_upload_size', 51200);
    }

    private function reloadFiles(MediaSourceBrowserService $browser): void
    {
        $mediaSource = $this->getSelectedMediaSource();

        if ($mediaSource === null) {
            $this->files = [];

            return;
        }

        $this->files = $browser->listFiles($mediaSource);
    }
}
