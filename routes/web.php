<?php

use App\Http\Controllers\Web\CatController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('cats.index');
});

Route::resource('cats', CatController::class);
