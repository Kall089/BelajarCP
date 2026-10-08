<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LearnController;
use App\Http\Controllers\ProblemController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [LearnController::class, 'home'])->name('home');
    Route::get('/materi/{lesson}', [LearnController::class, 'lesson'])->name('lessons.show');
    Route::post('/materi/{lesson}/selesai', [LearnController::class, 'complete'])->name('lessons.complete');

    Route::get('/soal', [ProblemController::class, 'index'])->name('problems.index');
    Route::get('/soal/{problem}', [ProblemController::class, 'show'])->name('problems.show');
    Route::get('/soal/{problem}/pembahasan', [ProblemController::class, 'editorial'])->name('problems.editorial');
    Route::get('/soal/{problem}/tes', [ProblemController::class, 'tests'])->name('problems.tests');
    Route::post('/soal/{problem}/jalankan', [ProblemController::class, 'run'])->name('problems.run')->middleware('throttle:40,1');
    Route::post('/soal/{problem}/submit', [ProblemController::class, 'submit'])->name('problems.submit')->middleware('throttle:30,1');
    Route::get('/submisi/{submission}', [ProblemController::class, 'submission'])->name('submissions.show');

    Route::get('/riwayat', [ProblemController::class, 'history'])->name('history');
    Route::get('/peringkat', [ProblemController::class, 'leaderboard'])->name('leaderboard');
});
