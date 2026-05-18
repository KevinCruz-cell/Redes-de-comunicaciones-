<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SystemRouterService;

class AdminController extends Controller
{
    public function index()
    {
        return view('system.administracion');
    }

    public function getData(SystemRouterService $router)
    {
        $rootPasswordAuth = $this->safeExec($router, "uci get dropbear.@dropbear[0].RootPasswordAuth");

        if ($rootPasswordAuth === '') {
            $rootPasswordAuth = $this->safeExec($router, "uci get dropbear.@dropbear[0].RootLogin");
        }

        return response()->json([
            'port' => $this->safeExec($router, "uci get dropbear.@dropbear[0].Port") ?: '22',
            'passAuth' => $this->safeExec($router, "uci get dropbear.@dropbear[0].PasswordAuth") ?: 'on',
            'rootLogin' => $rootPasswordAuth ?: 'on',
            'gateway' => $this->safeExec($router, "uci get dropbear.@dropbear[0].GatewayPorts") ?: 'off',
            'interface' => $this->safeExec($router, "uci get dropbear.@dropbear[0].Interface") ?: '',
        ]);
    }

    public function updateSSH(Request $request, SystemRouterService $router)
    {
        try {
            $port = (int) $request->input('port', 22);

            if ($port < 1 || $port > 65535) {
                return response()->json([
                    'ok' => false,
                    'error' => 'El puerto SSH debe estar entre 1 y 65535.'
                ], 422);
            }

            $passAuth = $request->boolean('passAuth') ? 'on' : 'off';
            $rootLogin = $request->boolean('rootLogin') ? 'on' : 'off';
            $gateway = $request->boolean('gateway') ? 'on' : 'off';
            $interface = trim((string) $request->input('interface', ''));

            $cmd = "";
            $cmd .= "uci set dropbear.@dropbear[0].Port=" . escapeshellarg((string) $port) . "; ";
            $cmd .= "uci set dropbear.@dropbear[0].PasswordAuth=" . escapeshellarg($passAuth) . "; ";

            // En OpenWrt moderno se usa RootPasswordAuth.
            $cmd .= "uci set dropbear.@dropbear[0].RootPasswordAuth=" . escapeshellarg($rootLogin) . "; ";

            // Por compatibilidad si tu router usa RootLogin.
            $cmd .= "uci set dropbear.@dropbear[0].RootLogin=" . escapeshellarg($rootLogin === 'on' ? '1' : '0') . "; ";

            $cmd .= "uci set dropbear.@dropbear[0].GatewayPorts=" . escapeshellarg($gateway) . "; ";

            if ($interface !== '') {
                $cmd .= "uci set dropbear.@dropbear[0].Interface=" . escapeshellarg($interface) . "; ";
            } else {
                $cmd .= "uci delete dropbear.@dropbear[0].Interface 2>/dev/null; ";
            }

            $cmd .= "uci commit dropbear; ";
            $cmd .= "/etc/init.d/dropbear restart; ";

            $router->exec($cmd);

            return response()->json([
                'ok' => true,
                'message' => 'Configuracion SSH actualizada correctamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request, SystemRouterService $router)
    {
        try {
            $password = (string) $request->input('password', '');

            if ($password === '') {
                return response()->json([
                    'ok' => false,
                    'error' => 'La contrasena no puede estar vacia.'
                ], 422);
            }

            if (strlen($password) < 4) {
                return response()->json([
                    'ok' => false,
                    'error' => 'La contrasena debe tener minimo 4 caracteres.'
                ], 422);
            }

            $escapedPassword = escapeshellarg($password);

            $cmd = "printf '%s\n%s\n' {$escapedPassword} {$escapedPassword} | passwd root";

            $router->exec($cmd);

            return response()->json([
                'ok' => true,
                'message' => 'Contrasena del enrutador actualizada correctamente. Actualiza tambien ROUTER_PASSWORD en tu .env si cambiaste la clave real.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadKey(Request $request, SystemRouterService $router)
    {
        try {
            if (!$request->hasFile('ssh_key')) {
                return response()->json([
                    'ok' => false,
                    'error' => 'No seleccionaste ningun archivo.'
                ], 400);
            }

            $file = $request->file('ssh_key');
            $content = trim(file_get_contents($file->getRealPath()));

            if ($content === '') {
                return response()->json([
                    'ok' => false,
                    'error' => 'El archivo de clave esta vacio.'
                ], 422);
            }

            $isValidKey =
                str_starts_with($content, 'ssh-rsa ') ||
                str_starts_with($content, 'ssh-ed25519 ') ||
                str_starts_with($content, 'ecdsa-sha2-nistp256 ') ||
                str_starts_with($content, 'ecdsa-sha2-nistp384 ') ||
                str_starts_with($content, 'ecdsa-sha2-nistp521 ');

            if (!$isValidKey) {
                return response()->json([
                    'ok' => false,
                    'error' => 'El archivo no parece ser una clave publica SSH valida. Debe iniciar con ssh-rsa, ssh-ed25519 o ecdsa-sha2.'
                ], 422);
            }

            $content = str_replace(["\r", "\n"], '', $content);
            $escapedKey = escapeshellarg($content);

            $cmd = "";
            $cmd .= "mkdir -p /etc/dropbear; ";
            $cmd .= "touch /etc/dropbear/authorized_keys; ";
            $cmd .= "grep -qxF {$escapedKey} /etc/dropbear/authorized_keys || printf '%s\n' {$escapedKey} >> /etc/dropbear/authorized_keys; ";
            $cmd .= "chmod 600 /etc/dropbear/authorized_keys; ";
            $cmd .= "/etc/init.d/dropbear restart; ";

            $router->exec($cmd);

            return response()->json([
                'ok' => true,
                'message' => 'Clave SSH publica agregada correctamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

public function getKeys(SystemRouterService $router)
{
    try {
        $content = $this->safeExec($router, "cat /etc/dropbear/authorized_keys 2>/dev/null");

        $keys = [];

        if ($content !== '') {
            $lines = preg_split('/\r\n|\r|\n/', trim($content));

            foreach ($lines as $index => $line) {
                $line = trim($line);

                if ($line === '') {
                    continue;
                }

                $parts = preg_split('/\s+/', $line);

                $type = $parts[0] ?? 'ssh-key';
                $keyBody = $parts[1] ?? '';
                $comment = $parts[2] ?? 'Sin comentario';

                $bits = match ($type) {
                    'ssh-rsa' => 'RSA',
                    'ssh-ed25519' => 'ED25519',
                    'ecdsa-sha2-nistp256',
                    'ecdsa-sha2-nistp384',
                    'ecdsa-sha2-nistp521' => 'ECDSA',
                    default => 'SSH'
                };

                $keys[] = [
                    'id' => $index,
                    'type' => $type,
                    'label' => $comment,
                    'bits' => $bits,
                    'preview1' => substr($keyBody, 0, 28) . '...',
                    'preview2' => substr($keyBody, -28),
                    'full' => $line,
                ];
            }
        }

        return response()->json([
            'ok' => true,
            'keys' => $keys
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'ok' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

public function deleteKey(Request $request, SystemRouterService $router)
{
    try {
        $key = trim((string) $request->input('key', ''));

        if ($key === '') {
            return response()->json([
                'ok' => false,
                'error' => 'No se recibio la clave a eliminar.'
            ], 422);
        }

        $escapedKey = escapeshellarg($key);

        $cmd = "";
        $cmd .= "touch /etc/dropbear/authorized_keys; ";
        $cmd .= "grep -vxF {$escapedKey} /etc/dropbear/authorized_keys > /tmp/authorized_keys.tmp; ";
        $cmd .= "cat /tmp/authorized_keys.tmp > /etc/dropbear/authorized_keys; ";
        $cmd .= "rm -f /tmp/authorized_keys.tmp; ";
        $cmd .= "chmod 600 /etc/dropbear/authorized_keys; ";
        $cmd .= "/etc/init.d/dropbear restart; ";

        $router->exec($cmd);

        return response()->json([
            'ok' => true,
            'message' => 'Clave SSH eliminada correctamente.'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'ok' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

    private function safeExec(SystemRouterService $router, string $cmd): string
    {
        try {
            $result = trim($router->exec($cmd . " 2>/dev/null"));

            if (
                $result === '' ||
                str_contains($result, 'not found') ||
                str_contains($result, 'Entry not found')
            ) {
                return '';
            }

            return $result;

        } catch (\Exception $e) {
            return '';
        }
    }
}
