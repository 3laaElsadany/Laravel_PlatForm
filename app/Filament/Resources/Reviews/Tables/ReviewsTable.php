<?php

namespace App\Filament\Resources\Reviews\Tables;

use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course.title')
                    ->searchable(query: function ($query, string $search): void {
                        $query->whereHas('course', fn ($q) => $q->where('title', 'like', '%'.$search.'%'));
                    })
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('user.fullname')
                    ->label('Student')
                    ->searchable(query: function ($query, string $search): void {
                        $query->whereHas('user', fn ($q) => $q->where('fullname', 'like', '%'.$search.'%'));
                    })
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('description')
                    ->limit(80)
                    ->wrap()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()->slideOver(),
            ])
            ->recordActions([
                ViewAction::make()->slideOver(),
            ]);
    }
}
