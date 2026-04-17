<?php

namespace App\Filament\Resources\Enrollments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EnrollmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.fullname')
                    ->label('Student')
                    ->searchable(query: function ($query, string $search): void {
                        $query->whereHas('user', function ($q) use ($search): void {
                            $q->where(function ($q2) use ($search): void {
                                $q2->where('fullname', 'like', '%'.$search.'%')
                                    ->orWhere('email', 'like', '%'.$search.'%');
                            });
                        });
                    })
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('course.title')
                    ->searchable(query: function ($query, string $search): void {
                        $query->whereHas('course', fn ($q) => $q->where('title', 'like', '%'.$search.'%'));
                    })
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('final_price')
                    ->money('usd')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('enrolled_at')
                    ->dateTime()
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('payment.reference')
                    ->label('Payment')
                    ->searchable(query: function ($query, string $search): void {
                        $query->whereHas('payment', fn ($q) => $q->where('reference', 'like', '%'.$search.'%'));
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('discountCode.code')
                    ->label('Discount code')
                    ->searchable(query: function ($query, string $search): void {
                        $query->whereHas('discountCode', fn ($q) => $q->where('code', 'like', '%'.$search.'%'));
                    })
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
                EditAction::make()->slideOver(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
