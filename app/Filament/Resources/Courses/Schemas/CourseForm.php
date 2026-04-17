<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->rows(6)
                    ->columnSpanFull(),
                Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->preload(),
                TextInput::make('price')
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                TextInput::make('discount')
                    ->numeric()
                    ->default(0)
                    ->suffix('%')
                    ->helperText('Catalog discount percentage (0–100).'),
                TextInput::make('rate')
                    ->numeric()
                    ->default(0)
                    ->helperText('Catalog rating (0–5). Subscriber averages update the stored rating on the public site; the original baseline is kept internally for when there are no ratings.'),
                TagsInput::make('course_includes')
                    ->placeholder('Add a bullet')
                    ->helperText('Each tag becomes one line in “course includes”.'),
                TextInput::make('img_link')
                    ->label('Image URL')
                    ->url()
                    ->maxLength(2048),
                TextInput::make('video_img_link')
                    ->label('Video poster URL')
                    ->url()
                    ->maxLength(2048),
                TextInput::make('video_link')
                    ->label('Video embed URL')
                    ->maxLength(2048)
                    ->helperText('YouTube embed or direct video URL.'),
            ]);
    }
}
