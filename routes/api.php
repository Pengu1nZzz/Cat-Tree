<?php

use App\Http\Controllers\Api\V1\CatController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/cats', [CatController::class, 'index'])->name('api.v1.cats.index');
    Route::get('/cats/{id}', [CatController::class, 'show'])->name('api.v1.cats.show');
    Route::post('/cats', [CatController::class, 'store'])->name('api.v1.cats.store');
    Route::match(['put', 'patch'], '/cats/{id}', [CatController::class, 'update'])->name('api.v1.cats.update');
    Route::delete('/cats/{id}', [CatController::class, 'destroy'])->name('api.v1.cats.destroy');
});

Route::get('/cats', [CatController::class, 'index'])->name('api.cats.index');
Route::get('/cats/{id}', [CatController::class, 'show'])->name('api.cats.show');
Route::post('/cats', [CatController::class, 'store'])->name('api.cats.store');
Route::match(['put', 'patch'], '/cats/{id}', [CatController::class, 'update'])->name('api.cats.update');
Route::delete('/cats/{id}', [CatController::class, 'destroy'])->name('api.cats.destroy');
