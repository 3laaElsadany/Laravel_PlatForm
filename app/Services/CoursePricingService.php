<?php

namespace App\Services;

use App\Models\Course;

class CoursePricingService
{
    public function priceAfterCatalogDiscount(Course $course): float
    {
        $base = (float) $course->price;
        $pct = max(0.0, min(100.0, (float) $course->discount));

        return round($base * (1 - ($pct / 100)), 2);
    }
}
