<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostgresLogController;
use App\Http\Controllers\RouterLoginController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/logs/postgresql', [PostgresLogController::class, 'index'])->name('logs.postgresql');

Route::get('/login', [RouterLoginController::class, 'showLogin'])->name('login');
Route::post('/login', [RouterLoginController::class, 'authenticate'])->name('router.login');

Route::get('/router-test', [RouterLoginController::class, 'testConnection'])->name('router.test');

Route::post('/logout', [RouterLoginController::class, 'logout'])->name('router.logout');

Route::get('/dhcp', function () {
    if (!session('router_logged_in')) {
        return redirect('/login');
    }

    return view('dhcp');
});
