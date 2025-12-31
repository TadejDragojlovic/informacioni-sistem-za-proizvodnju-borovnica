<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::resource('proizvods', App\Http\Controllers\ProizvodController::class);

Route::resource('skladistes', App\Http\Controllers\SkladisteController::class);

Route::resource('resurs', App\Http\Controllers\ResursController::class);

Route::resource('narudzbinas', App\Http\Controllers\NarudzbinaController::class);
