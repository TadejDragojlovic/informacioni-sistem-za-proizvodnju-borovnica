<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProizvodController;
use App\Http\Controllers\SkladisteController;
use App\Http\Controllers\ResursController;
use App\Http\Controllers\NarudzbinaController;
use App\Http\Controllers\FinansijeController;
use App\Http\Controllers\KorpaController;

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
    Route::get('/moje-narudzbine', [NarudzbinaController::class, 'mojeNarudzbine'])->name('user.orders');
    Route::post('/narudzbine/potvrdi', [NarudzbinaController::class, 'potvrdi'])->name('narudzbine.potvrdi');

    Route::get('/korpa', [KorpaController::class, 'index'])->name('korpa.index');
    Route::post('/korpa/dodaj/{id}', [KorpaController::class, 'dodaj'])->name('korpa.dodaj');
    Route::post('/korpa/azuriraj/{id}', [KorpaController::class, 'azuriraj'])->name('korpa.azuriraj');
    Route::post('/korpa/obrisi/{id}', [KorpaController::class, 'obrisi'])->name('korpa.obrisi');
});

// rute za radnike (zaposleni i admin)
Route::middleware(['auth', 'role:admin,zaposleni'])->group(function() {
    // upravljanje
    Route::resource('proizvod', ProizvodController::class);
    Route::resource('skladiste', SkladisteController::class);
    Route::resource('resurs', ResursController::class);
    Route::resource('narudzbine', NarudzbinaController::class);
});

// samo admin rute
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/finansije', [FinansijeController::class, 'index'])->name('admin.finansije');
    Route::get('/finansije/izvestaj', [FinansijeController::class, 'generate'])->name('admin.izvestaj');
});