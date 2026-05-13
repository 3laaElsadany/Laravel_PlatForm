<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('avatar_path')
                    ->label('Profile photo')
                    ->image()
                    ->avatar()
                    ->disk('public')
                    ->directory('avatars')
                    ->imageEditor()
                    ->nullable(),
                TextInput::make('fullname')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')->maxLength(50),
                TextInput::make('country')->maxLength(100),
                TextInput::make('language')->maxLength(50),
                Select::make('gender')
                    ->options([
                        'female' => 'Female',
                        'male' => 'Male',
                        'other' => 'Other',
                    ])
                    ->native(false),
                Select::make('role')
                    ->options([
                        User::ROLE_STUDENT => 'Student',
                        User::ROLE_TEACHER => 'Instructor',
                        User::ROLE_ADMIN => 'Admin',
                    ])
                    ->required()
                    ->native(false),
                Toggle::make('isVerified')
                    ->label('Email verified (OTP completed)'),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->minLength(8)
                    ->maxLength(255)
                    ->helperText('Leave blank when editing to keep the current password.'),
            ]);
    }
}
