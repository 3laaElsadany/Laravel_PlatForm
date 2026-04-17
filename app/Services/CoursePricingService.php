<?php

namespace App\Services;

use App\Models\Course;
use App\Models\DiscountCode;

class CoursePricingService
{
    public function priceAfterCatalogDiscount(Course $course): float
    {
        $base = (float) $course->price;
        $pct = max(0.0, min(100.0, (float) $course->discount));

        return round($base * (1 - ($pct / 100)), 2);
    }

    public function finalEnrollmentPrice(Course $course, ?DiscountCode $code = null): float
    {
        $amount = $this->priceAfterCatalogDiscount($course);

        if (! $code || ! $code->isUsable() || $code->course_id !== $course->id) {
            return round($amount, 2);
        }

        if ($code->type === 'fixed') {
            $amount = max(0, $amount - (float) $code->value);
        } else {
            $pct = max(0.0, min(100.0, (float) $code->value));
            $amount = $amount * (1 - ($pct / 100));
        }

        return round(max(0, $amount), 2);
    }
}
