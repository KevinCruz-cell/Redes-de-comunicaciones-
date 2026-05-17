<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use phpseclib3\Net\SSH2;

class RouterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SSH2::class, function ($app) {
            // El segundo parámetro es un valor por defecto si no existen en el .env
            $host = env('ROUTER_HOST', '192.168.10.1');
            $port = env('ROUTER_PORT', 22);
            $user = env('ROUTER_USER', 'root');
            $pass = env('ROUTER_PASSWORD', '');

            $ssh = new SSH2($host, $port);

            if (!$ssh->login($user, $pass)) {
                throw new \Exception('No se pudo establecer la conexión SSH con el Router.');
            }

            return $ssh;
        });
    }

    public function boot(): void
    {
        //
    }
}
