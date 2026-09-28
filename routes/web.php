<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\JobController;
use App\Http\Controllers\Admin\NavigationController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\HomeController;
use App\Models\JobPosting;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/8thpass', [HomeController::class, 'category'])->name('8thpass');
Route::get('/10thpass', [HomeController::class, 'category'])->name('10thpass');
Route::get('/12thpass', [HomeController::class, 'category'])->name('12thpass');
Route::get('/govt-jobs', [HomeController::class, 'category'])->name('govt-jobs');

Route::get('/job-details', function () {
    $job = JobPosting::query()
        ->where('is_published', true)
        ->when(request('job'), fn ($query) => $query->where('slug', request('job')))
        ->latest('published_at')
        ->first();

    if (! $job) {
        abort(404);
    }

    $relatedJobs = JobPosting::query()
        ->where('is_published', true)
        ->where('id', '!=', $job->id)
        ->orderByDesc('published_at')
        ->take(3)
        ->get();

    $latestJobs = JobPosting::query()
        ->where('is_published', true)
        ->where('id', '!=', $job->id)
        ->orderByDesc('published_at')
        ->take(5)
        ->get();

    return view('frontend.pages.job-details', [
        'job' => $job,
        'relatedJobs' => $relatedJobs,
        'latestJobs' => $latestJobs,
    ]);
})->name('job-details');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest:admin')->group(function (): void {
        Route::get('/login', [AuthController::class, 'create'])->name('login');
        Route::post('/login', [AuthController::class, 'store'])->name('login.store');
    });

    Route::middleware('admin')->group(function (): void {
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::resource('/navigation', NavigationController::class)->except(['create', 'show', 'edit']);
        Route::resource('/categories', CategoryController::class)->except(['create', 'show', 'edit']);
        Route::resource('/jobs', JobController::class)->except(['show']);
    });
});
