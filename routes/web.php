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

// Logs de PostgreSQL
Route::get('/logs/postgresql', [PostgresLogController::class, 'index'])->name('logs.postgresql');

// Test de conexión
Route::get('/router-test', [RouterLoginController::class, 'testConnection'])->name('router.test');

// --- SECCIÓN RED (Vistas con validación manual de sesión) ---

// DHCP Configuración General
Route::get('/dhcp', function () {
    if (!session('router_logged_in')) {
        return redirect('/login');
    }
    return view('dhcp.dhcp');
})->name('dhcp.index');

// DHCP Archivos Resolv y Hosts
Route::get('/dhcp/resolv', function () {
    if (!session('router_logged_in')) {
        return redirect('/login');
    }
    return view('dhcp.dhcp-resolv');
})->name('dhcp.resolv');

// Nombres de Host
Route::get('/nombres-host', function () {
    if (!session('router_logged_in')) {
        return redirect('/login');
    }
    return view('hosts.hosts');
})->name('hosts.index');
