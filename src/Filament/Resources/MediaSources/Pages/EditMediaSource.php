<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery\Filament\Resources\MediaSources\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use WwGallery\FilamentGallery\Filament\Resources\MediaSources\MediaSourceResource;

final class EditMediaSource extends EditRecord
{
    protected static string $resource = MediaSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
