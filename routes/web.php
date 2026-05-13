<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ParkingController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [ParkingController::class, 'dashboard'])->name('dashboard');
    Route::get('/gestion', [ParkingController::class, 'manage'])->name('manage');
    Route::post('/gestion/entrada', [ParkingController::class, 'storeEntry'])->name('manage.entry.store');
    Route::post('/gestion/salida', [ParkingController::class, 'closeTicket'])->name('manage.exit.close');
    Route::post('/gestion/pagos/{payment}/liquidar', [ParkingController::class, 'settlePending'])->name('manage.pending.settle');
    Route::match(['get', 'post'], '/portal-sync/run', [ParkingController::class, 'runPortalSync'])->name('portal.sync.run');

    Route::get('/entrada', [ParkingController::class, 'entry'])->name('entry');
    Route::get('/salida', [ParkingController::class, 'exit'])->name('exit');
    Route::get('/liquidacion', [ParkingController::class, 'payment'])->name('payment');
    Route::get('/confirmacion', [ParkingController::class, 'confirmation'])->name('confirmation');
    Route::delete('/settings/tariff/{tariff}', [ParkingController::class, 'deleteTariff'])
        ->name('settings.tariff.delete');
    Route::middleware('role:admin')->group(function () {
        Route::get('/reportes', [ParkingController::class, 'reports'])->name('reports');
        Route::get('/configuracion', [ParkingController::class, 'settings'])->name('settings');
        Route::post('/configuracion/tarifas', [ParkingController::class, 'storeTariff'])->name('settings.tariff.store');
        Route::put('/configuracion/tarifas/{tariff}', [ParkingController::class, 'updateTariff'])->name('settings.tariff.update');
        Route::post('/configuracion/locker', [ParkingController::class, 'updateLockerSettings'])->name('settings.locker.update');
        Route::get('/auditoria', [ParkingController::class, 'audit'])->name('audit');
        Route::post('/auditoria/usuarios', [ParkingController::class, 'storeUser'])->name('audit.users.store');
        Route::put('/auditoria/usuarios/{user}', [ParkingController::class, 'updateUser'])->name('audit.users.update');
    });

    Route::get('/transacciones/{ticket}/imprimir/{type}', [ParkingController::class, 'printReceipt'])
        ->where('type', 'ingreso|salida')
        ->name('tickets.print');
    Route::get('/transacciones/{ticket}/recibo/{type}', [ParkingController::class, 'receipt'])
        ->where('type', 'ingreso|salida')
        ->name('tickets.receipt');
    Route::get('/transacciones/{ticket}', [ParkingController::class, 'transaction'])->name('transaction.show');
});
