<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery\Filament\Resources\MediaSources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use WwGallery\FilamentGallery\Filament\Resources\MediaSources\Pages\CreateMediaSource;
use WwGallery\FilamentGallery\Filament\Resources\MediaSources\Pages\EditMediaSource;
use WwGallery\FilamentGallery\Filament\Resources\MediaSources\Pages\ListMediaSources;
use WwGallery\FilamentGallery\Filament\Resources\MediaSources\RelationManagers\SettingsRelationManager;
use WwGallery\FilamentGallery\Filament\Resources\MediaSources\Schemas\MediaSourceForm;
use WwGallery\FilamentGallery\Filament\Resources\MediaSources\Tables\MediaSourcesTable;
use WwGallery\FilamentGallery\Models\MediaSource;
use UnitEnum;

final class MediaSourceResource extends Resource
{
    protected static ?string $model = MediaSource::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('filament-gallery::filament-gallery.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-gallery::filament-gallery.navigation.media_sources');
    }

    public static function getModelLabel(): string
    {
        return __('filament-gallery::filament-gallery.resource.media_source.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-gallery::filament-gallery.resource.media_source.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return MediaSourceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MediaSourcesTable::configure($table);
    }

    /**
     * @return array<class-string>
     */
    public static function getRelations(): array
    {
        return [
            SettingsRelationManager::class,
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListMediaSources::route('/'),
            'create' => CreateMediaSource::route('/create'),
            'edit' => EditMediaSource::route('/{record}/edit'),
        ];
    }
}
