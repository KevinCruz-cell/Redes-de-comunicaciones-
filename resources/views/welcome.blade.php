<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NuupNet</title>

    @vite('resources/css/welcome.css')
</head>
<body>
<div class="page">
    <header class="topbar">
        <div class="brand">
            <div class="logo">NUUP</div>
            <div class="brand-text">
                <h2>NuupNet</h2>
                <span>Panel de administracion de red</span>
            </div>
        </div>

        <a href="/login" class="btn btn-top">Iniciar sesion</a>
    </header>

    <main class="hero">
        <div class="hero-content">
            <p class="tag">Sistema de administracion</p>
            <h1>Bienvenido a NuupNet</h1>
            <p class="description">
                Administra servicios de red, configuraciones DHCP, DNS, interfaces,
                seguridad y monitoreo desde una interfaz clara, moderna y profesional.
            </p>

            <div class="hero-actions">
                <a href="/login" class="btn btn-primary">Iniciar sesion</a>

            </div>
        </div>

        <div class="hero-card">
            <div class="card-header">
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>

            <div class="card-body">
                <div class="card-title">Estado del sistema</div>

                <div class="status-item">
                    <span>Red local</span>
                    <strong>Activa</strong>
                </div>

                <div class="status-item">
                    <span>Servidor DHCP</span>
                    <strong>En ejecucion</strong>
                </div>

                <div class="status-item">
                    <span>Resolucion DNS</span>
                    <strong>Estable</strong>
                </div>

                <div class="status-item">
                    <span>Interfaz web</span>
                    <strong>Disponible</strong>
                </div>
            </div>
        </div>
    </main>

    <section class="info" id="info">
        <div class="info-card">
            <h3>Gestion centralizada</h3>
            <p>
                Controla configuraciones clave del sistema de red desde un solo panel
                para mejorar la administracion y el monitoreo.
            </p>
        </div>

        <div class="info-card">
            <h3>Interfaz intuitiva</h3>
            <p>
                Diseñada para ofrecer una experiencia visual clara, organizada y facil
                de usar en entornos academicos o profesionales.
            </p>
        </div>

        <div class="info-card">
            <h3>Acceso rapido</h3>
            <p>
                Ingresa al sistema y administra servicios como DHCP, DNS, rutas,
                interfaces y diagnosticos.
            </p>
        </div>
    </section>

    <footer class="footer">
        <p>NuupNet © 2026 - Plataforma de administracion de red</p>
    </footer>
</div>
</body>
</html>
