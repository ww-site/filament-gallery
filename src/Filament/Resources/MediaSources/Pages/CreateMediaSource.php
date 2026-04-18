<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery\Filament\Resources\MediaSources\Pages;

use Filament\Resources\Pages\CreateRecord;
use WwGallery\FilamentGallery\Filament\Resources\MediaSources\MediaSourceResource;

final class CreateMediaSource extends CreateRecord
{
    protected static string $resource = MediaSourceResource::class;
}
