<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar_path')
                    ->circular()
                    ->imageHeight(40)
                    ->label('')
                    ->toggleable()
                    ->getStateUsing(function (User $record): ?string {
                        $path = trim((string) $record->getAttribute('avatar_path'));
                        if ($path === '') {
                            return null;
                        }

                        $relative = ltrim($path, '/');
                        if (str_starts_with($relative, 'storage/')) {
                            $relative = substr($relative, strlen('storage/'));
                        }

                        if (! Storage::disk('public')->exists($relative)) {
                            return null;
                        }

                        $base = request()->getSchemeAndHttpHost();
                        if ($base === '') {
                            $base = rtrim((string) config('app.url'), '/');
                        }

                        return $base.'/storage/'.$relative;
                    })
                    ->defaultImageUrl(fn (User $record): string => 'https://ui-avatars.com/api/?background=6366f1&color=ffffff&name='.urlencode($record->fullname ?: $record->email ?: 'User')),
                TextColumn::make('fullname')
                    ->searchable(['fullname', 'phone', 'country'])
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('role')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('country')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('isVerified')
                    ->boolean()
                    ->label('Verified')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
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
                // EditAction::make()->slideOver(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
