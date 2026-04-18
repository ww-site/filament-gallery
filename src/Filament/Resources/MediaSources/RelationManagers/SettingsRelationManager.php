<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery\Filament\Resources\MediaSources\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class SettingsRelationManager extends RelationManager
{
    protected static string $relationship = 'settings';

    protected static ?string $title = 'Настройки';

    public static function getTitleForRecord(): string
    {
        return __('filament-gallery::filament-gallery.resource.media_source.settings.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->label(__('filament-gallery::filament-gallery.resource.media_source.settings.fields.key'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('value')
                    ->label(__('filament-gallery::filament-gallery.resource.media_source.settings.fields.value'))
                    ->maxLength(65535),
                TextInput::make('sort_order')
                    ->label(__('filament-gallery::filament-gallery.resource.media_source.settings.fields.sort_order'))
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('key')
            ->columns([
                TextColumn::make('key')
                    ->label(__('filament-gallery::filament-gallery.resource.media_source.settings.fields.key'))
                    ->searchable(),
                TextColumn::make('value')
                    ->label(__('filament-gallery::filament-gallery.resource.media_source.settings.fields.value'))
                    ->wrap(),
                TextColumn::make('sort_order')
                    ->label(__('filament-gallery::filament-gallery.resource.media_source.settings.fields.sort_order'))
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
