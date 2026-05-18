<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SystemRouterService;

class ArranqueController extends Controller
{
    public function index()
    {
        return view('system.arranque');
    }

    public function getData(SystemRouterService $router)
    {
        try {
            $raw = $router->exec("
                for f in /etc/init.d/*; do
                    [ -f \"\$f\" ] || continue;

                    svc=\$(basename \"\$f\");

                    priority=\$(ls /etc/rc.d/S*\$svc 2>/dev/null | head -n 1 | sed -E 's/.*S([0-9]+).*/\\1/');
                    [ -z \"\$priority\" ] && priority='-';

                    \"\$f\" enabled >/dev/null 2>&1;
                    enabled=\$?;

                    echo \"\$priority|\$svc|\$enabled\";
                done
            ");

            $services = [];

            foreach (explode("\n", trim($raw)) as $line) {
                $line = trim($line);

                if ($line === '' || !str_contains($line, '|')) {
                    continue;
                }

                [$priority, $name, $enabledCode] = array_pad(explode('|', $line), 3, '');

                $services[] = [
                    'priority' => trim($priority),
                    'name' => trim($name),
                    'enabled' => trim($enabledCode) === '0',
                ];
            }

            usort($services, function ($a, $b) {
                $pa = is_numeric($a['priority']) ? (int) $a['priority'] : 999;
                $pb = is_numeric($b['priority']) ? (int) $b['priority'] : 999;

                if ($pa === $pb) {
                    return strcmp($a['name'], $b['name']);
                }

                return $pa <=> $pb;
            });

            $localStartup = $router->exec("cat /etc/rc.local 2>/dev/null");

            return response()->json([
                'ok' => true,
                'services' => $services,
                'local_startup' => $localStartup,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function action(Request $request, SystemRouterService $router)
    {
        try {
            $service = (string) $request->input('service', '');
            $action = (string) $request->input('action', '');

            if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $service)) {
                return response()->json([
                    'ok' => false,
                    'error' => 'Nombre de servicio no valido.',
                ], 422);
            }

            $allowedActions = ['start', 'stop', 'restart', 'enable', 'disable'];

            if (!in_array($action, $allowedActions, true)) {
                return response()->json([
                    'ok' => false,
                    'error' => 'Accion no valida.',
                ], 422);
            }

            $cmd = "/etc/init.d/" . escapeshellarg($service) . " " . escapeshellarg($action);

            $router->exec($cmd);

            return response()->json([
                'ok' => true,
                'message' => 'Accion aplicada correctamente.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateLocalStartup(Request $request, SystemRouterService $router)
    {
        try {
            $content = (string) $request->input('local_startup', '');

            if (trim($content) === '') {
                $content = "# Put your custom commands here that should be executed once\n";
                $content .= "# the system init finished. By default this file does nothing.\n\n";
                $content .= "exit 0\n";
            }

            if (!str_contains($content, 'exit 0')) {
                $content = rtrim($content) . "\n\nexit 0\n";
            }

            /*
             * IMPORTANTE:
             * Ya no usamos base64 porque si falla, puede vaciar /etc/rc.local.
             * Primero escribimos a un archivo temporal y luego lo movemos.
             */
            $escapedContent = escapeshellarg($content);

            $cmd = "";
            $cmd .= "printf %s " . $escapedContent . " > /tmp/rc.local.new; ";
            $cmd .= "if [ -s /tmp/rc.local.new ]; then ";
            $cmd .= "mv /tmp/rc.local.new /etc/rc.local; ";
            $cmd .= "chmod +x /etc/rc.local; ";
            $cmd .= "else ";
            $cmd .= "rm -f /tmp/rc.local.new; ";
            $cmd .= "exit 1; ";
            $cmd .= "fi; ";

            $router->exec($cmd);

            $savedContent = $router->exec("cat /etc/rc.local 2>/dev/null");

            return response()->json([
                'ok' => true,
                'message' => 'Arranque local guardado correctamente.',
                'local_startup' => $savedContent,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
