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

    // ==================== EQUIPO JONA ====================
    // Métodos agregados para el Dashboard

    /**
     * Obtener información completa del sistema
     */
    public function getSystemInfo(): array
    {
        return [
            'hostname' => trim($this->exec("uci get system.@system[0].hostname 2>/dev/null")),
            'time' => trim($this->exec("date 2>/dev/null")),
            'uptime' => trim($this->exec("uptime 2>/dev/null")),
            'load' => trim($this->exec("cat /proc/loadavg 2>/dev/null | cut -d' ' -f1-3")),
            'memory' => $this->getMemoryInfo(),
            'cpu' => $this->getCpuInfo(),
            'interfaces' => $this->getNetworkInterfaces(),
            'kernel' => trim($this->exec("uname -r 2>/dev/null")),
            'model' => trim($this->exec("cat /tmp/sysinfo/model 2>/dev/null")),
        ];
    }

    /**
     * Obtener información de memoria
     */
    protected function getMemoryInfo(): array
    {
        $output = $this->exec("free -m 2>/dev/null | grep -E '^Mem:|^Swap:'");
        $lines = explode("\n", trim($output));
        $memory = [];

        foreach ($lines as $line) {
            if (preg_match('/^(\w+):\s+(\d+)\s+(\d+)\s+(\d+)/', $line, $matches)) {
                $memory[strtolower($matches[1])] = [
                    'total' => (int) $matches[2],
                    'used' => (int) $matches[3],
                    'free' => (int) $matches[4],
                ];
            }
        }

        return $memory;
    }

    /**
     * Obtener información de CPU
     */
    protected function getCpuInfo(): string
    {
        return trim($this->exec("grep -m 1 'model name' /proc/cpuinfo 2>/dev/null | cut -d':' -f2"));
    }

    /**
     * Obtener interfaces de red
     */
    protected function getNetworkInterfaces(): array
    {
        $output = $this->exec("ip -br link 2>/dev/null | grep -v LOOPBACK | grep -v '@'");
        $lines = explode("\n", trim($output));
        $interfaces = [];

        foreach ($lines as $line) {
            if (preg_match('/^(\S+)\s+(\S+)/', $line, $matches)) {
                $interfaces[] = [
                    'name' => $matches[1],
                    'status' => $matches[2],
                ];
            }
        }

        return $interfaces;
    }
}
