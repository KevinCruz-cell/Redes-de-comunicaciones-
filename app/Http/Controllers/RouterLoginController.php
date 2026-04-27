<?php

namespace App\Http\Controllers;

use App\Services\RouterService;
use Illuminate\Http\Request;

class RouterLoginController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function testConnection(RouterService $routerService)
    {
        $result = $routerService->isReachable();
        return view('router-test', compact('result'));
    }

    public function authenticate(Request $request, RouterService $routerService)
    {
        $request->validate([
            'usuario' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $reachable = $routerService->isReachable();

        if (! $reachable['ok']) {
            return back()->withErrors([
                'router' => 'No se detecto el router en 192.168.10.1. Revisa el cable Ethernet y tu red.'
            ])->withInput();
        }

        $login = $routerService->login(
            $request->usuario,
            $request->password
        );

        if (! $login['ok']) {
            return back()->withErrors([
                'password' => $login['message']
            ])->withInput();
        }

        session([
            'router_logged_in' => true,
            'router_ip' => env('ROUTER_IP', '192.168.10.1'),
            'router_user' => $request->usuario,
            'router_sysauth' => $login['data']['sysauth'] ?? null,
            'router_location' => $login['data']['location'] ?? null,
        ]);

        return redirect('/dhcp');
    }

    public function logout(Request $request)
    {
        $request->session()->forget([
            'router_logged_in',
            'router_ip',
            'router_user',
            'router_sysauth',
            'router_location',
        ]);

        return redirect('/login');
    }
}
