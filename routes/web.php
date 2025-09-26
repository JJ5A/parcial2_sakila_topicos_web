<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActorController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\RentalController;

Route::get('/', function () {
    return redirect()->route('rentals.index');
});

// Rutas para el CRUD de actores
Route::resource('actors', ActorController::class);

// Rutas para el CRUD de películas
Route::resource('films', FilmController::class);

// Rutas para el sistema de rentas
Route::resource('rentals', RentalController::class)->except(['edit', 'update']);

// Rutas específicas para el sistema de rentas
Route::get('rentals/return', [RentalController::class, 'showReturnForm'])->name('rentals.return');
Route::post('rentals/{rental}/return', [RentalController::class, 'processReturn'])->name('rentals.process-return');
Route::get('rentals/overdue', [RentalController::class, 'overdueRentals'])->name('rentals.overdue');
Route::get('rentals/search-inventory', [RentalController::class, 'searchInventory'])->name('rentals.search-inventory');
Route::get('rentals/{rental}/return-details', [RentalController::class, 'getReturnDetails'])->name('rentals.return-details');
