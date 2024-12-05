<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/jobs', function () {
    return view('jobs');
})->middleware(['auth', 'verified'])->name('jobs');

Route::get('/chains', function () {
    return view('chains');
})->middleware(['auth', 'verified'])->name('chain');


Route::get('/batches', function () {
    return view('batches');
})->middleware(['auth', 'verified'])->name('batches');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
