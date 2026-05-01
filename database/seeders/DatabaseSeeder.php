<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\DiscountCode;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Review;
use App\Models\User;
use App\Services\CoursePricingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $pricing = app(CoursePricingService::class);

        $admin = User::factory()->admin()->create([
            'fullname' => 'أحمد المدرب — Instructor',
            'email' => 'admin@example.com',
            'isVerified' => true,
        ]);

        $students = collect([
            ['fullname' => 'سارة محمود', 'email' => 'student@example.com'],
            ['fullname' => 'Karim Adel', 'email' => 'karim@example.com'],
            ['fullname' => 'Nour Hassan', 'email' => 'nour@example.com'],
            ['fullname' => 'Omar Ali', 'email' => 'omar@example.com'],
            ['fullname' => 'Layla Ibrahim', 'email' => 'layla@example.com'],
        ])->map(fn (array $row) => User::factory()->create([
            'fullname' => $row['fullname'],
            'email' => $row['email'],
            'isVerified' => true,
            'country' => 'Egypt',
            'language' => 'ar',
        ]));

        $mainStudent = $students->first();

        $categories = [
            ['name' => 'البرمجة', 'description' => 'PHP، Laravel، JavaScript، وأطر العمل الحديثة.'],
            ['name' => 'الذكاء الاصطناعي', 'description' => 'تعلم الآلة، النماذج اللغوية، وتحليل البيانات.'],
            ['name' => 'التصميم', 'description' => 'واجهات المستخدم، تجربة المستخدم، والهوية البصرية.'],
            ['name' => 'البيانات والتحليل', 'description' => 'SQL، التصور، واتخاذ القرار بالأرقام.'],
            ['name' => 'السحابة والـ DevOps', 'description' => 'نشر التطبيقات، Docker، والمراقبة.'],
        ];

        $categoryModels = collect($categories)->map(fn ($c) => Category::create($c));

        $courseDefinitions = [
            // Programming (0)
            ['title' => 'Laravel 12 من الصفر للإنتاج', 'cat' => 0, 'price' => 129.99, 'discount' => 20, 'rate' => 4.9],
            ['title' => 'Vue 3 + Composition API', 'cat' => 0, 'price' => 79.00, 'discount' => 0, 'rate' => 4.7],
            ['title' => 'REST APIs مع Laravel', 'cat' => 0, 'price' => 69.50, 'discount' => 10, 'rate' => 4.6],
            ['title' => 'Git & GitHub للمبتدئين', 'cat' => 0, 'price' => 29.00, 'discount' => 0, 'rate' => 4.8],
            // AI (1)
            ['title' => 'مقدمة في تعلم الآلة', 'cat' => 1, 'price' => 99.00, 'discount' => 15, 'rate' => 4.8],
            ['title' => 'Prompt Engineering عملي', 'cat' => 1, 'price' => 49.00, 'discount' => 0, 'rate' => 4.9],
            ['title' => 'Python للتحليل والذكاء الاصطناعي', 'cat' => 1, 'price' => 89.00, 'discount' => 12, 'rate' => 4.7],
            // Design (2)
            ['title' => 'Figma: من الويرفريم للتسليم', 'cat' => 2, 'price' => 74.00, 'discount' => 5, 'rate' => 4.6],
            ['title' => 'تصميم أنظمة الألوان والطباعة', 'cat' => 2, 'price' => 54.00, 'discount' => 0, 'rate' => 4.5],
            // Data (3)
            ['title' => 'SQL للمحللين', 'cat' => 3, 'price' => 59.00, 'discount' => 0, 'rate' => 4.7],
            ['title' => 'لوحات معلومات بـ Excel & Sheets', 'cat' => 3, 'price' => 39.00, 'discount' => 8, 'rate' => 4.4],
            // Cloud (4)
            ['title' => 'نشر Laravel على VPS', 'cat' => 4, 'price' => 64.00, 'discount' => 10, 'rate' => 4.6],
            ['title' => 'Docker للمطورين', 'cat' => 4, 'price' => 71.00, 'discount' => 0, 'rate' => 4.8],
        ];

        $reviewSamples = [
            'شرح ممتاز وتمارين عملية أنصح بها بشدة.',
            'Very clear structure, worth every dollar.',
            'المحتوى منظم والمشروع النهائي مفيد.',
            'Good pace for beginners. Loved the examples.',
            'دعم سريع من المدرب عند الأسئلة.',
            'Would like more advanced chapters, but solid overall.',
        ];

        $courses = collect();

        foreach ($courseDefinitions as $idx => $row) {
            $cat = $categoryModels[$row['cat']];
            $slug = Str::slug($row['title']).'-'.$idx;

            $course = Course::create([
                'title' => $row['title'],
                'description' => "دورة عملية تغطي المفاهيم الأساسية والمتقدمة مع مشروع تطبيقي في النهاية.\n\n"
                    ."Practical lessons, downloadable resources, and a certificate-friendly final project.\n\n"
                    .'مناسبة للمبتدئين والذين يريدون تعميق المهارات في بيئة واقعية.',
                'price' => $row['price'],
                'discount' => $row['discount'],
                'category_id' => $cat->id,
                'instructor_id' => $admin->id,
                'rate' => $row['rate'],
                'catalog_rate' => $row['rate'],
                'course_includes' => [
                    'أكثر من 12 ساعة فيديو عالي الجودة',
                    'ملفات ومشاريع للتحميل',
                    'اختبارات قصيرة بعد كل وحدة',
                    'وصول مدى الحياة (حسب سياسة المنصة)',
                    'تحديثات للمحتوى عند توفرها',
                ],
                'img_link' => 'https://picsum.photos/seed/'.$slug.'/960/540',
                'video_link' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
                'video_img_link' => null,
            ]);

            $courses->push($course);

            DiscountCode::create([
                'course_id' => $course->id,
                'code' => 'SAVE'.str_pad((string) ($course->id % 30), 2, '0', STR_PAD_LEFT),
                'type' => 'percent',
                'value' => min(20, 5 + ($course->id % 10)),
                'is_active' => true,
                'expires_at' => now()->addMonths(6),
            ]);

            if ($idx % 3 === 0) {
                DiscountCode::create([
                    'course_id' => $course->id,
                    'code' => 'FIXED'.$course->id,
                    'type' => 'fixed',
                    'value' => 10,
                    'is_active' => true,
                    'expires_at' => now()->addYear(),
                ]);
            }

            foreach ($students->shuffle()->take(3) as $r) {
                Review::create([
                    'description' => fake()->randomElement($reviewSamples),
                    'user_id' => $r->id,
                    'course_id' => $course->id,
                ]);
            }
        }

        $enrollmentPairs = [
            [$mainStudent, $courses[0]],
            [$mainStudent, $courses[1]],
            [$mainStudent, $courses[5]],
            [$students[1], $courses[0]],
            [$students[1], $courses[3]],
            [$students[2], $courses[2]],
            [$students[2], $courses[7]],
            [$students[3], $courses[10]],
            [$students[4], $courses[11]],
        ];

        foreach ($enrollmentPairs as [$user, $course]) {
            if (! $course) {
                continue;
            }

            if (Enrollment::query()->where('user_id', $user->id)->where('course_id', $course->id)->exists()) {
                continue;
            }

            $useCode = fake()->boolean(35);
            $code = $useCode
                ? DiscountCode::query()->where('course_id', $course->id)->where('type', 'percent')->first()
                : null;

            $final = $pricing->finalEnrollmentPrice($course, $code);

            $payment = Payment::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'discount_code_id' => $code?->id,
                'amount' => $final,
                'currency' => 'USD',
                'status' => 'completed',
                'gateway' => 'demo',
                'reference' => 'PAY-'.Str::upper(Str::random(12)),
                'paid_at' => now()->subDays(rand(1, 45)),
            ]);

            Enrollment::create([
                'payment_id' => $payment->id,
                'user_id' => $user->id,
                'course_id' => $course->id,
                'enrolled_at' => $payment->paid_at,
                'discount_code_id' => $code?->id,
                'final_price' => $final,
            ]);
        }
    }
}
