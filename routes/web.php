<?php

use App\Http\Controllers\PackageController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PackageReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('packages.index');
});

Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
Route::get('/paquetes-registrados', [PackageController::class, 'registeredIndex'])->name('packages.registered');

Route::get('/bodega', [PackageController::class, 'bodegaIndex'])->name('bodega.index');
Route::post('/bodega', [PackageController::class, 'bodegaStore'])->name('bodega.store');

Route::get('/documentacion', [\App\Http\Controllers\PackageDocumentationController::class, 'index'])->name('documentacion.index');
Route::post('/documentacion', [\App\Http\Controllers\PackageDocumentationController::class, 'store'])->name('documentacion.store');
Route::get('/documentacion/documentados', [\App\Http\Controllers\PackageDocumentationController::class, 'documented'])->name('documentacion.documented');
Route::patch('/documentacion/{package}/recibido', [\App\Http\Controllers\PackageDocumentationController::class, 'markAsReceived'])->name('documentacion.markAsReceived');
Route::get('/reportes/paquetes', [PackageReportController::class, 'index'])->name('reports.packages');

Route::resource('clientes', ClientController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])->parameters(['clientes' => 'client'])->names('clients');
Route::resource('socios', PartnerController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])->parameters(['socios' => 'partner'])->names('partners');
Route::resource('usuarios', UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy'])->parameters(['usuarios' => 'user'])->names('users');
