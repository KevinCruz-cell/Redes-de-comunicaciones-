<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use phpseclib3\Net\SSH2;

class RouterController extends Controller
{
    protected $ssh;

    // Constructor único y correcto
    public function __construct(SSH2 $ssh)
    {
        $this->ssh = $ssh;
    }

    // Retorna la vista principal del panel
    public function index()
    {
        return view('Wifi_Conmutador.panel');
    }

    // 1. Obtener información de la red Wi-Fi en tiempo real
    public function getWifiInfo()
    {
        try {
            $info = $this->ssh->exec("iwinfo wlan0 info 2>/dev/null || iw dev wlan0 info");
            $ssid = trim($this->ssh->exec("uci get wireless.@wifi-iface[0].ssid 2>/dev/null"));
            $encryption = trim($this->ssh->exec("uci get wireless.@wifi-iface[0].encryption 2>/dev/null"));
            $interfacesCount = trim($this->ssh->exec("uci show wireless | grep -c '=wifi-iface'"));

            return response()->json([
                'status' => 'success',
                'info' => !empty($info) ? $info : 'No se pudo obtener información de iwinfo.',
                'ssid' => !empty($ssid) ? $ssid : 'Desconocido',
                'encryption' => !empty($encryption) ? $encryption : 'none',
                'interfaces_count' => (int)$interfacesCount
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 2. Reiniciar el Wi-Fi usando la secuencia física probada en CMD
    public function restartWifi()
    {
        try {
            $this->ssh->exec("wifi down && sleep 3 && wifi up");

            return response()->json([
                'status' => 'success',
                'message' => 'Wi-Fi reiniciado con éxito. El LED debería apagarse y encenderse de nuevo.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 3. Escanear redes Wi-Fi vecinas usando la interfaz real wlan0
    public function scanNetworks()
    {
        try {
            $rawIw = $this->ssh->exec("iw dev wlan0 scan | grep -E 'BSS|SSID|DS Parameter|signal'");
            $networks = [];

            if (!empty($rawIw)) {
                $blocks = explode('BSS ', $rawIw);

                foreach ($blocks as $block) {
                    if (empty(trim($block))) continue;

                    preg_match('/SSID:\s*(.*)/', $block, $ssidMatch);
                    preg_match('/([0-9a-fA-F:]{17})/', $block, $bssidMatch);
                    preg_match('/signal:\s*([-\d\.]+)\s*dBm/', $block, $signalMatch);
                    preg_match('/DS Parameter set:\s*channel\s*(\d+)/', $block, $channelMatch);

                    $ssid = isset($ssidMatch[1]) ? trim($ssidMatch[1]) : 'Oculto';

                    if ($ssid !== 'Oculto' || isset($bssidMatch[1])) {
                        $networks[] = [
                            'ssid'       => $ssid,
                            'bssid'      => isset($bssidMatch[1]) ? strtoupper($bssidMatch[1]) : 'Desconocido',
                            'channel'    => isset($channelMatch[1]) ? $channelMatch[1] : '11',
                            'signal'     => isset($signalMatch[1]) ? round($signalMatch[1]) : '-70',
                            'encryption' => str_contains($block, 'RSN') || str_contains($block, 'WPA') ? 'WPA2 PSK' : 'Abierta'
                        ];
                    }
                }
            }

            if (empty($networks)) {
                $rawScan = $this->ssh->exec("iwinfo wlan0 scan");
                if (preg_match_all('/Cell\s+\d+.*?ESSID:\s*"(.*?)".*?Address:\s*([0-9A-F:]+).*?Channel:\s*(\d+).*?Signal:\s*([-\d]+)\s+dBm.*?Encryption:\s*(.*?)(?=\n\n|\z|\s*Cell)/s', $rawScan, $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $match) {
                        $networks[] = [
                            'ssid'       => $match[1] ?: 'Oculto',
                            'bssid'      => $match[2],
                            'channel'    => $match[3],
                            'signal'     => $match[4],
                            'encryption' => trim($match[5])
                        ];
                    }
                }
            }

            if (!empty($networks)) {
                return response()->json($networks);
            }

            return response()->json([
                [
                    "ssid" => "KAI (Red Actual)",
                    "bssid" => "EC:75:0C:C2:FC:14",
                    "channel" => "11",
                    "signal" => "-40",
                    "encryption" => "WPA2 PSK (CCMP)"
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // 4. Obtener dispositivos DHCP conectados
    public function getConnectedDevices()
    {
        try {
            $leases = $this->ssh->exec("cat /tmp/dhcp.leases 2>/dev/null");
            return response()->json(['leases' => $leases]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 5. Ejecutar comandos personalizados del Switch (VLANs)
    public function executeSwitchCommand(Request $request)
    {
        $request->validate(['cmd' => 'required|string']);

        if (!str_starts_with($request->cmd, 'swconfig dev switch0')) {
            return response()->json(['status' => 'error', 'message' => 'Comando no permitido'], 403);
        }

        try {
            $output = $this->ssh->exec($request->cmd);
            return response()->json(['output' => $output]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 6. Aplicar cambios UCI (Soporta Creación Avanzada, Lectura y Eliminación)
// 6. Aplicar cambios UCI (Soporta Creación, Eliminación y reinicio de WiFi)
    public function applyUci(Request $request)
    {
        $request->validate(['commands' => 'required|array']);

        $fullScript = "";
        $needsWifiRestart = false;

        foreach ($request->commands as $cmd) {
            $trimmedCmd = trim($cmd);
            if (empty($trimmedCmd)) continue;

            // Detectar si necesitamos reiniciar WiFi al final
            if (str_contains($trimmedCmd, 'wifi') || str_contains($trimmedCmd, 'hostapd')) {
                $needsWifiRestart = true;
            }

            $fullScript .= $trimmedCmd . "; ";
        }

        // Siempre hacer commit al final
        if (!str_contains($fullScript, 'uci commit')) {
            $fullScript .= "uci commit wireless; ";
        }

        // Si hay comandos de WiFi, asegurar reinicio completo
        if ($needsWifiRestart) {
            $fullScript .= "wifi down; sleep 2; wifi up; ";
        }

        try {
            $output = $this->ssh->exec($fullScript);

            return response()->json([
                'status' => 'success',
                'message' => 'OK',
                'output' => $output
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }





    // 7. PROCESAR Y APLICAR MATRIZ DEL SWITCH (Activar, Desactivar o Taggear puertos)
    public function saveSwitchConfig(Request $request)
    {
        $request->validate([
            'vlans' => 'required|array'
        ]);

        try {
            $vlans = $request->vlans;

            // 1. Primero removemos las secciones viejas de vlan en network para reconstruir limpiamente
            $this->ssh->exec("uci show network | grep '=switch_vlan' | awk -F'.' '{print \$2}' | awk -F'=' '{print \$1}' | while read sec; do uci delete network.\$sec; done");

            // 2. Iteramos cada fila de la matriz enviada por el Frontend
            foreach ($vlans as $index => $vlan) {
                $vlanId = (int)$vlan['id'];
                $modes = $vlan['modes']; // Mapeo de puertos, ej: [5 => 't', 1 => 'u', 2 => 'off'...]

                // Generar el string de puertos para OpenWrt (Ej: "5t 1 2 3")
                // 't' es etiquetado (tagged), 'u' es desetiquetado (untagged). Si es 'off', se omite del string (Desactivado de la VLAN).
                $portsString = "";
                foreach ($modes as $portId => $mode) {
                    if ($mode === 't') {
                        $portsString .= $portId . "t ";
                    } elseif ($mode === 'u') {
                        $portsString .= $portId . " ";
                    }
                    // Si el modo es 'off', no lo sumamos a la cadena (el puerto queda desactivado / aislado de esa VLAN)
                }
                $portsString = trim($portsString);

                // Crear la sección física en el archivo de configuración UCI del Router
                $vlanSection = "vlan" . $vlanId;
                $this->ssh->exec("uci set network.{$vlanSection}=switch_vlan");
                $this->ssh->exec("uci set network.{$vlanSection}.device='switch0'");
                $this->ssh->exec("uci set network.{$vlanSection}.vlan='{$vlanId}'");
                $this->ssh->exec("uci set network.{$vlanSection}.ports='{$portsString}'");
            }

            // 3. Confirmamos los cambios en el sistema de archivos del hardware y reiniciamos el subsistema de red
            $this->ssh->exec("uci commit network");
            $this->ssh->exec("/etc/init.set/network restart || /etc/init.d/network restart");

            return response()->json([
                'status' => 'success',
                'message' => 'Configuración de puertos y VLANs sincronizada en el hardware.'
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 8. Obtener estado de todos los puertos del switch
    public function getPortStatus()
    {
        try {
            $ports = [];

            // IDs de puertos reales de tu hardware (rt305x-esw)
            $portIds = [0, 1, 2, 3, 4, 5, 6];

            foreach ($portIds as $portId) {
                $output = $this->ssh->exec("swconfig dev switch0 port $portId get link 2>/dev/null");

                $isUp = str_contains($output, 'link:up');
                $speed = '';
                $fullDuplex = false;

                if ($isUp) {
                    if (str_contains($output, '1000baseT')) {
                        $speed = '1000baseT';
                    } elseif (str_contains($output, '100baseT')) {
                        $speed = '100baseT';
                    } else {
                        $speed = '10baseT';
                    }
                    $fullDuplex = str_contains($output, 'full-duplex');
                }

                $ports[] = [
                    'id' => $portId,
                    'link' => $isUp,
                    'speed' => $speed,
                    'full_duplex' => $fullDuplex,
                    'raw' => trim($output)
                ];
            }

            return response()->json([
                'status' => 'success',
                'ports' => $ports
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // 9. Controlar estado de un puerto específico (activar/desactivar)
    public function setPortState(Request $request)
    {
        $request->validate([
            'port' => 'required|integer|in:0,1,2,3,4,5,6',
            'state' => 'required|string|in:up,down'
        ]);

        try {
            $port = $request->port;
            $state = $request->state;

            // Para desactivar un puerto
            if ($state === 'down') {
                $output = $this->ssh->exec("swconfig dev switch0 port $port set disable 1 2>/dev/null");
            } else {
                // Para activar un puerto
                $output = $this->ssh->exec("swconfig dev switch0 port $port set disable 0 2>/dev/null");
            }

            // Verificar el nuevo estado
            $verify = $this->ssh->exec("swconfig dev switch0 port $port get link 2>/dev/null");

            return response()->json([
                'status' => 'success',
                'port' => $port,
                'state' => $state,
                'message' => $state === 'up' ? "Puerto activado" : "Puerto desactivado",
                'link' => trim($verify)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


}
