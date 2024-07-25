<?php

use App\Http\Controllers\CoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CoreController::class, 'main'])->name('index');
