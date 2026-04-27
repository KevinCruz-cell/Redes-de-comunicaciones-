<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NuupNet - @yield('title')</title>
    @vite(['public/css/style.css'])
</head>
<body>
<header class="navbar-top">
    <div class="navbar-brand">
        <a href="{{ url('/') }}" class="brand-link">
            <img src="{{ asset('nuupwisp.png') }}" alt="NuupNet" class="logo-small">
            <span class="brand-text">NuupNet</span>
        </a>
    </div>
    <div class="navbar-actions">
        <button class="btn-refresh" onclick="location.reload()">REFRESCAR</button>
    </div>
</header>

<div class="container">
    <aside class="sidebar">
        <nav class="sidebar-nav">
            <div class="menu-group {{ request()->is('estado*') ? 'expanded' : '' }}">
                <button class="group-toggle" type="button">
                    Estado <span class="arrow">﹀</span>
                </button>
                <ul class="submenu">
                    <li><a href="#" class="{{ request()->is('vision-general') ? 'active' : '' }}">Visión general</a></li>
                    <li><a href="#">Cortafuegos</a></li>
                    <li><a href="#">Rutas</a></li>
                    <li><a href="#">Registro del sistema</a></li>
                    <li><a href="#">Registro del núcleo</a></li>
                    <li><a href="#">Procesos</a></li>
                    <li><a href="#">Gráficos en tiempo real</a></li>
                </ul>
            </div>

            <div class="menu-group {{ request()->is('sistema*') ? 'expanded' : '' }}">
                <button class="group-toggle" type="button">
                    Sistema <span class="arrow">﹀</span>
                </button>
                <ul class="submenu">
                    <li><a href="#">Sistema</a></li>
                    <li><a href="#">Administración</a></li>
                    <li><a href="#">Arranque</a></li>
                    <li><a href="#">Tareas programadas</a></li>
                    <li><a href="#">Configuración de LEDs</a></li>
                    <li><a href="#">Copia de seguridad / Grabar firmware</a></li>
                    <li><a href="#">Reiniciar</a></li>
                </ul>
            </div>

            <div class="menu-group {{ request()->is('red*') || request()->is('dhcp*') || request()->is('hosts*') ? 'expanded' : '' }}">
                <button class="group-toggle" type="button">
                    Red <span class="arrow">︿</span>
                </button>
                <ul class="submenu">
                    <li><a href="#">Interfaces</a></li>
                    <li><a href="#">Wi-Fi</a></li>
                    <li><a href="#">Conmutador</a></li>
                    <li class="{{ request()->is('dhcp*') ? 'active-item' : '' }}">
                        <a href="/dhcp">DHCP y DNS</a>
                    </li>
                    <li class="{{ request()->is('hosts*') ? 'active-item' : '' }}">
                        <a href="/nombres-host">Nombres de host</a>
                    </li>
                    <li><a href="#">Rutas estáticas</a></li>
                    <li><a href="#">Diagnósticos</a></li>
                </ul>
            </div>
        </nav>

        <div class="sidebar-footer">
            <form action="{{ route('router.logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    <span class="icon">➡️</span> Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    <main class="main-body">
        <div class="page-header">
            <h2>@yield('header_title')</h2>
        </div>

        <section class="content">
            @yield('content')
        </section>

        <footer class="luci-version">
            Powered by LuCI openwrt-19.07 branch / OpenWrt 19.07.9
        </footer>
    </main>
</div>
</body>
<script>

    document.addEventListener('DOMContentLoaded', function() {
        // Seleccionamos todos los botones que activan el menú
        const toggles = document.querySelectorAll('.group-toggle');

        toggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                // Buscamos el contenedor padre (menu-group)
                const parent = this.parentElement;

                // OPCIONAL: Cerrar los otros menús abiertos (efecto acordeón único)
                /*
                document.querySelectorAll('.menu-group').forEach(group => {
                    if (group !== parent) {
                        group.classList.remove('expanded');
                        const arrow = group.querySelector('.arrow');
                        if(arrow) arrow.textContent = '﹀';
                    }
                });
                */

                // Alternamos la clase 'expanded' en el grupo actual
                parent.classList.toggle('expanded');

                // Cambiamos la flechita según el estado
                const arrow = this.querySelector('.arrow');
                if (parent.classList.contains('expanded')) {
                    arrow.textContent = '︿'; // Flecha hacia arriba
                } else {
                    arrow.textContent = '﹀'; // Flecha hacia abajo
                }
            });
        });
    });

</script>
</html>
