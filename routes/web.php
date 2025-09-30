<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActorController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\CustomerController;

// Rutas de autenticación
Auth::routes();

// Ruta raíz - redirigir según autenticación
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('rentals.index');
    }
    return redirect()->route('login');
});

// Grupo de rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    
    // Rutas para el CRUD de actores
    Route::resource('actors', ActorController::class);

    // Rutas para el CRUD de películas
    Route::resource('films', FilmController::class);
    Route::get('films-available', [FilmController::class, 'available'])->name('films.available');

    // Rutas para el sistema de clientes
    Route::resource('customers', CustomerController::class)->only(['index', 'show']);

    // Rutas para el sistema de rentas
    Route::resource('rentals', RentalController::class)->except(['edit', 'update']);
    Route::get('rentals-active', [RentalController::class, 'active'])->name('rentals.active');
    Route::get('rentals-overdue', [RentalController::class, 'overdue'])->name('rentals.overdue');

    // Rutas específicas para el sistema de rentas
    Route::get('rentals/return', [RentalController::class, 'returnForm'])->name('rentals.return');
    Route::post('rentals/{rental}/return', [RentalController::class, 'processReturn'])->name('rentals.process-return');
    Route::post('rentals/{rental}/quick-return', [RentalController::class, 'quickReturn'])->name('rentals.quick-return');
    Route::get('rentals/search', [RentalController::class, 'searchRental'])->name('rentals.search');
    Route::get('rentals/search-inventory', [RentalController::class, 'searchInventory'])->name('rentals.search-inventory');
    Route::get('rentals/{rental}/return-details', [RentalController::class, 'getReturnDetails'])->name('rentals.return-details');
    Route::get('rentals/{rental}/calculate-return', [RentalController::class, 'calculateReturnDetails'])->name('rentals.calculate-return');
    Route::get('rentals/report', [RentalController::class, 'report'])->name('rentals.report');
    
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
