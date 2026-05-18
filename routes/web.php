<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\Admin\JobApplicationController as AdminJobApplicationController;
use App\Http\Controllers\AppliedJobsController;
use App\Http\Controllers\SavedJobsController;
use App\Http\Controllers\UserJobController;
use App\Http\Controllers\UserNotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public routes
|--------------------------------------------------------------------------
*/

Route::get('/', [PageController::class, 'landing'])->name('landing');
Route::get('/sitemap', [PageController::class, 'sitemap'])->name('sitemap');

Route::prefix('suite')->name('suite.')->group(function () {
    Route::get('/1', [PageController::class, 'executiveSuiteOne'])->name('one');
    Route::get('/2', [PageController::class, 'executiveSuiteTwo'])->name('two');
});

/*
|--------------------------------------------------------------------------
| Guest auth routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Authenticated routes
|--------------------------------------------------------------------------
*/

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'user'])->name('dashboard');
    Route::get('/jobs/recommendations', [UserJobController::class, 'recommendations'])->name('jobs.recommendations');
    Route::get('/jobs/{job}', [UserJobController::class, 'show'])->name('jobs.show');
    Route::post('/jobs/{job}/apply', [UserJobController::class, 'apply'])->name('jobs.apply');
    Route::post('/jobs/{job}/save', [UserJobController::class, 'saveJob'])->name('jobs.save');
    Route::get('/resume/upload', [ResumeController::class, 'show'])->name('resume.upload');
    Route::post('/resume/upload', [ResumeController::class, 'upload'])->name('resume.upload.store');
    Route::get('/resume/parse/{log}', [ResumeController::class, 'status'])->name('resume.parse.status');
    Route::get('/resume/preview/{log}', [ResumeController::class, 'preview'])->name('resume.preview');
    Route::post('/resume/profile', [ResumeController::class, 'storeProfile'])->name('resume.profile.store');
    Route::get('/resume/analytics', [PageController::class, 'resumeAnalytics'])->name('resume.analytics');
    Route::get('/settings/notifications', [PageController::class, 'emailNotificationSettings'])->name('settings.notifications');
    Route::get('/saved-jobs', [SavedJobsController::class, 'index'])->name('saved-jobs');
    Route::delete('/saved-jobs/{job}', [SavedJobsController::class, 'destroy'])->name('saved-jobs.destroy');
    Route::get('/applied-jobs', [AppliedJobsController::class, 'index'])->name('applied-jobs');
    Route::get('/notifications', [UserNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read', [UserNotificationController::class, 'markRead'])->name('notifications.read');
});

Route::middleware(['auth', 'role:hr'])->prefix('hr')->name('hr.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'hr'])->name('dashboard');
    Route::get('/jobs/create', [JobController::class, 'create'])->name('jobs.create');
    Route::post('/jobs', [JobController::class, 'store'])->name('jobs.store');
    Route::get('/jobs/{job}/edit', [JobController::class, 'edit'])->name('jobs.edit');
    Route::put('/jobs/{job}', [JobController::class, 'update'])->name('jobs.update');
    Route::delete('/jobs/{job}', [JobController::class, 'destroy'])->name('jobs.destroy');
    Route::patch('/jobs/{job}/status', [JobController::class, 'toggleStatus'])->name('jobs.toggle-status');
    Route::get('/applicants', [PageController::class, 'applicantManagement'])->name('applicants');
    Route::get('/resume/upload', [ResumeController::class, 'show'])->name('resume.upload');
    Route::post('/resume/upload', [ResumeController::class, 'upload'])->name('resume.upload.store');
    Route::get('/resume/parse/{log}', [ResumeController::class, 'status'])->name('resume.parse.status');
    Route::get('/resume/preview/{log}', [ResumeController::class, 'preview'])->name('resume.preview');
    Route::post('/resume/profile', [ResumeController::class, 'storeProfile'])->name('resume.profile.store');
    Route::get('/settings/notifications', [PageController::class, 'emailNotificationSettings'])->name('settings.notifications');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::get('/analytics', [PageController::class, 'adminAnalytics'])->name('analytics');
    Route::get('/job-applications', [AdminJobApplicationController::class, 'index'])->name('job-applications.index');
    Route::get('/job-applications/{application}', [AdminJobApplicationController::class, 'show'])->name('job-applications.show');
    Route::post('/job-applications/{application}/status', [AdminJobApplicationController::class, 'updateStatus'])->name('job-applications.status');
    Route::get('/job-applications/{application}/resume', [AdminJobApplicationController::class, 'downloadResume'])->name('job-applications.resume');
});

/*
|--------------------------------------------------------------------------
| Legacy redirects (old public URLs → auth-protected paths)
|--------------------------------------------------------------------------
*/

Route::redirect('/login-old', '/login');
Route::redirect('/candidate/dashboard', '/user/dashboard');
Route::redirect('/employer/dashboard', '/hr/dashboard');
Route::redirect('/employer/jobs/create', '/hr/jobs/create');
Route::redirect('/employer/applicants', '/hr/applicants');
Route::redirect('/jobs/recommendations', '/user/jobs/recommendations');
Route::get('/jobs/{id}', fn (string $id) => redirect("/user/jobs/{$id}"))->where('id', '[0-9]+');
Route::redirect('/resume/upload', '/user/resume/upload');
Route::redirect('/resume/analytics', '/user/resume/analytics');
Route::redirect('/settings/notifications', '/login');
