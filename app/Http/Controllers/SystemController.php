<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SystemRouterService;

class SystemController extends Controller
{
    public function index()
    {
        return view('system.index');
    }

    public function getData(SystemRouterService $router)
    {
        $log_level = $this->limpiarUci($router->exec("uci get system.@system[0].conloglevel"));
        $cron_level = $this->limpiarUci($router->exec("uci get system.@system[0].cronloglevel"));

        return response()->json([
            'hostname' => $this->limpiarUci($router->exec("uci get system.@system[0].hostname")),
            'time' => trim($router->exec("date")),

            // Datos de configuracion general
            'description' => $this->limpiarUci($router->exec("uci get system.@system[0].description")),
            'notes' => $this->limpiarUci($router->exec("uci get system.@system[0].notes")),
            'zonename' => $this->limpiarUci($router->exec("uci get system.@system[0].zonename")),
            'timezone' => $this->limpiarUci($router->exec("uci get system.@system[0].timezone")),

            // Datos de registro
            'log_size' => $this->limpiarUci($router->exec("uci get system.@system[0].log_size")),
            'log_ip' => $this->limpiarUci($router->exec("uci get system.@system[0].log_ip")),
            'log_port' => $this->limpiarUci($router->exec("uci get system.@system[0].log_port")),
            'log_proto' => $this->limpiarUci($router->exec("uci get system.@system[0].log_proto")),
            'log_file' => $this->limpiarUci($router->exec("uci get system.@system[0].log_file")),

            'log_level' => $this->mapLogLevel($log_level),
            'cron_level' => $this->mapCronLevel($cron_level),
        ]);
    }

    public function update(Request $request, SystemRouterService $router)
    {
        try {
            $cmd = "";

            // Nombre de host
            if ($request->has('hostname')) {
                $cmd .= "uci set system.@system[0].hostname=" . escapeshellarg($request->hostname) . "; ";
            }

            // Descripcion
            if ($request->has('description')) {
                $cmd .= "uci set system.@system[0].description=" . escapeshellarg($request->description ?? '') . "; ";
            }

            // Notas
            if ($request->has('notes')) {
                $cmd .= "uci set system.@system[0].notes=" . escapeshellarg($request->notes ?? '') . "; ";
            }

            // Zona horaria visible
            if ($request->has('zonename')) {
                $cmd .= "uci set system.@system[0].zonename=" . escapeshellarg($request->zonename ?? 'UTC') . "; ";
            }

            // Codigo real de zona horaria
            if ($request->has('timezone')) {
                $cmd .= "uci set system.@system[0].timezone=" . escapeshellarg($request->timezone ?? 'UTC0') . "; ";
            }

            // Registro del sistema
            if ($request->has('log_size')) {
                $cmd .= "uci set system.@system[0].log_size=" . escapeshellarg($request->log_size ?? '') . "; ";
            }

            if ($request->has('log_ip')) {
                $cmd .= "uci set system.@system[0].log_ip=" . escapeshellarg($request->log_ip ?? '') . "; ";
            }

            if ($request->has('log_port')) {
                $cmd .= "uci set system.@system[0].log_port=" . escapeshellarg($request->log_port ?? '') . "; ";
            }

            if ($request->has('log_proto')) {
                $cmd .= "uci set system.@system[0].log_proto=" . escapeshellarg($request->log_proto ?? 'udp') . "; ";
            }

            if ($request->has('log_file')) {
                $cmd .= "uci set system.@system[0].log_file=" . escapeshellarg($request->log_file ?? '') . "; ";
            }

            // Nivel de registro
            if ($request->has('log_level')) {
                $cmd .= "uci set system.@system[0].conloglevel=" . escapeshellarg($this->reverseLogLevel($request->log_level)) . "; ";
            }

            // Nivel de cron
            if ($request->has('cron_level')) {
                $cmd .= "uci set system.@system[0].cronloglevel=" . escapeshellarg($this->reverseCronLevel($request->cron_level)) . "; ";
            }

            // Guardar y aplicar cambios
            $cmd .= "uci commit system; ";
            $cmd .= "/etc/init.d/system reload; ";
            $cmd .= "/etc/init.d/log restart; ";

            $router->exec($cmd);

            return response()->json([
                'success' => true,
                'message' => 'Configuracion guardada correctamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function mapLogLevel($level)
    {
        return match ((string) $level) {
            '8' => 'Depurar',
            '7' => 'Info',
            '6' => 'Aviso',
            '5' => 'Advertencia',
            '4' => 'Error',
            '3' => 'Critico',
            '2' => 'Alerta',
            '1' => 'Emergencia',
            default => 'Depurar'
        };
    }

    private function reverseLogLevel($text)
    {
        return match ($text) {
            'Depurar' => 8,
            'Info' => 7,
            'Aviso' => 6,
            'Advertencia' => 5,
            'Error' => 4,
            'Critico', 'Crítico' => 3,
            'Alerta' => 2,
            'Emergencia' => 1,
            default => 8
        };
    }

    private function mapCronLevel($level)
    {
        return match ((string) $level) {
            '5' => 'Depurar',
            '8' => 'Normal',
            '9' => 'Advertencia',
            default => 'Depurar'
        };
    }

    private function reverseCronLevel($text)
    {
        return match ($text) {
            'Depurar' => 5,
            'Normal' => 8,
            'Advertencia' => 9,
            default => 5
        };
    }

    private function limpiarUci($valor)
    {
        $valor = trim($valor);

        if (
            str_contains($valor, 'uci: Entry not found') ||
            str_contains($valor, 'Entry not found') ||
            str_contains($valor, 'not found')
        ) {
            return '';
        }

        return $valor;
    }
}
