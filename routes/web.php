<?php

use App\Http\Controllers\PackageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('packages.index');
});

Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');

Route::get('/bodega', [PackageController::class, 'bodegaIndex'])->name('bodega.index');
Route::post('/bodega', [PackageController::class, 'bodegaStore'])->name('bodega.store');

Route::get('/documentacion', [\App\Http\Controllers\PackageDocumentationController::class, 'index'])->name('documentacion.index');
Route::post('/documentacion', [\App\Http\Controllers\PackageDocumentationController::class, 'store'])->name('documentacion.store');
