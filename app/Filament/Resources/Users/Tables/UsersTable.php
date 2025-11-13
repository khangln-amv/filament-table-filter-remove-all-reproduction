<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                          ->searchable(),
                TextColumn::make('email')
                          ->label('Email address')
                          ->searchable(),
                TextColumn::make('email_verified_at')
                          ->dateTime()
                          ->sortable(),
                TextColumn::make('created_at')
                          ->dateTime()
                          ->sortable()
                          ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                          ->dateTime()
                          ->sortable()
                          ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('email_verified_at')->toggle()->label('Email verified')->default(),
                SelectFilter::make('email')
                    ->options([
                        'filamentphp.com' => 'from filament',
                        'others'          => 'others',
                    ])
                    ->default('filamentphp.com')
                    ->selectablePlaceholder(false)
                    ->query(fn(Builder $query, array $data) => match($data['value']) {
                        'filamentphp.com' => $query->whereLike('email', '%filamentphp.com'),
                        'others'          => $query->whereNotLike('email', '%filamentphp.com'),
                        default           => $query,
                    })
                    ->indicateUsing(fn(array $data) => match($data['value']) {
                        'filamentphp.com' => Indicator::make('From Filament Only')->removable(false),
                        'others'          => Indicator::make('Not From Filament')->removable(false),
                        default           => null,
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
