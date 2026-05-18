<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Copia de Seguridad - NuupNet</title>
    <style>
        body { font-family: Arial; background: #eef2f7; margin: 0; padding: 20px; }
        .menu { background: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; }
        .menu a { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
        .menu a:hover { background: #0056b3; }
        .contenedor { background: white; padding: 30px; border-radius: 10px; max-width: 800px; margin: 0 auto; box-shadow: 0 0 15px rgba(0,0,0,0.2); }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .seccion { background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0; border: 1px solid #dee2e6; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        button.danger { background: #dc3545; }
        button.warning { background: #ffc107; color: #333; }
        button.success { background: #28a745; }
        button:hover { background: #0056b3; }
        .exito { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .aviso { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 5px; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>

<div class="menu">
    <a href="{{ route('router4.tareas') }}">⏰ Tareas Programadas</a>
    <a href="{{ route('router4.leds') }}">💡 Configuración LEDs</a>
    <a href="{{ route('router4.copia') }}">💾 Copia de Seguridad</a>
</div>

<div class="contenedor">
    <h1>💾 Copia de Seguridad / Firmware</h1>

    @if(session('success'))
        <div class="exito">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif
    @if(session('warning'))
        <div class="aviso">{{ session('warning') }}</div>
    @endif

    <div class="seccion">
        <h2>📦 Descargar copia</h2>
        <a href="{{ route('router4.backup.descargar') }}" class="success" style="background:#28a745;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block;">
            📥 GENERAR ARCHIVO
        </a>
    </div>

    <div class="seccion">
        <h2>🔄 Restaurar copia</h2>
        <form method="POST" enctype="multipart/form-data" action="{{ route('router4.backup.restaurar') }}">
            @csrf
            <input type="file" name="backup" accept=".gz,.tar.gz"><br><br>
            <button type="submit" class="warning">RESTAURAR</button>
        </form>
    </div>

    <div class="seccion">
        <h2>⚠️ Reset fábrica</h2>
        <form method="POST" action="{{ route('router4.reset') }}">
            @csrf
            <input type="hidden" name="confirm" value="true">
            <button type="submit" class="danger" onclick="return confirm('¿Estás seguro de restablecer a valores de fábrica? Esta acción no se puede deshacer.')">RESET</button>
        </form>
    </div>

    <div class="seccion">
        <h2>🔥 Firmware</h2>
        <form method="POST" enctype="multipart/form-data" action="{{ route('router4.firmware.grabar') }}">
            @csrf
            <input type="file" name="firmware" accept=".bin"><br><br>
            <button type="submit" class="warning" onclick="return confirm('¿Estás seguro de grabar el firmware? El router se reiniciará.')">SUBIR</button>
        </form>
    </div>
</div>

</body>
</html>
