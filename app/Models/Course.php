<?php

namespace App\Models;

use App\Services\CoursePricingService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    public $timestamps = false;

    const CREATED_AT = 'created_at';

    protected $fillable = [
        'title',
        'description',
        'price',
        'discount',
        'category_id',
        'rate',
        'catalog_rate',
        'course_includes',
        'video_img_link',
        'video_link',
        'img_link',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount' => 'float',
            'rate' => 'float',
            'catalog_rate' => 'float',
            'course_includes' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Course $course): void {
            if ($course->catalog_rate === null && $course->rate !== null) {
                $course->catalog_rate = (float) $course->rate;
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function courseRatings(): HasMany
    {
        return $this->hasMany(CourseRating::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function discountCodes(): HasMany
    {
        return $this->hasMany(DiscountCode::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function priceAfterCatalogDiscount(): float
    {
        return app(CoursePricingService::class)->priceAfterCatalogDiscount($this);
    }
}
