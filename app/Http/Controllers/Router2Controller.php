<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use phpseclib3\Net\SSH2;

class Router2Controller extends Controller
{
    // CONFIGURACIÓN GLOBAL - EDITA ESTO
    private $ip = '192.168.10.1';
    private $user = 'root';
    private $password = 'TU_CONTRASEÑA_REAL'; // Pon aquí tu contraseña real

    public function gestionRed()
    {
        $interfaces = session('interfaces', [
            ['name' => 'LAN', 'device' => 'br-lan', 'protocol' => 'Dirección estática', 'mac' => 'EC:75:0C:48:B9:01', 'ipv4' => '192.168.10.1/24', 'color' => 'green'],
            ['name' => 'WAN', 'device' => 'eth0.2', 'protocol' => 'Cliente DHCP', 'mac' => '28:EE:52:29:AC:DF', 'ipv4' => '0.0.0.0', 'color' => 'red'],
        ]);
        $dispositivos = $this->getDispositivos();
        return view('Interfaces.interfaces', compact('interfaces', 'dispositivos'));
    }

    public function reiniciar()
    {
        try {
            $ssh = new SSH2($this->ip);
            if (!$ssh->login($this->user, $this->password)) {
                return back()->with('error', 'Contraseña de SSH incorrecta.');
            }
            $ssh->exec('reinicio');
            return back()->with('success', 'El router se está reiniciando. Espera 1 minuto.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error de conexión (10060): El router no responde.');
        }
    }

    public function crearInterfaz(Request $request)
    {
        $nombre = strtolower($request->input('nombre'));
        $proto = $request->input('proto');
        $ifname = $request->input('ifname');
        $bridge = $request->has('bridge');

        try {
            $ssh = new SSH2($this->ip);
            if ($ssh->login($this->user, $this->password)) {
                $ssh->exec("uci set network.{$nombre}=interface");
                $ssh->exec("uci set network.{$nombre}.proto='{$proto}'");
                $ssh->exec("uci set network.{$nombre}.ifname='{$ifname}'");
                if ($bridge) {
                    $ssh->exec("uci set network.{$nombre}.type='bridge'");
                }
                if ($proto == 'static') {
                    if ($request->filled('ipaddr')) $ssh->exec("uci set network.{$nombre}.ipaddr='{$request->ipaddr}'");
                    if ($request->filled('netmask')) $ssh->exec("uci set network.{$nombre}.netmask='{$request->netmask}'");
                    if ($request->filled('gateway')) $ssh->exec("uci set network.{$nombre}.gateway='{$request->gateway}'");
                    if ($request->filled('dns')) $ssh->exec("uci set network.{$nombre}.dns='{$request->dns}'");
                }
                $ssh->exec("uci commit network");
                $ssh->exec("(sleep 2 && /etc/init.d/network reload) > /dev/null 2>&1 &");
                $ssh->disconnect();

                $interfaces = session('interfaces', []);
                $interfaces[] = [
                    'name' => strtoupper($nombre),
                    'device' => $ifname,
                    'protocol' => ($proto == 'dhcp') ? 'Cliente DHCP' : 'Dirección estática',
                    'mac' => '00:00:00:00:00:00',
                    'ipv4' => 'Pendiente...',
                    'color' => 'blue'
                ];
                session(['interfaces' => $interfaces]);
                return back()->with('success', "Interfaz {$nombre} creada correctamente.");
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear: ' . $e->getMessage());
        }
    }

    public function eliminarInterfaz($name)
    {
        try {
            $ssh = new SSH2($this->ip);
            if ($ssh->login($this->user, $this->password)) {
                $nombreLow = strtolower($name);
                $ssh->exec("uci delete network.$nombreLow");
                $ssh->exec("uci commit network");
                $ssh->exec("(sleep 2 && /etc/init.d/network reload) > /dev/null 2>&1 &");
                $ssh->disconnect();

                $interfaces = session('interfaces', []);
                $nuevasInterfaces = array_filter($interfaces, function($iface) use ($name) {
                    return $iface['name'] !== strtoupper($name);
                });
                session(['interfaces' => $nuevasInterfaces]);
                return back()->with('success', "Interfaz $name eliminada.");
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    public function refrescar()
    {
        session()->forget('interfaces');
        return redirect()->route('router2.interfaces')->with('success', 'Datos de sesión limpiados.');
    }

    // ==================== NUEVOS MÉTODOS ====================
    private function getDispositivos()
    {
        try {
            $ssh = new SSH2($this->ip);
            if (!$ssh->login($this->user, $this->password)) {
                return ['eth0', 'eth0.2', 'br-lan'];
            }
            $output = $ssh->exec("ls /sys/class/net | grep -v lo");
            $ssh->disconnect();
            $devices = array_filter(explode("\n", trim($output)));
            return !empty($devices) ? $devices : ['eth0', 'eth0.2', 'br-lan'];
        } catch (\Exception $e) {
            return ['eth0', 'eth0.2', 'br-lan'];
        }
    }

    public function reiniciarInterfaz($iface)
    {
        try {
            $ssh = new SSH2($this->ip);
            if (!$ssh->login($this->user, $this->password)) {
                return back()->with('error', 'Contraseña SSH incorrecta.');
            }
            $ssh->exec("ifdown $iface 2>/dev/null; sleep 1; ifup $iface 2>/dev/null");
            $ssh->disconnect();
            return back()->with('success', "Interfaz $iface reiniciada.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al reiniciar: ' . $e->getMessage());
        }
    }

    public function detenerInterfaz($iface)
    {
        try {
            $ssh = new SSH2($this->ip);
            if (!$ssh->login($this->user, $this->password)) {
                return back()->with('error', 'Contraseña SSH incorrecta.');
            }
            $ssh->exec("ifdown $iface 2>/dev/null");
            $ssh->disconnect();
            return back()->with('success', "Interfaz $iface detenida.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al detener: ' . $e->getMessage());
        }
    }

    public function editarInterfaz($iface)
    {
        try {
            $ssh = new SSH2($this->ip);
            if (!$ssh->login($this->user, $this->password)) {
                return response()->json(['error' => 'SSH login failed'], 401);
            }
            $data = [
                'proto' => trim($ssh->exec("uci get network.{$iface}.proto 2>/dev/null")),
                'ifname' => trim($ssh->exec("uci get network.{$iface}.ifname 2>/dev/null")),
                'ipaddr' => trim($ssh->exec("uci get network.{$iface}.ipaddr 2>/dev/null")),
                'netmask' => trim($ssh->exec("uci get network.{$iface}.netmask 2>/dev/null")),
                'gateway' => trim($ssh->exec("uci get network.{$iface}.gateway 2>/dev/null")),
                'dns' => trim($ssh->exec("uci get network.{$iface}.dns 2>/dev/null")),
            ];
            $ssh->disconnect();
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function actualizarInterfaz(Request $request, $iface)
    {
        try {
            $ssh = new SSH2($this->ip);
            if (!$ssh->login($this->user, $this->password)) {
                return back()->with('error', 'Contraseña SSH incorrecta.');
            }
            $proto = $request->input('proto');
            $ssh->exec("uci set network.{$iface}.proto='$proto'");
            if ($proto == 'static') {
                if ($request->filled('ipaddr')) $ssh->exec("uci set network.{$iface}.ipaddr='{$request->ipaddr}'");
                if ($request->filled('netmask')) $ssh->exec("uci set network.{$iface}.netmask='{$request->netmask}'");
                if ($request->filled('gateway')) $ssh->exec("uci set network.{$iface}.gateway='{$request->gateway}'");
                if ($request->filled('dns')) $ssh->exec("uci set network.{$iface}.dns='{$request->dns}'");
            } else {
                $ssh->exec("uci del network.{$iface}.ipaddr 2>/dev/null");
                $ssh->exec("uci del network.{$iface}.netmask 2>/dev/null");
                $ssh->exec("uci del network.{$iface}.gateway 2>/dev/null");
                $ssh->exec("uci del network.{$iface}.dns 2>/dev/null");
            }
            $ssh->exec("uci commit network");
            $ssh->exec("(sleep 2 && /etc/init.d/network reload) > /dev/null 2>&1 &");
            $ssh->disconnect();
            return back()->with('success', "Interfaz $iface actualizada.");
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }
}
