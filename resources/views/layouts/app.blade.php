<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NuupNet - @yield('title')</title>
    @vite(['resources/css/app.css']) </head>
<body>
<div class="container">
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('logo_nuupnet.png') }}" alt="NuupNet" class="logo">
        </div>

        <nav class="sidebar-nav">
            <div class="menu-group">
                <div class="group-title">Estado <span class="arrow">^</span></div>
                <ul>
                    <li><a href="#">Visión general</a></li>
                    <li><a href="#">Cortafuegos</a></li>
                    <li><a href="#">Rutas</a></li>
                    <li><a href="#">Registro del sistema</a></li>
                    <li><a href="#">Registro del núcleo</a></li>
                    <li><a href="#">Procesos</a></li>
                    <li><a href="#">Gráficos en tiempo real</a></li>
                </ul>
            </div>

            <div class="menu-group">
                <div class="group-title">Sistema <span class="arrow">^</span></div>
                <ul>
                    <li><a href="#">Sistema</a></li>
                    <li><a href="#">Administración</a></li>
                    <li><a href="#">Arranque</a></li>
                    <li><a href="#">Tareas programadas</a></li>
                    <li><a href="#">Configuración de LEDs</a></li>
                    <li><a href="#">Copia de seguridad / Grabar firmware</a></li>
                    <li><a href="#">Reiniciar</a></li>
                </ul>
            </div>

            <div class="menu-group">
                <div class="group-title">Red <span class="arrow">^</span></div>
                <ul>
                    <li><a href="#">Interfaces</a></li>
                    <li><a href="#">Wi-Fi</a></li>
                    <li><a href="#">Conmutador</a></li>
                    <li class="{{ request()->is('dhcp*') ? 'active-item' : '' }}">
                        <a href="/dhcp">DHCP y DNS</a>
                    </li>
                    <li><a href="#">Nombres de host</a></li>
                    <li><a href="#">Rutas estáticas</a></li>
                    <li><a href="#">Diagnósticos</a></li>
                </ul>
            </div>
        </nav>

        <div class="sidebar-footer">
            <a href="/login" class="logout">
                <span class="icon">➡️</span> Cerrar sesión
            </a>
        </div>
    </aside>

    <main class="main-body">
        <header class="top-header">
            <h1>@yield('header_title')</h1>
            <button class="refresh-btn" onclick="location.reload()">🔄</button>
        </header>

        <section class="content">
            @yield('content')
        </section>
    </main>
</div>
</body>
</html>
