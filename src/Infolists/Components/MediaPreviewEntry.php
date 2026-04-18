<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery\Infolists\Components;

use Filament\Infolists\Components\ImageEntry;
use WwGallery\FilamentGallery\Models\MediaSource;

final class MediaPreviewEntry extends ImageEntry
{
    protected ?string $mediaSourceSlug = null;

    protected ?int $mediaSourceId = null;

    protected ?MediaSource $resolvedMediaSource = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->square();
        $this->checkFileExistence(false);
        $this->disk(fn (): string => $this->resolveDisk());
    }

    public function mediaSourceSlug(string $slug): static
    {
        $this->mediaSourceSlug = $slug;
        $this->resolvedMediaSource = null;

        return $this;
    }

    public function mediaSourceId(int $id): static
    {
        $this->mediaSourceId = $id;
        $this->resolvedMediaSource = null;

        return $this;
    }

    public function getResolvedMediaSource(): ?MediaSource
    {
        if ($this->resolvedMediaSource !== null) {
            return $this->resolvedMediaSource;
        }

        if ($this->mediaSourceId === null && $this->mediaSourceSlug === null) {
            return null;
        }

        return $this->resolvedMediaSource = MediaSource::query()
            ->with('settings')
            ->when(
                $this->mediaSourceId !== null,
                fn ($query) => $query->whereKey($this->mediaSourceId),
                fn ($query) => $query->where('slug', $this->mediaSourceSlug),
            )
            ->first();
    }

    private function resolveDisk(): string
    {
        $mediaSource = $this->getResolvedMediaSource();

        if ($mediaSource === null) {
            return (string) config('filament.default_filesystem_disk', 'public');
        }

        return $mediaSource->getDisk();
    }
}
