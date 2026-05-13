<?php

namespace App\Filament\Resources\Courses\Schemas;

use App\Models\User;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('instructor_id')
                    ->default(fn (): ?int => auth()->id())
                    ->visible(fn (): bool => auth()->user()?->isTeacher() ?? false)
                    ->dehydrated(fn (): bool => auth()->user()?->isTeacher() ?? false),
                Select::make('instructor_id')
                    ->relationship(
                        'instructor',
                        'fullname',
                        fn (Builder $query) => $query->whereIn('role', [User::ROLE_ADMIN, User::ROLE_TEACHER])
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
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
