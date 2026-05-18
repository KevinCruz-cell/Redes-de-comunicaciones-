<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostgresLogController;
use App\Http\Controllers\RouterLoginController;
//Equipo Kevin
use App\Http\Controllers\RouterController;
//Equipo Adrian
use App\Http\Controllers\Router2Controller;

//Moy
use App\Http\Controllers\SystemController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ArranqueController;

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


//Equipo Kevin
// Vista principal
Route::get('/', [RouterController::class, 'index'])->name('panel.index');

// Endpoints de la API interna para el Router
Route::prefix('api/router')->group(function () {
    Route::get('/wifi-info', [RouterController::class, 'getWifiInfo']);
    Route::post('/restart-wifi', [RouterController::class, 'restartWifi']);
    Route::get('/scan', [RouterController::class, 'scanNetworks']);
    Route::get('/devices', [RouterController::class, 'getConnectedDevices']);
    Route::post('/switch', [RouterController::class, 'executeSwitchCommand']);
    Route::post('/apply-uci', [RouterController::class, 'applyUci']);
});Route::post('/api/router/save-switch', [RouterController::class, 'saveSwitchConfig']);
Route::get('/api/router/port-status', [RouterController::class, 'getPortStatus']);
Route::post('/api/router/set-port-state', [RouterController::class, 'setPortState']);
Route::get('/Wifi_Conmutador', [RouterController::class, 'index'])->name('Wifi_Conmutador');


// Equipo Adrian
// Ruta para la página de reinicio (vista simple)
Route::get('/router2/control', function () {
    return view('Reinicio.reinicio');  // CORREGIDO: reinicio
})->name('router2.control');

// Ruta principal de gestión de interfaces (listado) - CORREGIDO
Route::get('/router2/interfaces', [Router2Controller::class, 'gestionRed'])->name('router2.interfaces');

// Acciones globales
Route::post('/router2-reboot', [Router2Controller::class, 'reiniciar'])->name('router2.reboot');
Route::post('/router2/interfaces/create', [Router2Controller::class, 'crearInterfaz'])->name('router2.crear');
Route::get('/router2/refresh', [Router2Controller::class, 'refrescar'])->name('router2.refresh');

// ============================================================
// RUTAS CORRECTAS PARA OPERACIONES SOBRE UNA INTERFAZ ESPECÍFICA
// ============================================================
Route::post('/interfaz2/{iface}/reiniciar', [Router2Controller::class, 'reiniciarInterfaz'])->name('router2.iface.restart');
Route::post('/interfaz2/{iface}/detener', [Router2Controller::class, 'detenerInterfaz'])->name('router2.iface.stop');
Route::delete('/interfaz2/{iface}', [Router2Controller::class, 'eliminarInterfaz'])->name('router2.delete');
Route::get('/interfaz2/{iface}/editar', [Router2Controller::class, 'editarInterfaz'])->name('router2.editar');
Route::put('/interfaz2/{iface}', [Router2Controller::class, 'actualizarInterfaz'])->name('router2.actualizar');

// ============================================================
// Equipo Moy
// ============================================================

Route::get('/sistema', [SystemController::class, 'index'])
    ->name('system.index');

Route::get('/sistema/data', [SystemController::class, 'getData'])
    ->name('system.data');

Route::post('/sistema/update', [SystemController::class, 'update'])
    ->name('system.update');

// ============================================================
// Equipo Moy - Administracion
// ============================================================

Route::get('/sistema/administracion', [AdminController::class, 'index'])
    ->name('admin.index');

Route::get('/administracion/data', [AdminController::class, 'getData'])
    ->name('admin.data');

Route::post('/administracion/password', [AdminController::class, 'updatePassword'])
    ->name('admin.password');

Route::post('/administracion/ssh', [AdminController::class, 'updateSSH'])
    ->name('admin.ssh');

Route::post('/administracion/key', [AdminController::class, 'uploadKey'])
    ->name('admin.key');

    Route::get('/administracion/keys', [AdminController::class, 'getKeys'])
        ->name('admin.keys');

    Route::post('/administracion/key/delete', [AdminController::class, 'deleteKey'])
        ->name('admin.key.delete');

        /// Equipo Moy arranque

    Route::get('/sistema/arranque', [ArranqueController::class, 'index'])
        ->name('sistema.arranque');

    Route::get('/arranque/data', [ArranqueController::class, 'getData'])
        ->name('arranque.data');

    Route::post('/arranque/action', [ArranqueController::class, 'action'])
        ->name('arranque.action');

    Route::post('/arranque/local-startup', [ArranqueController::class, 'updateLocalStartup'])
        ->name('arranque.local.startup');

    ///Equipo Lizz
use App\Http\Controllers\Router3Controller;
// ============================================================
// RUTAS DEL EQUIPO DE RUTAS ESTÁTICAS Y DIAGNÓSTICO (Router3)
// ============================================================
// Ruta para la vista de rutas estáticas
Route::get('/router3/rutas', function () {
    if (!session('router_logged_in')) {
        return redirect('/login');
    }
    return view('Rutas.rutas');  // CORREGIDO: Rutas.rutas
})->name('router3.rutas');

// Ruta para la vista de diagnósticos
Route::get('/router3/diagnosticos', function () {
    if (!session('router_logged_in')) {
        return redirect('/login');
    }
    return view('Diagnostico.diagnosticos');
})->name('router3.diagnosticos');

// API para Router3
Route::prefix('api/router3')->group(function () {
    Route::get('/rutas', [Router3Controller::class, 'getRutas']);
    Route::post('/ruta/agregar', [Router3Controller::class, 'agregarRuta']);
    Route::post('/ruta/actualizar', [Router3Controller::class, 'actualizarRuta']);
    Route::post('/ruta/eliminar', [Router3Controller::class, 'eliminarRuta']);
    Route::post('/diagnostico', [Router3Controller::class, 'diagnostico']);
});

//Equipo Jona
use App\Http\Controllers\DashboardController;

// Dashboard - Equipo Jona
Route::get('/dashboard', [DashboardController::class, 'index'])->name('Dashboard.dashboard');
Route::get('/api/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');

// APIs adicionales para el dashboard
Route::prefix('api/dashboard')->group(function () {
    Route::get('/firewall', [DashboardController::class, 'getFirewallRules']);
    Route::get('/routes', [DashboardController::class, 'getRoutes']);
    Route::get('/syslog', [DashboardController::class, 'getSyslog']);
    Route::get('/kernel', [DashboardController::class, 'getKernelLog']);
    Route::get('/processes', [DashboardController::class, 'getProcesses']);
    Route::get('/realtime', [DashboardController::class, 'getRealtimeStats']);
});

//Equipo Joss
use App\Http\Controllers\Router4Controller;

// ============================================================
// RUTAS DEL EQUIPO DE TAREAS PROGRAMADAS, LEDs Y COPIA (Router4)
// ============================================================

// Tareas Programadas
Route::get('/router4/tareas', [Router4Controller::class, 'tareas'])->name('router4.tareas');
Route::post('/router4/tareas/guardar', [Router4Controller::class, 'guardarTareas'])->name('router4.tareas.guardar');

// Configuración LEDs
Route::get('/router4/leds', [Router4Controller::class, 'leds'])->name('router4.leds');
Route::post('/router4/led/encender', [Router4Controller::class, 'encenderLed'])->name('router4.led.encender');
Route::post('/router4/led/apagar', [Router4Controller::class, 'apagarLed'])->name('router4.led.apagar');
Route::post('/router4/led/trigger', [Router4Controller::class, 'configurarTrigger'])->name('router4.led.trigger');

// Copia de Seguridad
Route::get('/router4/copia', [Router4Controller::class, 'copia'])->name('router4.copia');
Route::get('/router4/backup/descargar', [Router4Controller::class, 'descargarBackup'])->name('router4.backup.descargar');
Route::post('/router4/backup/restaurar', [Router4Controller::class, 'restaurarBackup'])->name('router4.backup.restaurar');
Route::post('/router4/reset', [Router4Controller::class, 'resetFabrica'])->name('router4.reset');
Route::post('/router4/firmware/grabar', [Router4Controller::class, 'grabarFirmware'])->name('router4.firmware.grabar');
