<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SystemRouterService;

class DashboardController extends Controller
{
    public function index()
    {
        return view('Dashboard.dashboard');
    }

    public function getData(SystemRouterService $router)
    {
        try {
            return response()->json([
                'status' => 'success',
                'data' => $router->getSystemInfo()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener reglas de cortafuegos (iptables)
     */
    public function getFirewallRules(SystemRouterService $router)
    {
        try {
            $rules = $router->exec("iptables -L -n -v 2>/dev/null | head -100");
            return response()->json([
                'status' => 'success',
                'data' => $rules
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener tabla de rutas
     */
    public function getRoutes(SystemRouterService $router)
    {
        try {
            $routes = $router->exec("ip route show 2>/dev/null");
            return response()->json([
                'status' => 'success',
                'data' => $routes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener registro del sistema (syslog)
     */
    public function getSyslog(SystemRouterService $router)
    {
        try {
            $syslog = $router->exec("logread 2>/dev/null | tail -100");
            return response()->json([
                'status' => 'success',
                'data' => $syslog
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener registro del núcleo (dmesg)
     */
    public function getKernelLog(SystemRouterService $router)
    {
        try {
            $dmesg = $router->exec("dmesg 2>/dev/null | tail -100");
            return response()->json([
                'status' => 'success',
                'data' => $dmesg
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener lista de procesos
     */
    public function getProcesses(SystemRouterService $router)
    {
        try {
            $processes = $router->exec("ps aux 2>/dev/null | head -100");
            return response()->json([
                'status' => 'success',
                'data' => $processes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas en tiempo real
     */
    public function getRealtimeStats(SystemRouterService $router)
    {
        try {
            $stats = [
                'cpu' => $router->exec("top -bn1 | grep 'Cpu(s)' | awk '{print $2}' | cut -d'%' -f1 2>/dev/null"),
                'memory' => $router->exec("free -m | grep Mem | awk '{print $3}' 2>/dev/null"),
                'memory_total' => $router->exec("free -m | grep Mem | awk '{print $2}' 2>/dev/null"),
                'load' => $router->exec("cat /proc/loadavg | cut -d' ' -f1-3 2>/dev/null"),
                'connections' => $router->exec("netstat -an | grep ESTABLISHED | wc -l 2>/dev/null"),
                'uptime' => $router->exec("cat /proc/uptime | awk '{print int($1/3600)\"h \"int(($1%3600)/60)\"m\"}' 2>/dev/null"),
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
