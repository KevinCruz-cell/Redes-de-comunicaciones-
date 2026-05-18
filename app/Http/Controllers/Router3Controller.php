<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use phpseclib3\Net\SSH2;

class Router3Controller extends Controller
{
    // Configuración del router
    private $ip = '192.168.10.1';
    private $user = 'root';
    private $password = 'TU_CONTRASEÑA_REAL';

    /**
     * Obtener todas las rutas IPv4 e IPv6
     */
    public function getRutas()
    {
        try {
            $ssh = new SSH2($this->ip);
            if (!$ssh->login($this->user, $this->password)) {
                return response()->json(['status' => 'error', 'message' => 'Error de autenticación SSH'], 401);
            }

            // Obtener rutas IPv4
            $output = $ssh->exec("uci show network 2>/dev/null | grep -E 'network\\.@route\\[[0-9]+\\]\\.' | grep -v ip6");
            $rutas_ipv4 = $this->parseRutas($output, 'route');

            // Obtener rutas IPv6
            $output6 = $ssh->exec("uci show network 2>/dev/null | grep -E 'network\\.@route6\\[[0-9]+\\]\\.'");
            $rutas_ipv6 = $this->parseRutas($output6, 'route6');

            $ssh->disconnect();

            return response()->json([
                'status' => 'success',
                'ipv4' => $rutas_ipv4,
                'ipv6' => $rutas_ipv6
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function parseRutas($output, $tipo)
    {
        $rutas = [];
        preg_match_all("/network\.@{$tipo}\[(\d+)\]\.([^=]+)='([^']+)'/", $output, $matches, PREG_SET_ORDER);

        $rutas_tmp = [];
        foreach ($matches as $match) {
            $idx = $match[1];
            $key = $match[2];
            $value = $match[3];
            if (!isset($rutas_tmp[$idx])) $rutas_tmp[$idx] = [];
            $rutas_tmp[$idx][$key] = $value;
        }

        foreach ($rutas_tmp as $idx => $ruta) {
            $item = [
                'id' => $idx,
                'interface' => $ruta['interface'] ?? '',
                'target' => $ruta['target'] ?? '',
                'gateway' => $ruta['gateway'] ?? '',
                'metric' => $ruta['metric'] ?? '0',
                'mtu' => $ruta['mtu'] ?? '1500',
                'table' => $ruta['table'] ?? 'main',
                'type' => $ruta['type'] ?? 'unicast',
                'source' => $ruta['source'] ?? '',
                'onlink' => isset($ruta['onlink']) ? '1' : '0'
            ];

            if ($tipo === 'route') {
                $item['netmask'] = $ruta['netmask'] ?? '255.255.255.255';
            }

            $rutas[] = $item;
        }
        return $rutas;
    }

    /**
     * Agregar una nueva ruta
     */
    public function agregarRuta(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:ipv4,ipv6',
            'interfaz' => 'required|string',
            'destino' => 'required|string',
            'gateway' => 'required|string'
        ]);

        try {
            $ssh = new SSH2($this->ip);
            if (!$ssh->login($this->user, $this->password)) {
                return response()->json(['status' => 'error', 'message' => 'Error de autenticación SSH'], 401);
            }

            $tipo_ruta = ($request->tipo === 'ipv4') ? 'route' : 'route6';
            $cmds = [
                "uci add network {$tipo_ruta}",
                "uci set network.@{$tipo_ruta}[-1].interface='{$request->interfaz}'",
                "uci set network.@{$tipo_ruta}[-1].target='{$request->destino}'",
                "uci set network.@{$tipo_ruta}[-1].gateway='{$request->gateway}'",
                "uci set network.@{$tipo_ruta}[-1].metric='{$request->metrica}'",
                "uci set network.@{$tipo_ruta}[-1].mtu='{$request->mtu}'",
                "uci set network.@{$tipo_ruta}[-1].table='{$request->tabla}'",
                "uci set network.@{$tipo_ruta}[-1].type='{$request->tipo_ruta}'"
            ];

            if ($request->tipo === 'ipv4') {
                $cmds[] = "uci set network.@{$tipo_ruta}[-1].netmask='{$request->mascara}'";
            }

            if (!empty($request->origen)) {
                $cmds[] = "uci set network.@{$tipo_ruta}[-1].source='{$request->origen}'";
            }

            if ($request->onlink == '1') {
                $cmds[] = "uci set network.@{$tipo_ruta}[-1].onlink='1'";
            }

            $cmds[] = "uci commit network";

            foreach ($cmds as $cmd) {
                $ssh->exec($cmd);
            }

            $ssh->disconnect();

            return response()->json(['status' => 'success', 'message' => 'Ruta agregada correctamente']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar una ruta existente
     */
    public function actualizarRuta(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:ipv4,ipv6',
            'id' => 'required|string',
            'interfaz' => 'required|string',
            'destino' => 'required|string',
            'gateway' => 'required|string'
        ]);

        try {
            $ssh = new SSH2($this->ip);
            if (!$ssh->login($this->user, $this->password)) {
                return response()->json(['status' => 'error', 'message' => 'Error de autenticación SSH'], 401);
            }

            $tipo_ruta = ($request->tipo === 'ipv4') ? 'route' : 'route6';
            $cmds = [
                "uci set network.@{$tipo_ruta}[{$request->id}].interface='{$request->interfaz}'",
                "uci set network.@{$tipo_ruta}[{$request->id}].target='{$request->destino}'",
                "uci set network.@{$tipo_ruta}[{$request->id}].gateway='{$request->gateway}'",
                "uci set network.@{$tipo_ruta}[{$request->id}].metric='{$request->metrica}'",
                "uci set network.@{$tipo_ruta}[{$request->id}].mtu='{$request->mtu}'",
                "uci set network.@{$tipo_ruta}[{$request->id}].table='{$request->tabla}'",
                "uci set network.@{$tipo_ruta}[{$request->id}].type='{$request->tipo_ruta}'"
            ];

            if ($request->tipo === 'ipv4') {
                $cmds[] = "uci set network.@{$tipo_ruta}[{$request->id}].netmask='{$request->mascara}'";
            }

            if (!empty($request->origen)) {
                $cmds[] = "uci set network.@{$tipo_ruta}[{$request->id}].source='{$request->origen}'";
            } else {
                $cmds[] = "uci -q del network.@{$tipo_ruta}[{$request->id}].source";
            }

            if ($request->onlink == '1') {
                $cmds[] = "uci set network.@{$tipo_ruta}[{$request->id}].onlink='1'";
            } else {
                $cmds[] = "uci -q del network.@{$tipo_ruta}[{$request->id}].onlink";
            }

            $cmds[] = "uci commit network";

            foreach ($cmds as $cmd) {
                $ssh->exec($cmd);
            }

            $ssh->disconnect();

            return response()->json(['status' => 'success', 'message' => 'Ruta actualizada correctamente']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar una ruta
     */
    public function eliminarRuta(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:ipv4,ipv6',
            'target' => 'required|string',
            'gateway' => 'required|string'
        ]);

        try {
            // Primero obtener las rutas para encontrar el ID
            $rutas = $this->getRutas()->getData();
            if ($rutas->status !== 'success') {
                return response()->json(['status' => 'error', 'message' => 'No se pudieron obtener las rutas'], 500);
            }

            $lista = ($request->tipo === 'ipv4') ? $rutas->ipv4 : $rutas->ipv6;
            $id = null;

            foreach ($lista as $ruta) {
                if ($ruta->target == $request->target && $ruta->gateway == $request->gateway) {
                    $id = $ruta->id;
                    break;
                }
            }

            if ($id === null) {
                return response()->json(['status' => 'error', 'message' => 'Ruta no encontrada'], 404);
            }

            $ssh = new SSH2($this->ip);
            if (!$ssh->login($this->user, $this->password)) {
                return response()->json(['status' => 'error', 'message' => 'Error de autenticación SSH'], 401);
            }

            $tipo_ruta = ($request->tipo === 'ipv4') ? 'route' : 'route6';
            $ssh->exec("uci delete network.@{$tipo_ruta}[{$id}]");
            $ssh->exec("uci commit network");
            $ssh->disconnect();

            return response()->json(['status' => 'success', 'message' => 'Ruta eliminada correctamente']);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Diagnóstico: Ping, Traceroute, Nslookup
     */
    /**
     * Diagnóstico: Ping, Traceroute, Nslookup
     */
    public function diagnostico(Request $request)
    {
        try {
            $request->validate([
                'accion' => 'required|in:ping,traceroute,nslookup',
                'destino' => 'required|string'
            ]);

            // Log para depuración
            \Log::info('Diagnóstico recibido', ['accion' => $request->accion, 'destino' => $request->destino]);

            $ssh = new SSH2($this->ip);
            if (!$ssh->login($this->user, $this->password)) {
                \Log::error('Error de autenticación SSH');
                return response()->json(['status' => 'error', 'message' => 'Error de autenticación SSH'], 401);
            }

            $comando = '';
            switch ($request->accion) {
                case 'ping':
                    $comando = "ping -c 5 -W 2 " . escapeshellarg($request->destino) . " 2>&1";
                    break;
                case 'traceroute':
                    $comando = "traceroute -n " . escapeshellarg($request->destino) . " 2>&1";
                    break;
                case 'nslookup':
                    $comando = "nslookup " . escapeshellarg($request->destino) . " 2>&1";
                    break;
            }

            \Log::info('Ejecutando comando:', ['comando' => $comando]);

            $output = $ssh->exec($comando);
            $ssh->disconnect();

            \Log::info('Resultado obtenido', ['output_length' => strlen($output)]);

            return response()->json([
                'status' => 'success',
                'output' => $output ?: 'No se obtuvo respuesta del comando'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error en diagnóstico:', ['error' => $e->getMessage(), 'line' => $e->getLine()]);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
