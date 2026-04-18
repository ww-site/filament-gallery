<?php

declare(strict_types=1);

namespace WwGallery\FilamentGallery\Filament\Resources\MediaSources\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

final class MediaSourcesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('filament-gallery::filament-gallery.resource.media_source.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label(__('filament-gallery::filament-gallery.resource.media_source.fields.slug'))
                    ->searchable()
                    ->copyable(),
                TextColumn::make('description')
                    ->label(__('filament-gallery::filament-gallery.resource.media_source.fields.description'))
                    ->limit(80)
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label(__('filament-gallery::filament-gallery.resource.media_source.fields.is_active'))
                    ->boolean(),
                TextColumn::make('settings_count')
                    ->label(__('filament-gallery::filament-gallery.resource.media_source.fields.settings_count'))
                    ->counts('settings'),
                TextColumn::make('updated_at')
                    ->label(__('filament-gallery::filament-gallery.resource.media_source.fields.updated_at'))
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('id', 'desc');
    }
}
