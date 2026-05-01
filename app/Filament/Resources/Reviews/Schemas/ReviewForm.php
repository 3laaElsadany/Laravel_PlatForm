<?php

namespace App\Filament\Resources\Reviews\Schemas;

use App\Support\FilamentInstructor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ReviewForm
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
                Select::make('user_id')
                    ->relationship('user', 'fullname')
                    ->searchable()
                    ->preload()
                    ->required(),
                Textarea::make('description')
                    ->rows(6)
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
