<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use phpseclib3\Net\SSH2;

class Router4Controller extends Controller
{
    protected $ip;
    protected $user;
    protected $password;

    public function __construct()
    {
        $this->ip = env('ROUTER_IP', '192.168.10.1');
        $this->user = env('ROUTER_USER', 'root');
        $this->password = env('ROUTER_PASSWORD', '');
    }

    protected function execCommand($command)
    {
        try {
            $ssh = new SSH2($this->ip);
            if (!$ssh->login($this->user, $this->password)) {
                return ['error' => 'Error de autenticación SSH'];
            }
            $output = $ssh->exec($command);
            $ssh->disconnect();
            return ['success' => true, 'output' => $output];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // ==================== TAREAS PROGRAMADAS ====================
    public function tareas()
    {
        $result = $this->execCommand("crontab -l 2>/dev/null");
        $tareas = ($result['success'] ?? false) ? $result['output'] : '';

        return view('Tareas_programadas.tareas', compact('tareas'));
    }

    public function guardarTareas(Request $request)
    {
        $tareas = $request->input('tareas', '');

        // Guardar tareas
        $comando = "echo " . escapeshellarg($tareas) . " | crontab - 2>&1";
        $this->execCommand($comando);

        // Reiniciar cron
        $this->execCommand("/etc/init.d/cron restart");

        return redirect()->route('router4.tareas')->with('success', '✅ Tareas programadas guardadas correctamente');
    }

    // ==================== CONFIGURACIÓN LEDs ====================
    public function leds()
    {
        $leds = ['green:lan', 'green:wan', 'green:wlan', 'mt76-phy0', 'orange:wan'];
        $estados = [];

        foreach ($leds as $led) {
            $brillo = $this->execCommand("cat /sys/class/leds/{$led}/brightness 2>/dev/null || echo '0'");
            $trigger = $this->execCommand("cat /sys/class/leds/{$led}/trigger 2>/dev/null | sed 's/.*\\[//; s/\\].*//'");

            $estados[$led] = [
                'brillo' => trim(($brillo['success'] ?? false) ? $brillo['output'] : '0'),
                'trigger' => trim(($trigger['success'] ?? false) ? $trigger['output'] : 'none')
            ];
        }

        return view('Configuracion_led.led', compact('estados', 'leds'));
    }

    public function encenderLed(Request $request)
    {
        $led = $request->input('led');
        $this->execCommand("echo 255 > /sys/class/leds/{$led}/brightness 2>&1");
        return redirect()->route('router4.leds')->with('success', "✅ LED {$led} encendido");
    }

    public function apagarLed(Request $request)
    {
        $led = $request->input('led');
        $this->execCommand("echo 0 > /sys/class/leds/{$led}/brightness 2>&1");
        return redirect()->route('router4.leds')->with('success', "✅ LED {$led} apagado");
    }

    public function configurarTrigger(Request $request)
    {
        $led = $request->input('led');
        $trigger = $request->input('trigger');
        $this->execCommand("echo {$trigger} > /sys/class/leds/{$led}/trigger 2>&1");
        return redirect()->route('router4.leds')->with('success', "✅ LED {$led} configurado como: {$trigger}");
    }

    // ==================== COPIA DE SEGURIDAD ====================
    public function copia()
    {
        return view('Copia_seguridad.copia');
    }

    public function descargarBackup()
    {
        // Crear backup
        $this->execCommand("sysupgrade -b /tmp/backup.tar.gz 2>&1");

        // Verificar existencia
        $check = $this->execCommand("ls /tmp/backup.tar.gz 2>/dev/null");

        if (strpos(($check['output'] ?? ''), 'backup.tar.gz') !== false) {
            // Leer archivo
            $backup = $this->execCommand("cat /tmp/backup.tar.gz 2>/dev/null");

            if (isset($backup['success']) && !empty($backup['output'])) {
                $fecha = date('Y-m-d_H-i-s');
                $nombre = "backup_router_{$fecha}.tar.gz";

                // Limpiar buffer
                ob_clean();

                // Headers para descarga
                header('Content-Type: application/gzip');
                header('Content-Disposition: attachment; filename="' . $nombre . '"');
                header('Cache-Control: private');
                header('Content-Length: ' . strlen($backup['output']));

                echo $backup['output'];

                // Limpiar archivo temporal
                $this->execCommand("rm -f /tmp/backup.tar.gz");
                exit;
            }
        }

        return redirect()->route('router4.copia')->with('error', '❌ No se pudo crear el backup');
    }

    public function restaurarBackup(Request $request)
    {
        $request->validate([
            'backup' => 'required|file|mimes:gz,tar.gz|max:10240'
        ]);

        $archivo = $request->file('backup');
        $contenido = file_get_contents($archivo->getPathname());

        // Guardar archivo en el router via SSH
        $tempFile = tempnam(sys_get_temp_dir(), 'backup_');
        file_put_contents($tempFile, $contenido);

        $scpCommand = "scp -o StrictHostKeyChecking=no {$tempFile} {$this->user}@{$this->ip}:/tmp/restore.tar.gz 2>&1";
        shell_exec($scpCommand);

        unlink($tempFile);

        // Restaurar backup
        $this->execCommand("sysupgrade -r /tmp/restore.tar.gz 2>&1");

        return redirect()->route('router4.copia')->with('success', '🔄 Backup restaurado. El router se reiniciará.');
    }

    public function resetFabrica(Request $request)
    {
        $confirm = $request->input('confirm', false);

        if ($confirm === 'true' || $confirm === true) {
            $this->execCommand("firstboot -y && reboot");
            return redirect()->route('router4.copia')->with('warning', '⚠️ Restableciendo a valores de fábrica. El router se reiniciará.');
        }

        return redirect()->route('router4.copia')->with('error', '❌ No se confirmó el restablecimiento');
    }

    public function grabarFirmware(Request $request)
    {
        $request->validate([
            'firmware' => 'required|file|mimes:bin|max:20480'
        ]);

        $archivo = $request->file('firmware');
        $contenido = file_get_contents($archivo->getPathname());

        $tempFile = tempnam(sys_get_temp_dir(), 'firmware_');
        file_put_contents($tempFile, $contenido);

        $scpCommand = "scp -o StrictHostKeyChecking=no {$tempFile} {$this->user}@{$this->ip}:/tmp/firmware.bin 2>&1";
        shell_exec($scpCommand);

        unlink($tempFile);

        // Grabar firmware
        $this->execCommand("sysupgrade /tmp/firmware.bin 2>&1");

        return redirect()->route('router4.copia')->with('warning', '🔥 Grabando firmware. El router se reiniciará.');
    }
}
