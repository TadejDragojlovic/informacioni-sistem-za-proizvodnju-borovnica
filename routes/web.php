<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProizvodController;
use App\Http\Controllers\SkladisteController;
use App\Http\Controllers\ResursController;
use App\Http\Controllers\NarudzbinaController;
use App\Http\Controllers\FinansijeController;


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// javne rute
Route::get('/', [ProizvodController::class, 'pocetna'])->name('home');

// kupac rute
Route::middleware(['auth', 'role:kupac'])->group(function () {
    Route::get('/moje-narudzbine', [NarudzbinaController::class, 'index'])->name('user.orders');
    // Ostale rute za korpu i sl.
});

// rute za radnike (zaposleni i admin)
Route::middleware(['auth', 'role:admin,zaposleni'])->group(function() {
    // upravljanje
    Route::resource('proizvod', ProizvodController::class);
    Route::resource('skladiste', SkladisteController::class);
    Route::resource('resurs', ResursController::class);
    
    // pregled narudzbina
    Route::get('/upravljanje-narudzbinama', [NarudzbinaController::class, 'index'])->name('admin.orders');
});

// samo admin rute
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/finansije', [FinansijeController::class, 'index'])->name('admin.finansije');
    Route::get('/finansije/izvestaj', [FinansijeController::class, 'generate'])->name('admin.izvestaj');
});