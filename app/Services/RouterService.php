<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class RouterService
{
    protected string $routerIp;
    protected int $timeout;

    public function __construct()
    {
        $this->routerIp = env('ROUTER_IP', '192.168.10.1');
        $this->timeout = (int) env('ROUTER_TIMEOUT', 5);
    }

    public function isReachable(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("http://{$this->routerIp}/cgi-bin/luci/");

            return [
                'ok' => true,
                'status' => $response->status(),
                'message' => 'El router respondio correctamente.'
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'status' => null,
                'message' => 'No se pudo conectar con el router.'
            ];
        }
    }

    public function login(string $username, string $password): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withOptions([
                    'allow_redirects' => false,
                ])
                ->asForm()
                ->post("http://{$this->routerIp}/cgi-bin/luci", [
                    'luci_username' => $username,
                    'luci_password' => $password,
                ]);

            $status = $response->status();
            $setCookie = $response->header('Set-Cookie');
            $location = $response->header('Location');

            $sysauth = $this->extractSysauthCookie($setCookie);

            if ($status === 302 && $sysauth) {
                return [
                    'ok' => true,
                    'message' => 'Autenticacion correcta.',
                    'data' => [
                        'sysauth' => $sysauth,
                        'location' => $location,
                        'status' => $status,
                    ],
                ];
            }

            return [
                'ok' => false,
                'message' => 'Usuario o contrasena incorrectos.',
                'data' => [
                    'status' => $status,
                    'location' => $location,
                    'set_cookie' => $setCookie,
                    'body' => $response->body(),
                ],
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'message' => 'No fue posible autenticar con el router.',
                'data' => [
                    'error' => $e->getMessage(),
                ],
            ];
        }
    }

    public function validateSession(string $sysauth): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Cookie' => "sysauth={$sysauth}",
                ])
                ->get("http://{$this->routerIp}/cgi-bin/luci/");

            $body = $response->body();

            if ($response->successful() && !str_contains($body, 'Autorizacion requerida')) {
                return [
                    'ok' => true,
                    'status' => $response->status(),
                    'message' => 'Sesion valida en el router.',
                ];
            }

            return [
                'ok' => false,
                'status' => $response->status(),
                'message' => 'La sesion del router no es valida.',
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'status' => null,
                'message' => 'No se pudo validar la sesion del router.',
            ];
        }
    }

    protected function extractSysauthCookie(?string $setCookie): ?string
    {
        if (!$setCookie) {
            return null;
        }

        if (preg_match('/sysauth=([^;]+)/', $setCookie, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
