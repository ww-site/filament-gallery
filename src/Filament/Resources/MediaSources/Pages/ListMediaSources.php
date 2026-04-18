<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery\Filament\Resources\MediaSources\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use WwGallery\FilamentGallery\Filament\Resources\MediaSources\MediaSourceResource;

final class ListMediaSources extends ListRecords
{
    protected static string $resource = MediaSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
