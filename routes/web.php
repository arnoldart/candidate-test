<?php

use App\Http\Controllers\CltLayerController;
use App\Http\Controllers\CltLayupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('suppliers/export', [SupplierController::class, 'exportAll'])->name('suppliers.exportAll');
    Route::resource('suppliers', SupplierController::class)->except(['show', 'create', 'edit']);
    Route::get('suppliers/{supplier}/export', [SupplierController::class, 'export'])->name('suppliers.export');
    Route::post('suppliers/{supplier}/import', [SupplierController::class, 'import'])->name('suppliers.import');
    Route::resource('suppliers.layups', CltLayupController::class)->except(['show', 'create', 'edit']);
    Route::post('layups/{layup}/layers/sync', [CltLayerController::class, 'sync'])->name('layups.layers.sync');
    Route::post('layups/{layup}/duplicate', [CltLayupController::class, 'duplicate'])->name('layups.duplicate');
    Route::resource('layups.layers', CltLayerController::class)->except(['show', 'create', 'edit']);
});

require __DIR__.'/auth.php';
