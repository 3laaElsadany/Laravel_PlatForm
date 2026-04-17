<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Model implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, FilamentUser, HasName
{
    /** @use HasFactory<UserFactory> */
    use Authenticatable, Authorizable, CanResetPassword, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_STUDENT = 'student';

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'fullname',
        'email',
        'country',
        'phone',
        'gender',
        'language',
        'avatar_path',
        'password',
        'role',
        'isVerified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'isVerified' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function getFilamentName(): string
    {
        $name = trim((string) ($this->getAttribute('fullname')));
        if ($name !== '') {
            return $name;
        }

        $email = trim((string) ($this->getAttribute('email')));
        if ($email !== '') {
            return $email;
        }

        return 'User';
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function courseRatings(): HasMany
    {
        return $this->hasMany(CourseRating::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function getAvatarUrlAttribute(): ?string
    {
        $path = trim((string) $this->avatar_path);

        if ($path === '') {
            return null;
        }

        return asset('storage/'.ltrim($path, '/'));
    }

    public function getInitialsAttribute(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->fullname)) ?: [];
        $letters = '';

        foreach (array_slice($parts, 0, 2) as $part) {
            $letters .= mb_substr((string) $part, 0, 1, 'UTF-8');
        }

        $letters = mb_strtoupper($letters, 'UTF-8');

        return $letters !== '' ? $letters : 'U';
    }
}
