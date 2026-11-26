<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrdenController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RepuestoController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\VehiculoController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::resource('clientes', ClienteController::class)->except(['destroy']);
    Route::resource('vehiculos', VehiculoController::class)->except(['destroy']);
    Route::resource('ordenes', OrdenController::class)->except(['destroy'])->parameters(['ordenes' => 'orden']);

    Route::patch('ordenes/{orden}/estado', [OrdenController::class, 'cambiarEstado'])->name('ordenes.estado');
    Route::patch('ordenes/{orden}/avanzar', [OrdenController::class, 'avanzarEstado'])->name('ordenes.avanzar');
    Route::get('ordenes/{orden}/imprimir', [OrdenController::class, 'imprimir'])->name('ordenes.imprimir');

    Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('reportes/exportar', [ReporteController::class, 'exportar'])->name('reportes.exportar');

    Route::middleware('role:admin')->group(function () {
        Route::delete('clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
        Route::delete('vehiculos/{vehiculo}', [VehiculoController::class, 'destroy'])->name('vehiculos.destroy');
        Route::delete('ordenes/{orden}', [OrdenController::class, 'destroy'])->name('ordenes.destroy');

        Route::post('repuestos/{repuesto}/reponer', [RepuestoController::class, 'reponer'])->name('repuestos.reponer');

        Route::resource('servicios', ServicioController::class)->except(['index', 'show']);
        Route::resource('repuestos', RepuestoController::class)->except(['index', 'show']);
    });

    Route::resource('servicios', ServicioController::class)->only(['index', 'show']);
    Route::resource('repuestos', RepuestoController::class)->only(['index', 'show']);
});
