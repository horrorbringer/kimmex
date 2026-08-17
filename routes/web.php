<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TestimonialController;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

// Health Check Endpoint
Route::get('/health', HealthCheckController::class)->name('health');

// Language Switcher
Route::get('/lang/{locale}', function (string $locale) {
    $normalizedLocale = $locale === 'kh' ? 'km' : $locale;

    if (in_array($normalizedLocale, ['en', 'km'])) {
        session(['locale' => $normalizedLocale]);
        app()->setLocale($normalizedLocale);
    }

    return redirect()->back();
})->name('lang.switch');

Route::post('/contact', [FormController::class, 'submitContact'])->name('contact.submit')->middleware('throttle:forms');
Route::post('/careers/apply', [FormController::class, 'submitApplication'])->name('careers.apply')->middleware('throttle:forms');
Route::get('/media/{path}', [MediaController::class, 'show'])
    ->where('path', '.*')
    ->name('media.show');

Route::get('/sw.js', function () {
    $path = public_path('build/sw.js');
    if (! file_exists($path)) {
        return response('', 404);
    }

    $worker = str_replace(
        ['./workbox-', 'url:"assets/', 'url:"manifest.webmanifest"'],
        ['/build/workbox-', 'url:"/build/assets/', 'url:"/build/manifest.webmanifest"'],
        file_get_contents($path),
    );

    return response($worker, 200, [
        'Content-Type' => 'application/javascript',
        'Service-Worker-Allowed' => '/',
        'Cache-Control' => 'no-cache',
    ]);
})->name('pwa.sw');

Route::get('/manifest.json', function () {
    $path = public_path('build/manifest.webmanifest');
    if (! file_exists($path)) {
        return response('', 404);
    }

    return response()->file($path, [
        'Content-Type' => 'application/manifest+json',
        'Cache-Control' => 'public, max-age=3600',
    ]);
})->name('pwa.manifest');

Route::get('/robots.txt', function () {
    $path = public_path('robots.txt');

    if (file_exists($path)) {
        return response(file_get_contents($path), 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    return response(implode("\n", [
        'User-agent: *',
        'Allow: /',
        'Disallow: /admin',
        'Sitemap: '.url('/sitemap.xml'),
        '',
    ]), 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
})->name('robots');

// HTML Sitemap (Human-Readable)
Route::get('/sitemap', [SitemapController::class, 'index'])->name('sitemap');

// XML Sitemap (Search Engines)
Route::get('/sitemap.xml', [SitemapController::class, 'xml'])->name('sitemap.xml');

// Home Page
Route::get('/', [HomeController::class, 'index'])->name('home');

// About Page
Route::get('/about', [AboutController::class, 'index'])->name('about');

// Careers Page
Route::get('/careers', [CareerController::class, 'index'])->name('careers');

Route::get('/careers/{slug}', [CareerController::class, 'show'])->name('careers.show');

// Services Archive
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');

Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');

// Projects Archive & Single
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');

Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');

// News Archive & Single
Route::get('/news', [NewsController::class, 'index'])->name('news.index');

Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');

// Contact Page
Route::get('/contact', [FormController::class, 'showContact'])->name('contact');

// Documents Page
Route::get('/documents', [DocumentController::class, 'index'])->name('documents');

Route::get('/documents/{slug}', [DocumentController::class, 'show'])->name('documents.show');

// Privacy Policy
Route::get('/privacy-policy', function () {
    return view('pages.privacy');
})->name('privacy');

// Admin: Download Database Backup
Route::get('/admin/backup/download/{filename}', function (string $filename) {
    // Validate filename format to prevent directory traversal
    if (! preg_match('/^kimmex_backup_\d{4}-\d{2}-\d{2}_\d{6}\.sql(\.gz)?$/', $filename)) {
        abort(404);
    }

    $filepath = storage_path('app/backups/'.$filename);

    if (! file_exists($filepath)) {
        abort(404);
    }

    $contentType = str_ends_with($filename, '.gz')
        ? 'application/gzip'
        : 'application/sql';

    return response()->download($filepath, $filename, [
        'Content-Type' => $contentType,
    ]);
})->middleware(['web', Authenticate::class])->name('admin.backup.download');

// Testimonial Submission (signed URL protected)
Route::get('/testimonials/submit', [TestimonialController::class, 'showSubmitForm'])->name('testimonials.submit');
Route::post('/testimonials/submit', [TestimonialController::class, 'store'])->name('testimonials.store');

// Newsletter Unsubscribe
Route::get('/unsubscribe/{token}', [FormController::class, 'unsubscribe'])->name('unsubscribe');
