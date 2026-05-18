<?php

namespace App\Services;

use phpseclib3\Net\SSH2;
use Exception;

class SystemRouterService
{
    protected string $routerIp;
    protected int $sshPort;
    protected string $sshUser;
    protected string $sshPassword;

    public function __construct()
    {
        $this->routerIp = env('ROUTER_IP', '192.168.10.1');
        $this->sshPort = (int) env('ROUTER_PORT', 22);
        $this->sshUser = env('ROUTER_USER', 'root');
        $this->sshPassword = env('ROUTER_PASSWORD', '');
    }

    public function exec(string $command): string
    {
        $ssh = new SSH2($this->routerIp, $this->sshPort);

        if (!$ssh->login($this->sshUser, $this->sshPassword)) {
            throw new Exception('No se pudo conectar por SSH al router. Revisa usuario o contrasena.');
        }

        return $ssh->exec($command);
    }
}
