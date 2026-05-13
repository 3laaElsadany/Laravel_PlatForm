<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory;

    public $timestamps = false;

    const CREATED_AT = 'enrolled_at';

    protected $fillable = [
        'payment_id',
        'user_id',
        'course_id',
        'enrolled_at',
        'final_price',
        'trial_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
            'final_price' => 'decimal:2',
            'trial_expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function isTrial(): bool
    {
        return $this->trial_expires_at !== null;
    }

    public function isTrialActive(): bool
    {
        return $this->isTrial() && $this->trial_expires_at->isFuture();
    }

    public function isTrialExpired(): bool
    {
        return $this->isTrial() && $this->trial_expires_at->isPast();
    }
}
