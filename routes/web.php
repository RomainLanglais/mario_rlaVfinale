<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\StockController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Routes films protégées par authentification
Route::middleware('auth')->group(function () {
    Route::get('/films', [FilmController::class, 'index'])->name('films.index');
    Route::get('/films/create', [FilmController::class, 'create'])->name('films.create');
    Route::post('/films', [FilmController::class, 'store'])->name('films.store');
    Route::delete('/films/{id}', [FilmController::class, 'destroy'])->name('films.destroy');
    Route::get('/films/{id}/edit', [FilmController::class, 'edit'])->name('films.edit');
    Route::put('/films/{id}', [FilmController::class, 'update'])->name('films.update');
    Route::get('/films/{id}', [FilmController::class, 'show'])->name('films.show');

    // Route stocks
    Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
    Route::get('/stocks/{filmId}', [StockController::class, 'show'])->name('stocks.show');
    Route::post('/stocks/{filmId}/dvd', [StockController::class, 'addDvd'])->name('stocks.addDvd');
    Route::delete('/stocks/{filmId}/dvd/{inventoryId}', [StockController::class, 'removeDvd'])->name('stocks.removeDvd');

});
