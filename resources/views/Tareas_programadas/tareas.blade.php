<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tareas Programadas - NuupNet</title>
    <style>
        body { font-family: Arial; background: #eef2f7; margin: 0; padding: 20px; }
        .menu { background: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; }
        .menu a { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
        .menu a:hover { background: #0056b3; }
        .contenedor { background: white; padding: 30px; border-radius: 10px; max-width: 800px; margin: 0 auto; box-shadow: 0 0 15px rgba(0,0,0,0.2); }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .aviso { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 5px; }
        textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-family: monospace; margin: 10px 0; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .exito { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow: auto; }
    </style>
</head>
<body>

<div class="menu">
    <a href="{{ route('router4.tareas') }}">⏰ Tareas Programadas</a>
    <a href="{{ route('router4.leds') }}">💡 Configuración LEDs</a>
    <a href="{{ route('router4.copia') }}">💾 Copia de Seguridad</a>
</div>

<div class="contenedor">
    <h1>⏰ Tareas Programadas (Crontab)</h1>
    <p>Definición de tareas programadas para el controlador.</p>
    <div class="aviso">
        📌 Nota: Debe reiniciar manualmente el servicio si el archivo control está vacío antes de editar.
    </div>

    @if(session('success'))
        <div class="exito">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('router4.tareas.guardar') }}">
        @csrf
        <label><strong>📝 Tareas programadas (formato crontab):</strong></label>
        <textarea name="tareas" rows="12">{{ trim($tareas) }}</textarea>
        <p><small>📌 Formato: * * * * * comando_a_ejecutar<br>Ejemplo: 0 5 * * * /sbin/reboot (reinicia a las 5:00 AM)</small></p>
        <button type="submit">💾 Guardar Tareas</button>
    </form>

    <h2>📖 Ejemplos útiles:</h2>
    <pre>
# Reiniciar el router todos los días a las 3:00 AM
0 3 * * * /sbin/reboot

# Reiniciar WiFi cada 6 horas
0 */6 * * * /sbin/wifi restart

# Hacer respaldo de configuración cada semana
0 2 * * 0 /sbin/sysupgrade -b /etc/config/backup.tar.gz
    </pre>
</div>

</body>
</html>
