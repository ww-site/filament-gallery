<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery\Filament\Resources\MediaSources\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class MediaSourceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('filament-gallery::filament-gallery.resource.media_source.sections.main'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('filament-gallery::filament-gallery.resource.media_source.fields.name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->label(__('filament-gallery::filament-gallery.resource.media_source.fields.slug'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Toggle::make('is_active')
                            ->label(__('filament-gallery::filament-gallery.resource.media_source.fields.is_active'))
                            ->default(true),
                        Textarea::make('description')
                            ->label(__('filament-gallery::filament-gallery.resource.media_source.fields.description'))
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
