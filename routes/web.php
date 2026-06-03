<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
use App\Http\Controllers\MediaController;
use App\Models\Document;
use App\Models\JobPosting;
use App\Models\NewsArticle;
use App\Models\Project;
use App\Models\Service;

// Language Switcher
Route::get('/lang/{locale}', function (string $locale) {
    $normalizedLocale = $locale === 'kh' ? 'km' : $locale;

    if (in_array($normalizedLocale, ['en', 'km'])) {
        session(['locale' => $normalizedLocale]);
        app()->setLocale($normalizedLocale);
    }
    return redirect()->back();
})->name('lang.switch');

Route::post('/contact', [FormController::class, 'submitContact'])->name('contact.submit')->middleware('throttle:5,1');
Route::post('/careers/apply', [FormController::class, 'submitApplication'])->name('careers.apply')->middleware('throttle:5,1');
Route::get('/media/{path}', [MediaController::class, 'show'])
    ->where('path', '.*')
    ->name('media.show');

Route::get('/robots.txt', function () {
    return response(implode("\n", [
        'User-agent: *',
        'Disallow:',
        'Sitemap: ' . url('/sitemap.xml'),
        '',
    ]), 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
})->name('robots');

Route::get('/sitemap.xml', function () {
    $urls = collect();
    $add = function (string $loc, ?\Carbon\CarbonInterface $lastmod = null, string $changefreq = 'monthly', string $priority = '0.7') use ($urls) {
        $urls->push([
            'loc' => url($loc),
            'lastmod' => $lastmod?->toAtomString(),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ]);
    };

    $add('/', null, 'weekly', '1.0');
    $add('/about', null, 'monthly', '0.8');
    $add('/services', Service::where('isActive', true)->max('updated_at') ? \Carbon\Carbon::parse(Service::where('isActive', true)->max('updated_at')) : null, 'weekly', '0.9');
    $add('/projects', Project::where('isActive', true)->max('updated_at') ? \Carbon\Carbon::parse(Project::where('isActive', true)->max('updated_at')) : null, 'weekly', '0.9');
    $add('/news', NewsArticle::where('isActive', true)->where('publishedAt', '<=', now())->max('updated_at') ? \Carbon\Carbon::parse(NewsArticle::where('isActive', true)->where('publishedAt', '<=', now())->max('updated_at')) : null, 'weekly', '0.8');
    $add('/careers', JobPosting::where('isActive', true)->max('updated_at') ? \Carbon\Carbon::parse(JobPosting::where('isActive', true)->max('updated_at')) : null, 'weekly', '0.7');
    $add('/contact', null, 'monthly', '0.7');

    if (Document::publicDocumentsExist()) {
        $add('/documents', Document::publiclyVisible()->max('updated_at') ? \Carbon\Carbon::parse(Document::publiclyVisible()->max('updated_at')) : null, 'weekly', '0.7');
    }

    Service::where('isActive', true)->select('slug', 'updated_at')->orderBy('orderIndex')->get()
        ->each(fn (Service $service) => $add(route('services.show', ['slug' => $service->slug], false), $service->updated_at, 'monthly', '0.8'));

    Project::where('isActive', true)->select('slug', 'updated_at')->latest('updated_at')->get()
        ->each(fn (Project $project) => $add(route('projects.show', ['slug' => $project->slug], false), $project->updated_at, 'monthly', '0.8'));

    NewsArticle::where('isActive', true)->where('publishedAt', '<=', now())->select('slug', 'updated_at')->orderByDesc('publishedAt')->get()
        ->each(fn (NewsArticle $article) => $add(route('news.show', ['slug' => $article->slug], false), $article->updated_at, 'weekly', '0.7'));

    Document::publiclyVisible()->select('slug', 'updated_at')->latest('updated_at')->get()
        ->each(fn (Document $document) => $add(route('documents.show', ['slug' => $document->slug], false), $document->updated_at, 'monthly', '0.6'));

    JobPosting::where('isActive', true)->select('slug', 'updated_at')->latest('updated_at')->get()
        ->each(fn (JobPosting $job) => $add(route('careers.show', ['slug' => $job->slug], false), $job->updated_at, 'weekly', '0.6'));

    return response()
        ->view('sitemap', ['urls' => $urls], 200)
        ->header('Content-Type', 'application/xml; charset=UTF-8');
})->name('sitemap');

// Home Page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// About Page
Route::get('/about', function () {
    return view('pages.about');
})->name('about');

// Careers Page
Route::get('/careers', function () {
    return view('pages.careers');
})->name('careers');

Route::get('/careers/{slug}', function ($slug) {
    return view('pages.careers.show', ['slug' => $slug]);
})->name('careers.show');

// Services Archive
Route::get('/services', function () {
    return view('pages.services');
})->name('services.index');

Route::get('/services/{slug}', function ($slug) {
    return view('pages.services.show', ['slug' => $slug]);
})->name('services.show');

// Projects Archive & Single
Route::get('/projects', function () {
    return view('pages.projects.index');
})->name('projects.index');

Route::get('/projects/{slug}', function ($slug) {
    return view('pages.projects.show', ['slug' => $slug]);
})->name('projects.show');

// News Archive & Single
Route::get('/news', function () {
    return view('pages.news.index');
})->name('news.index');

Route::get('/news/{slug}', function ($slug) {
    return view('pages.news.show', ['slug' => $slug]);
})->name('news.show');

// Contact Page
Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

// Documents Page
Route::get('/documents', function () {
    abort_unless(Document::publicDocumentsExist(), 404);

    return view('pages.documents');
})->name('documents');

Route::get('/documents/{slug}', function ($slug) {
    return view('pages.documents.show', ['slug' => $slug]);
})->name('documents.show');

// Privacy Policy
Route::get('/privacy-policy', function () {
    return view('pages.privacy');
})->name('privacy');
