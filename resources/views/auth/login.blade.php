<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Router - NuupNet</title>

    @vite('resources/css/login.css')
</head>
<body>
<header class="topbar">
    <div class="brand">
        <div class="logo">NUUP</div>
        <span>NuupNet</span>
        <span class="brand-secondary">NuupNet</span>
    </div>
</header>

<main class="wrapper">
    <section class="login-box">
        <div class="warning">
            <div class="warning-header">
                <strong>¡Sin contraseña!</strong>
                <a href="/" class="back-link" title="Volver al inicio">← Regresar</a>
            </div>
            <p>No hay ninguna contraseña establecida en este enrutador. Configure una contraseña de root para proteger la interfaz web.</p>
        </div>

        <h1>Autorizacion requerida</h1>
        <hr class="divider" />
        <p class="subtitle">Por favor, introduzca su nombre de usuario y contraseña.</p>

        <form class="form-area" method="POST" action="{{ route('router.login') }}">
            @csrf

            <div class="form-row">
                <label for="usuario">Nombre de usuario</label>
                <input type="text" id="usuario" name="usuario" value="{{ old('usuario', 'root') }}" />
            </div>

            <div class="form-row">
                <label for="password">Contrasena</label>
                <input type="password" id="password" name="password" />
            </div>

            @if ($errors->any())
                <p class="mensaje" style="display:block; color:#c62828;">
                    {{ $errors->first() }}
                </p>
            @endif

            <div class="actions">
                <button type="submit" class="btn-login">Iniciar sesion</button>
                <button type="reset" class="btn-reset">Restablecer</button>
            </div>
        </form>
    </section>
</main>

<div class="footer">
    Powered by LuCI openwrt-19.07 branch (git-22.045.73925-36e5c1c) / OpenWrt 19.07.9 r11405-2a3558b0de
</div>

</body>
</html>
