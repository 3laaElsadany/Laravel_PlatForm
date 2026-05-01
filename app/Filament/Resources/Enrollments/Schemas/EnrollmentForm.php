<?php

namespace App\Filament\Resources\Enrollments\Schemas;

use App\Support\FilamentInstructor;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class EnrollmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('payment_id')
                    ->relationship('payment', 'reference', fn (Builder $q) => FilamentInstructor::limitPaymentsForInstructor($q))
                    ->searchable()
                    ->preload(),
                Select::make('user_id')
                    ->relationship('user', 'fullname')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('course_id')
                    ->relationship('course', 'title', fn (Builder $q) => FilamentInstructor::limitCoursesQuery($q))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('discount_code_id')
                    ->relationship('discountCode', 'code', fn (Builder $q) => FilamentInstructor::limitDiscountCodesForInstructor($q))
                    ->searchable()
                    ->preload(),
                TextInput::make('final_price')
                    ->numeric()
                    ->prefix('$'),
                DateTimePicker::make('enrolled_at')
                    ->native(false)
                    ->required(),
            ]);
    }
}
