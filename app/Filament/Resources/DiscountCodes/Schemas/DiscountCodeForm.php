<?php

namespace App\Filament\Resources\DiscountCodes\Schemas;

use App\Support\FilamentInstructor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class DiscountCodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('course_id')
                    ->relationship('course', 'title', fn (Builder $q) => FilamentInstructor::limitCoursesQuery($q))
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('code')
                    ->required()
                    ->maxLength(64)
                    ->helperText('Must be unique for the selected course (case-insensitive on the site).'),
                Select::make('type')
                    ->options([
                        'percent' => 'Percent off remaining price',
                        'fixed' => 'Fixed amount off remaining price',
                    ])
                    ->required()
                    ->native(false),
                TextInput::make('value')
                    ->numeric()
                    ->required()
                    ->helperText('Percent (0–100) or fixed USD depending on type.'),
                Toggle::make('is_active')->default(true),
                DateTimePicker::make('expires_at')->native(false),
            ]);
    }
}
