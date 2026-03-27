<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;

// Language Switcher
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'km'])) {
        session(['locale' => $locale]);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->name('lang.switch');

Route::post('/contact', [FormController::class, 'submitContact'])->name('contact.submit')->middleware('throttle:5,1');
Route::post('/careers/apply', [FormController::class, 'submitApplication'])->name('careers.apply')->middleware('throttle:5,1');

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

Route::get('/careers/{id}', function ($id) {
    return view('pages.careers.show', ['id' => $id]);
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
    return view('pages.documents');
})->name('documents');

Route::get('/documents/{slug}', function ($slug) {
    return view('pages.documents.show', ['slug' => $slug]);
})->name('documents.show');
