<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Utilidades de Red - OpenWrt</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: white; border-radius: 15px; padding: 20px 30px; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px; }
        .header h1 { color: #4e73df; font-size: 1.8em; display: flex; align-items: center; gap: 12px; }
        .info-banner { background: #e8f4f8; border-left: 5px solid #4e73df; padding: 12px 20px; border-radius: 8px; display: flex; align-items: center; gap: 12px; color: #2c3e50; }
        .card { background: white; border-radius: 15px; padding: 25px; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); }
        .card-title { font-size: 1.3em; color: #4e73df; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e3e6f0; display: flex; align-items: center; gap: 10px; }
        .enlaces-grid { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 30px; }
        .enlace-btn { background: #f8f9fc; border: 2px solid #e3e6f0; border-radius: 30px; padding: 8px 20px; cursor: pointer; font-size: 0.9em; transition: all 0.3s ease; color: #5a5c69; }
        .enlace-btn:hover { background: #4e73df; border-color: #4e73df; color: white; transform: translateY(-2px); }
        .enlace-btn.active { background: #4e73df; border-color: #4e73df; color: white; }
        .tools { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
        .tool-btn { background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); color: white; border: none; padding: 12px 30px; font-size: 1em; font-weight: 600; border-radius: 10px; cursor: pointer; transition: all 0.3s ease; display: inline-flex; align-items: center; gap: 10px; }
        .tool-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(78,115,223,0.4); }
        .tool-btn.ping-btn { background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); }
        .tool-btn.traceroute-btn { background: linear-gradient(135deg, #fd7e14 0%, #e66a0a 100%); }
        .tool-btn.nslookup-btn { background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%); }
        .custom-input { display: flex; gap: 10px; margin-top: 15px; margin-bottom: 20px; flex-wrap: wrap; }
        .custom-input input { flex: 1; padding: 12px 15px; border: 2px solid #e3e6f0; border-radius: 10px; font-size: 1em; transition: all 0.3s ease; }
        .custom-input input:focus { outline: none; border-color: #4e73df; box-shadow: 0 0 0 3px rgba(78,115,223,0.1); }
        .custom-input button { background: #e3e6f0; color: #5a5c69; border: none; padding: 12px 25px; border-radius: 10px; cursor: pointer; font-weight: 600; transition: all 0.2s; }
        .custom-input button:hover { background: #d1d3e2; }
        .resultado { background: #1a1a2e; color: #00ff88; border-radius: 12px; padding: 20px; font-family: 'Courier New', monospace; font-size: 0.9em; overflow-x: auto; white-space: pre-wrap; word-break: break-all; max-height: 500px; overflow-y: auto; }
        .resultado-titulo { font-size: 1.1em; font-weight: bold; margin-bottom: 15px; color: #4e73df; }
        .error { background: #f8d7da; color: #721c24; border-left: 5px solid #dc3545; padding: 15px; border-radius: 10px; margin-bottom: 20px; }
        .loading { display: inline-block; width: 20px; height: 20px; border: 3px solid #f3f3f3; border-top: 3px solid #4e73df; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @media (max-width: 768px) { .tools { justify-content: center; } .enlaces-grid { justify-content: center; } }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1><i class="fas fa-network-wired"></i> Utilidades de Red</h1>
        <div class="info-banner">
            <i class="fas fa-chart-line"></i>
            <span>Diagnósticos de red - Ping, Traceroute, Nslookup</span>
        </div>
    </div>

    <div class="card">
        <div class="card-title">
            <i class="fas fa-globe"></i> Destinos comunes
        </div>

        <div class="enlaces-grid" id="enlacesGrid">
            @php
                $enlaces = [
                    'google.org' => 'google.org',
                    'openwrt.org' => 'openwrt.org',
                    'github.com' => 'github.com',
                    'cloudflare.com' => 'cloudflare.com',
                    '8.8.8.8' => '8.8.8.8 (Google DNS)',
                    '1.1.1.1' => '1.1.1.1 (Cloudflare DNS)',
                    'yahoo.com' => 'yahoo.com',
                    'bing.com' => 'bing.com',
                    'microsoft.com' => 'microsoft.com',
                    'amazon.com' => 'amazon.com'
                ];
            @endphp
            @foreach($enlaces as $key => $label)
                <button class="enlace-btn" data-destino="{{ $key }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="tools">
            <button class="tool-btn ping-btn" id="btnPing">
                <i class="fas fa-chart-simple"></i> PING
            </button>
            <button class="tool-btn traceroute-btn" id="btnTraceroute">
                <i class="fas fa-route"></i> TRACEROUTE
            </button>
            <button class="tool-btn nslookup-btn" id="btnNslookup">
                <i class="fas fa-search"></i> NSLOOKUP
            </button>
        </div>

        <div class="custom-input">
            <input type="text" id="customDestino" placeholder="O escribe un dominio o IP personalizado... (ej: google.com, 8.8.8.8, 192.168.10.1)">
            <button id="btnPersonalizado"><i class="fas fa-rocket"></i> Diagnosticar</button>
        </div>

        @if(session('error'))
            <div class="error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif

        @if(session('resultado'))
            <div class="resultado-titulo">
                <i class="fas fa-terminal"></i> {{ session('titulo') }}
            </div>
            <div class="resultado">
                {{ session('resultado') }}
            </div>
        @endif
    </div>

    <div class="card" style="text-align: center; color: #858796;">
        <p>
            <i class="fas fa-info-circle"></i>
            Powered by <strong>Interfaz Repetidor</strong> / OpenWrt
        </p>
    </div>
</div>

<script>
    let destinoActual = '';

    document.querySelectorAll('.enlace-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.enlace-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            destinoActual = this.dataset.destino;
            document.getElementById('customDestino').value = this.textContent.split(' ')[0];
        });
    });

    async function ejecutarDiagnostico(accion, destino) {
        if (!destino) {
            alert('Por favor, selecciona un destino o escribe uno personalizado');
            return false;
        }

        // Mostrar loading
        const btn = document.querySelector(`.${accion}-btn`) || document.getElementById('btnPersonalizado');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<div class="loading"></div> Cargando...';
        btn.disabled = true;

        try {
            const response = await fetch('/api/router3/diagnostico', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ accion: accion, destino: destino })
            });

            // Verificar si la respuesta es JSON válido
            const text = await response.text();
            console.log('Respuesta raw:', text);

            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('Error parsing JSON:', e);
                alert('Error del servidor: ' + text.substring(0, 200));
                return;
            }

            if (data.status === 'success') {
                // Mostrar resultado en la página
                const resultadoDiv = document.createElement('div');
                resultadoDiv.className = 'resultado-titulo';
                resultadoDiv.innerHTML = `<i class="fas fa-terminal"></i> ${accion.toUpperCase()} ${destino}`;

                const resultadoContent = document.createElement('div');
                resultadoContent.className = 'resultado';
                resultadoContent.innerText = data.output;

                // Eliminar resultados anteriores
                document.querySelectorAll('.resultado-titulo, .resultado').forEach(el => el.remove());

                // Insertar nuevos resultados
                const card = document.querySelector('.card');
                card.appendChild(resultadoDiv);
                card.appendChild(resultadoContent);

                resultadoContent.scrollIntoView({ behavior: 'smooth' });
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error de comunicación: ' + error.message);
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    document.getElementById('btnPing').addEventListener('click', function() {
        let destino = document.getElementById('customDestino').value;
        if (!destino) destino = destinoActual;
        if (!destino) { alert('Selecciona o escribe un destino'); return; }
        ejecutarDiagnostico('ping', destino);
    });

    document.getElementById('btnTraceroute').addEventListener('click', function() {
        let destino = document.getElementById('customDestino').value;
        if (!destino) destino = destinoActual;
        if (!destino) { alert('Selecciona o escribe un destino'); return; }
        ejecutarDiagnostico('traceroute', destino);
    });

    document.getElementById('btnNslookup').addEventListener('click', function() {
        let destino = document.getElementById('customDestino').value;
        if (!destino) destino = destinoActual;
        if (!destino) { alert('Selecciona o escribe un destino'); return; }
        ejecutarDiagnostico('nslookup', destino);
    });

    document.getElementById('btnPersonalizado').addEventListener('click', function() {
        let destino = document.getElementById('customDestino').value;
        if (!destino) { alert('Escribe un dominio o IP'); return; }
        let accion = prompt('¿Qué acción quieres realizar?\n1 - PING\n2 - TRACEROUTE\n3 - NSLOOKUP', '1');
        if (accion === '1') ejecutarDiagnostico('ping', destino);
        else if (accion === '2') ejecutarDiagnostico('traceroute', destino);
        else if (accion === '3') ejecutarDiagnostico('nslookup', destino);
    });

    document.getElementById('customDestino').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            let destino = this.value;
            if (destino) {
                let accion = prompt('¿Qué acción quieres realizar?\n1 - PING\n2 - TRACEROUTE\n3 - NSLOOKUP', '1');
                if (accion === '1') ejecutarDiagnostico('ping', destino);
                else if (accion === '2') ejecutarDiagnostico('traceroute', destino);
                else if (accion === '3') ejecutarDiagnostico('nslookup', destino);
            }
        }
    });
</script>
</body>
</html>
