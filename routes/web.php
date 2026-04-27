<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostgresLogController;
use App\Http\Controllers\RouterLoginController;

// Vista principal
Route::get('/', function () {
    return view('welcome');
});

// Autenticación
Route::get('/login', [RouterLoginController::class, 'showLogin'])->name('login');
Route::post('/login', [RouterLoginController::class, 'authenticate'])->name('router.login');
Route::post('/logout', [RouterLoginController::class, 'logout'])->name('router.logout');

// Grupo de rutas protegidas (Middleware de sesión)
Route::middleware(['auth.router'])->group(function () {

    // Test de conexión
    Route::get('/router-test', [RouterLoginController::class, 'testConnection'])->name('router.test');

    // Logs
    Route::get('/logs/postgresql', [PostgresLogController::class, 'index'])->name('logs.postgresql');

    // --- SECCIÓN RED (DHCP y HOSTS) ---

    // DHCP Configuración General
    Route::get('/dhcp', function () {
        return view('dhcp.dhcp'); // Apunta a views/dhcp/dhcp.blade.php
    })->name('dhcp.index');

    // DHCP Archivos Resolv y Hosts
    Route::get('/dhcp/resolv', function () {
        return view('dhcp.dhcp-resolv'); // Apunta a views/dhcp/dhcp-resolv.blade.php
    })->name('dhcp.resolv');

    // Nombres de Host
    Route::get('/nombres-host', function () {
        return view('hosts.hosts'); // Apunta a views/hosts/hosts.blade.php
    })->name('hosts.index');

});
