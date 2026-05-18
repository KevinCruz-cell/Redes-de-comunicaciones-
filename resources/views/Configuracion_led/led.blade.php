<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Configuración de LEDs - NuupNet</title>
    <style>
        body { font-family: Arial; background: #eef2f7; margin: 0; padding: 20px; }
        .menu { background: white; padding: 15px; border-radius: 10px; margin-bottom: 20px; display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; }
        .menu a { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
        .menu a:hover { background: #0056b3; }
        .contenedor { background: white; padding: 30px; border-radius: 10px; max-width: 900px; margin: 0 auto; box-shadow: 0 0 15px rgba(0,0,0,0.2); }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #007bff; color: white; }
        tr:nth-child(even) { background: #f2f2f2; }
        .acciones button { background: #28a745; color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer; margin: 2px; }
        button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-top: 10px; }
        button:hover { background: #0056b3; }
        .exito { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .form-led { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-top: 20px; }
        input, select { padding: 8px; margin: 5px; border: 1px solid #ccc; border-radius: 4px; }
        .aviso { background: #e7f3ff; padding: 15px; border-radius: 5px; margin-top: 20px; }
        .led-on { background: #d4edda; color: #155724; display: inline-block; padding: 5px 10px; border-radius: 5px; }
        .led-off { background: #f8d7da; color: #721c24; display: inline-block; padding: 5px 10px; border-radius: 5px; }
    </style>
</head>
<body>

<div class="menu">
    <a href="{{ route('router4.tareas') }}">⏰ Tareas Programadas</a>
    <a href="{{ route('router4.leds') }}">💡 Configuración LEDs</a>
    <a href="{{ route('router4.copia') }}">💾 Copia de Seguridad</a>
</div>

<div class="contenedor">
    <h1>💡 Configuración de LEDs</h1>
    <p>Personaliza el comportamiento de los LEDs del dispositivo.</p>

    @if(session('success'))
        <div class="exito">{{ session('success') }}</div>
    @endif

    <table>
        <thead>
        <tr>
            <th>LED</th>
            <th>Estado actual</th>
            <th>Trigger actual</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        @foreach($leds as $led)
            @php
                $estado = $estados[$led]['brillo'] == 255 ? '<span class="led-on">🔵 ENCENDIDO</span>' : '<span class="led-off">⚫ APAGADO</span>';
                $trigger = $estados[$led]['trigger'];
            @endphp
            <tr>
                <td><strong>{{ $led }}</strong></td>
                <td>{!! $estado !!}</td>
                <td>{{ $trigger }}</td>
                <td class="acciones">
                    <form method="POST" action="{{ route('router4.led.encender') }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="led" value="{{ $led }}">
                        <button type="submit" style="background:#28a745;">💡 Encender</button>
                    </form>
                    <form method="POST" action="{{ route('router4.led.apagar') }}" style="display:inline;">
                        @csrf
                        <input type="hidden" name="led" value="{{ $led }}">
                        <button type="submit" style="background:#dc3545;">🔘 Apagar</button>
                    </form>
                    <button onclick="mostrarTrigger('{{ $led }}', '{{ $trigger }}')">⚙️ Trigger</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div id="formularioTrigger" class="form-led" style="display:none;">
        <h3>⚙️ Configurar Trigger del LED</h3>
        <form method="POST" action="{{ route('router4.led.trigger') }}">
            @csrf
            <input type="hidden" name="led" id="trigger_led">
            <label>Selecciona comportamiento:</label>
            <select name="trigger" id="trigger_select">
                <option value="none">none - Siempre apagado</option>
                <option value="default-on">default-on - Siempre encendido</option>
                <option value="netdev">netdev - Actividad de red</option>
                <option value="phy0rx">phy0rx - Recepción WiFi</option>
                <option value="phy0tx">phy0tx - Transmisión WiFi</option>
                <option value="timer">timer - Parpadeo</option>
            </select><br><br>
            <button type="submit">💾 Guardar Trigger</button>
            <button type="button" onclick="cerrarFormulario()">❌ Cancelar</button>
        </form>
    </div>

    <div class="aviso">
        <strong>📌 ¿Qué es un Trigger?</strong><br>
        - <strong>none:</strong> LED siempre apagado<br>
        - <strong>default-on:</strong> LED siempre encendido<br>
        - <strong>netdev:</strong> Se enciende cuando hay actividad de red<br>
        - <strong>phy0rx:</strong> Parpadea al recibir datos WiFi<br>
        - <strong>phy0tx:</strong> Parpadea al enviar datos WiFi<br>
        - <strong>timer:</strong> Parpadea en intervalos regulares
    </div>
</div>

<script>
    function mostrarTrigger(led, triggerActual) {
        document.getElementById('trigger_led').value = led;
        document.getElementById('trigger_select').value = triggerActual;
        document.getElementById('formularioTrigger').style.display = 'block';
    }

    function cerrarFormulario() {
        document.getElementById('formularioTrigger').style.display = 'none';
    }
</script>

</body>
</html>
