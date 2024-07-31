<?php

use App\Http\Controllers\CoreController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CoreController::class, 'main'])->name('index');
Route::get('/myslot', [CoreController::class, 'mySlot']);
Route::post('/viewslot', [CoreController::class, 'viewSlot']);
Route::post('/check', [CoreController::class, 'checkDate']);
Route::get('/select/{date}', [CoreController::class, 'selectDate']);
Route::post('/saveslot', [CoreController::class, 'saveSlot']);

Route::get('/admin', [CoreController::class, 'admin']);
Route::post('/auth', [CoreController::class, 'authAdmin']);
Route::get('/admin/{teacher}', [CoreController::class, 'teacherEdit']);
Route::post('/addteacher', [CoreController::class, 'addTeacher']);
Route::post('/updateslot', [CoreController::class, 'updateSlot']);
Route::post('/updatetime', [CoreController::class, 'updateTime']);
