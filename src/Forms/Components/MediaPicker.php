<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery\Forms\Components;

use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use WwGallery\FilamentGallery\Models\MediaSource;
use WwGallery\FilamentGallery\Services\MediaSourceBrowserService;

final class MediaPicker extends Field
{
    protected string $view = 'filament-gallery::forms.components.media-picker';

    protected string | null $mediaSourceSlug = null;

    protected ?int $mediaSourceId = null;

    /**
     * @var array<int, string> | null
     */
    protected ?array $acceptedFileTypes = null;

    protected ?int $maxFileSize = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registerActions([
            fn (self $component): Action => $component->getUploadImageAction(),
            fn (self $component): Action => $component->getSelectFromGalleryAction(),
            fn (self $component): Action => $component->getClearImageAction(),
        ]);
    }

    public function mediaSourceSlug(string $slug): static
    {
        $this->mediaSourceSlug = $slug;

        return $this;
    }

    public function mediaSourceId(int $id): static
    {
        $this->mediaSourceId = $id;

        return $this;
    }

    /**
     * @param array<int, string> $types
     */
    public function acceptedFileTypes(array $types): static
    {
        $this->acceptedFileTypes = $types;

        return $this;
    }

    public function maxFileSize(int $kilobytes): static
    {
        $this->maxFileSize = $kilobytes;

        return $this;
    }

    public function imagesOnly(): static
    {
        return $this->acceptedFileTypes($this->getDefaultAcceptedFileTypes());
    }

    public function getUploadImageAction(): Action
    {
        return Action::make('uploadImage')
            ->label(__('filament-gallery::filament-gallery.picker.upload_action'))
            ->icon(Heroicon::OutlinedArrowUpTray)
            ->modalWidth(Width::TwoExtraLarge)
            ->modalHeading(__('filament-gallery::filament-gallery.picker.upload_heading'))
            ->modalSubmitActionLabel(__('filament-gallery::filament-gallery.picker.submit_upload'))
            ->schema([
                FileUpload::make('uploaded_file')
                    ->label(__('filament-gallery::filament-gallery.picker.upload_field'))
                    ->acceptedFileTypes($this->getAcceptedFileTypes())
                    ->maxSize($this->getMaxFileSize())
                    ->required()
                    ->storeFiles(false),
            ])
            ->action(function (array $data, self $component, MediaSourceBrowserService $browser): void {
                $mediaSource = $component->getResolvedMediaSource();

                if ($mediaSource === null) {
                    Notification::make()
                        ->title(__('filament-gallery::filament-gallery.picker.notifications.source_not_found'))
                        ->danger()
                        ->send();

                    return;
                }

                $uploadedFile = $data['uploaded_file'] ?? null;

                if (! $uploadedFile instanceof UploadedFile) {
                    Notification::make()
                        ->title(__('filament-gallery::filament-gallery.picker.notifications.file_not_uploaded'))
                        ->danger()
                        ->send();

                    return;
                }

                $path = $browser->uploadFile($mediaSource, $uploadedFile);

                $component
                    ->state($path)
                    ->callAfterStateUpdated();

                Notification::make()
                    ->title(__('filament-gallery::filament-gallery.picker.notifications.image_uploaded'))
                    ->success()
                    ->send();
            });
    }

    public function getSelectFromGalleryAction(): Action
    {
        $component = $this;

        return Action::make('selectFromGallery')
            ->label(__('filament-gallery::filament-gallery.picker.select_action'))
            ->icon(Heroicon::OutlinedPhoto)
            ->modalWidth(Width::FiveExtraLarge)
            ->modalHeading(__('filament-gallery::filament-gallery.picker.select_heading'))
            ->modalSubmitActionLabel(__('filament-gallery::filament-gallery.picker.submit_select'))
            ->fillForm(fn (self $component): array => [
                'selection' => filled($component->getState()) ? [$component->getState()] : [],
            ])
            ->schema([
                CheckboxList::make('selection')
                    ->label(__('filament-gallery::filament-gallery.picker.gallery_label'))
                    ->hiddenLabel()
                    ->options(fn (): array => $component->getGalleryOptions())
                    ->allowHtml()
                    ->required()
                    ->maxItems(1),
            ])
            ->action(function (array $data, self $component): void {
                $selectedPath = $data['selection'][0] ?? null;

                if (! is_string($selectedPath) || $selectedPath === '') {
                    Notification::make()
                        ->title(__('filament-gallery::filament-gallery.picker.notifications.select_image_first'))
                        ->warning()
                        ->send();

                    return;
                }

                $component
                    ->state($selectedPath)
                    ->callAfterStateUpdated();

                Notification::make()
                    ->title(__('filament-gallery::filament-gallery.picker.notifications.image_selected'))
                    ->success()
                    ->send();
            });
    }

    public function getClearImageAction(): Action
    {
        return Action::make('clearImage')
            ->label(__('filament-gallery::filament-gallery.picker.clear_action'))
            ->icon(Heroicon::OutlinedXMark)
            ->color('gray')
            ->visible(fn (self $component): bool => filled($component->getState()))
            ->action(function (self $component): void {
                $component
                    ->state(null)
                    ->callAfterStateUpdated();
            });
    }

    public function getResolvedMediaSource(): ?MediaSource
    {
        return MediaSource::query()
            ->with('settings')
            ->when(
                $this->mediaSourceId !== null,
                fn ($query) => $query->whereKey($this->mediaSourceId),
                fn ($query) => $query->where('slug', $this->mediaSourceSlug),
            )
            ->where('is_active', true)
            ->first();
    }

    public function getPreviewUrl(): ?string
    {
        $state = $this->getState();

        if (! is_string($state) || $state === '') {
            return null;
        }

        $mediaSource = $this->getResolvedMediaSource();

        if ($mediaSource === null) {
            return null;
        }

        return Storage::disk($mediaSource->getDisk())->url($state);
    }

    /**
     * @return array<string, Htmlable>
     */
    public function getGalleryOptions(): array
    {
        $mediaSource = $this->getResolvedMediaSource();

        if ($mediaSource === null) {
            return [];
        }

        $files = app(MediaSourceBrowserService::class)->listImageFiles($mediaSource);

        $options = [];

        foreach ($files as $file) {
            $options[$file['path']] = new HtmlString(sprintf(
                '<div class="flex items-center gap-3"><img src="%s" alt="%s" class="h-14 w-14 rounded-xl object-cover"><div class="min-w-0"><div class="truncate font-medium">%s</div><div class="truncate text-xs text-gray-500">%s</div></div></div>',
                e($file['url']),
                e($file['name']),
                e($file['name']),
                e($file['path']),
            ));
        }

        return $options;
    }

    /**
     * @return array<int, string>
     */
    public function getAcceptedFileTypes(): array
    {
        return $this->acceptedFileTypes ?? $this->getDefaultAcceptedFileTypes();
    }

    public function getMaxFileSize(): int
    {
        return $this->maxFileSize ?? (int) config('filament-gallery.picker.max_upload_size', 10240);
    }

    /**
     * @return array<int, string>
     */
    private function getDefaultAcceptedFileTypes(): array
    {
        /** @var array<int, string> $types */
        $types = config('filament-gallery.picker.accepted_file_types', []);

        return $types;
    }
}
