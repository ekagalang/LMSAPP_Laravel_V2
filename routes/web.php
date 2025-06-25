<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseController; // Tambahkan ini
use App\Http\Controllers\LessonController; // Tambahkan ini
use App\Http\Controllers\ContentController; // Tambahkan ini
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', \App\Http\Controllers\DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ROUTE UNTUK MANAJEMEN KURSUS, PELAJARAN, dan KONTEN
    Route::middleware(['can:manage-courses'])->group(function () {
        Route::resource('courses', CourseController::class);

        // Nested resources untuk pelajaran di bawah kursus
        Route::resource('courses.lessons', LessonController::class)->except(['index', 'show']); // Index dan Show akan ditangani di CourseController@show
        // Nested resources untuk konten di bawah pelajaran
        Route::resource('lessons.contents', ContentController::class)->except(['index', 'show']); // Index dan Show akan ditangani di LessonController@show

        Route::post('courses/{course}/enroll', [CourseController::class, 'enrollParticipant'])->name('courses.enroll');
        Route::delete('courses/{course}/unenroll/{user}', [CourseController::class, 'unenrollParticipant'])->name('courses.unenroll');
    });

    // Untuk peserta melihat kursus (sementara, akan diperbaiki nanti)
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    Route::get('/lessons/{lesson}/contents/{content}', [ContentController::class, 'show'])->name('contents.show');

    Route::post('contents/{content}/complete', [App\Http\Controllers\ProgressController::class, 'markContentAsCompleted'])->name('contents.complete');
    Route::post('lessons/{lesson}/complete', [App\Http\Controllers\ProgressController::class, 'markLessonAsCompleted'])->name('lessons.complete');
});


require __DIR__.'/auth.php';
