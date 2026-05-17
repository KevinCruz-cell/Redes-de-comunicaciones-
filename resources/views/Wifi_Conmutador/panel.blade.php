<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NuupNet - Panel de Control</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; }
        .app-container { display: flex; min-height: 100vh; }
        .sidebar {
            width: 260px;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: white;
            padding: 20px 0;
        }
        .sidebar-header { padding: 0 20px 20px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .sidebar-header h2 { font-size: 20px; }
        .sidebar-header p { font-size: 11px; color: #a0a0a0; margin-top: 5px; }
        .nav-menu { list-style: none; }
        .nav-item {
            padding: 12px 20px;
            margin: 4px 10px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .nav-item:hover { background: rgba(255,255,255,0.1); }
        .nav-item.active { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .nav-icon { font-size: 18px; }
        .nav-label { font-size: 14px; font-weight: 500; }
        .main-content { flex: 1; padding: 20px; overflow-y: auto; }
        .card { background: white; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden; }
        .card-header { padding: 15px 20px; background: #fafafa; border-bottom: 1px solid #e0e0e0; font-weight: 600; font-size: 16px; }
        .card-body { padding: 20px; }
        .info-row { display: flex; margin-bottom: 8px; font-size: 14px; }
        .info-label { width: 130px; font-weight: 600; color: #666; }
        .info-value { color: #333; }
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 13px;
            margin: 4px;
            transition: all 0.2s;
        }
        .btn-blue { background: #3498db; color: white; }
        .btn-green { background: #00b894; color: white; }
        .btn-red { background: #e74c3c; color: white; }
        .btn-orange { background: #f39c12; color: white; }
        .btn-gray { background: #95a5a6; color: white; }
        .btn-group { display: flex; gap: 8px; flex-wrap: wrap; margin: 15px 0; }

        .wifi-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .wifi-table th { background: #f8f9fa; padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .wifi-table td { padding: 10px; border-bottom: 1px solid #eee; }
        .wifi-table tr:hover { background: #f8f9fa; cursor: pointer; }

        .signal-excellent { color: #00b894; font-weight: bold; }
        .signal-good { color: #3498db; font-weight: bold; }
        .signal-fair { color: #f39c12; font-weight: bold; }
        .signal-poor { color: #e74c3c; font-weight: bold; }

        .output { background: #f8f9fa; border-radius: 8px; padding: 15px; margin-top: 15px; max-height: 400px; overflow: auto; }
        .loading { text-align: center; padding: 30px; color: #3498db; }

        /* Modal Ampliado y con Pestañas */
        .modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 25px; border-radius: 12px; width: 650px; max-height: 85vh; overflow-y: auto; z-index: 1000; box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        .overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 999; }

        .modal-tabs { display: flex; border-bottom: 2px solid #eee; margin-bottom: 15px; gap: 5px; }
        .modal-tab { padding: 10px 15px; cursor: pointer; border-radius: 6px 6px 0 0; border: 1px solid transparent; border-bottom: none; font-size: 13px; font-weight: 600; background: #f5f5f5; color: #666; }
        .modal-tab.active { background: #fff; border-color: #eee; color: #3498db; margin-bottom: -2px; border-top: 3px solid #3498db; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        .toast { position: fixed; bottom: 20px; right: 20px; background: #333; color: white; padding: 12px 20px; border-radius: 8px; z-index: 1001; transition: opacity 0.3s; }
        .button-bar { display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px; padding-top: 15px; border-top: 1px solid #e0e0e0; }
        .info-note { background: #e8f0fe; border-radius: 6px; padding: 8px; margin-top: 10px; font-size: 12px; color: #3498db; text-align: center; }
        .new-interface { background: #f0fdf4; border-left: 3px solid #00b894; margin-top: 10px; padding: 10px; border-radius: 8px; }

        .vlan-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .vlan-table th { background: #f8f9fa; padding: 12px; text-align: left; border-bottom: 2px solid #ddd; }
        .vlan-table td { padding: 10px; border-bottom: 1px solid #eee; }
        .port-select { padding: 5px 10px; border-radius: 6px; border: 1px solid #ddd; background: white; cursor: pointer; font-size: 12px; }
        .port-select.untagged { background: #e8f0fe; color: #3498db; }
        .port-select.tagged { background: #f0fdf4; color: #00b894; }
        .port-select.off { background: #fef3f2; color: #e74c3c; }
        .status-up { color: #00b894; font-weight: bold; }
        .status-down { color: #e74c3c; }
        .config-group { margin-bottom: 10px; padding: 15px; background: #f8f9fa; border-radius: 8px; }
        .config-row { display: flex; margin-bottom: 12px; align-items: center; }
        .config-label { width: 220px; font-size: 13px; color: #666; font-weight: 500; }
        .config-input { flex: 1; }
        input, select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px; }

        /* Estilos de la Matriz Unificada del Conmutador (OpenWrt Style) */
        .vlan-matrix-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .vlan-matrix-table th { background: #f5f5f5; color: #2c3e50; font-weight: bold; padding: 8px; border: 1px solid #dcdde1; text-align: center; }
        .vlan-matrix-table td { border: 1px solid #dcdde1; padding: 10px 6px; text-align: center; vertical-align: middle; }
        .ports-status-row { background: #fafafa; }
        .text-bold-label { font-weight: bold; color: #333; text-align: right !important; padding-right: 15px !important; }
        .port-wrapper { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px; }
        .port-text-status { font-size: 11px; font-weight: bold; line-height: 1.2; text-align: center; }
        .vlan-select { width: 100%; padding: 5px; border: 1px solid #ccc; border-radius: 4px; background-color: #fff; font-size: 12px; color: #333; cursor: pointer; }
        .vlan-select:focus { border-color: #3498db; outline: none; }
        .vlan-id-input { width: 50px; text-align: center; padding: 4px; border: none; border-bottom: 1px solid #7f8c8d; font-weight: bold; font-size: 13px; background: transparent; }
        .vlan-id-input:focus { outline: none; border-bottom: 2px solid #3498db; }
    </style>
</head>
<body>
<div class="app-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>NuupNet</h2>
            <p>Panel de Control</p>
        </div>
        <ul class="nav-menu">
            <li class="nav-item active" onclick="showView('wifi')">
                <span class="nav-icon">📡</span>
                <span class="nav-label">Wi-Fi</span>
            </li>
            <li class="nav-item" onclick="loadSwitchView()">
                <span class="nav-icon">🔌</span>
                <span class="nav-label">Conmutador</span>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div id="wifiView">
            <div class="card">
                <div class="card-header">📡 Vista general de Wi-Fi</div>
                <div class="card-body">
                    <div class="info-row"><div class="info-label">radio0</div><div class="info-value"></div></div>
                    <div class="info-row"><div class="info-label">Hardware:</div><div class="info-value" id="hardware">-</div></div>
                    <div class="info-row"><div class="info-label">Canal:</div><div class="info-value" id="channel">-</div></div>
                    <div class="info-row"><div class="info-label">Tasa de bits:</div><div class="info-value" id="bitrate">-</div></div>
                    <div class="info-row"><div class="info-label">Tx-Power:</div><div class="info-value" id="txpower">-</div></div>
                    <div class="btn-group">
                        <button class="btn btn-orange" onclick="restartWiFi()">REINICIAR</button>
                        <button class="btn btn-green" onclick="showAddPanel()">AÑADIR</button>
                    </div>
                    <div class="info-row"><div class="info-label">SSID:</div><div class="info-value" id="ssid">-</div></div>
                    <div class="info-row"><div class="info-label">BSSID:</div><div class="info-value" id="bssid">-</div></div>
                    <div class="info-row"><div class="info-label">Modo:</div><div class="info-value" id="mode">Master</div></div>
                    <div class="info-row"><div class="info-label">Encriptación:</div><div class="info-value" id="encryption">-</div></div>
                    <div class="btn-group">
                        <button class="btn btn-red" onclick="disableWiFi()">DESACTIVAR</button>
                        <button class="btn btn-red" onclick="deleteWiFi()">ELIMINAR</button>
                    </div>
                    <div class="info-note" id="interfacesNote">📡 Interfaces: --</div>
                    <div id="additionalInterfaces"></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">🖥️ Dispositivos conectados</div>
                <div class="card-body">
                    <div id="devicesTable"><div class="loading">Cargando...</div></div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">🔍 Búsqueda de redes Wi-Fi</div>
                <div class="card-body">
                    <button class="btn btn-blue" onclick="scanNetworks()">ESCANEAR</button>
                    <div id="scanResult" class="output"><div class="loading">✨ Haz clic en ESCANEAR</div></div>
                </div>
            </div>
        </div>

        <div id="switchView" style="display: none;">
            <div class="card">
                <div class="card-header">🔌 VLANs en "switch0" (rt305x-esw)</div>
                <div class="card-body" style="padding: 12px;">
                    <div id="switchContent">
                        <table class="vlan-matrix-table">
                            <thead>
                            <tr>
                                <th style="width: 10%;">VLAN ID</th>
                                <th style="width: 15%;">CPU (eth0)</th>
                                <th style="width: 13%;">LAN 1</th>
                                <th style="width: 13%;">LAN 2</th>
                                <th style="width: 13%;">LAN 3</th>
                                <th style="width: 13%;">LAN 4</th>
                                <th style="width: 13%;">WAN</th>
                                <th style="width: 12%;">Acción</th>
                            </tr>
                            <tr id="portsStatusRow" class="ports-status-row">
                                <td class="text-bold-label">Estado del puerto:</td>
                            </tr>
                            </thead>
                            <tbody id="vlanRowsContainer">
                            </tbody>
                        </table>
                        <div class="btn-group" style="margin-top: 15px;">
                            <button class="btn btn-blue" onclick="addNewVlanRow()">AÑADIR VLAN</button>
                            <button class="btn btn-green" onclick="saveSwitchConfig()">GUARDAR Y APLICAR</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="overlay" id="overlay" onclick="closeAddModal()"></div>

<div class="modal" id="addModal">
    <h3 style="margin-bottom:15px; color:#16213e;">➕ Agregar nueva red Wi-Fi (Punto de Acceso)</h3>

    <div class="modal-tabs">
        <div class="modal-tab active" onclick="switchTab('tab-basica')">Configuración Básica</div>
        <div class="modal-tab" onclick="switchTab('tab-avanzada1')">Avanzado 1</div>
        <div class="modal-tab" onclick="switchTab('tab-filtromac')">Filtro MAC</div>
        <div class="modal-tab" onclick="switchTab('tab-avanzada2')">Avanzado 2</div>
    </div>

    <div id="tab-basica" class="tab-content active">
        <div class="config-group">
            <div class="config-row"><div class="config-label">ESSID:</div><div class="config-input"><input type="text" id="newEssid" value="OpenWrt_AP"></div></div>
            <div class="config-row"><div class="config-label">Encriptación:</div><div class="config-input"><select id="newEncryption" onchange="togglePassword(this.value)"><option value="none" selected>Sin encriptación</option><option value="psk2">WPA2-PSK</option></select></div></div>
            <div class="config-row" id="pwdRow" style="display:none;"><div class="config-label">Contraseña:</div><div class="config-input"><input type="text" id="newPassword" placeholder="Mínimo 8 caracteres"></div></div>
            <div class="config-row"><div class="config-label">📻 Canal:</div><div class="config-input"><select id="newChannel"><option value="1">Canal 1 (2.412 GHz)</option><option value="6">Canal 6 (2.437 GHz)</option><option value="11" selected>Canal 11 (2.462 GHz)</option></select></div></div>
            <div class="config-row"><div class="config-label">⚡ Potencia TX:</div><div class="config-input"><select id="newTxPower"><option value="15">15 dBm (32 mW)</option><option value="18" selected>18 dBm (63 mW)</option><option value="20">20 dBm (100 mW)</option></select></div></div>
        </div>
    </div>

    <div id="tab-avanzada1" class="tab-content">
        <div class="config-group">
            <div class="config-row"><div class="config-label">Código de País:</div><div class="config-input"><input type="text" id="newCountryCode" placeholder="Ej: MX, US (Opcional)"></div></div>
            <div class="config-row"><div class="config-label">Permitir tasas 802.11b heredadas:</div><div class="config-input"><select id="newLegacyRates"><option value="" selected>Por defecto (Sí)</option><option value="1">Sí</option><option value="0">No</option></select></div></div>
            <div class="config-row"><div class="config-label">Optimización a distancia (metros):</div><div class="config-input"><input type="number" id="newDistance" placeholder="Ej: 10, 50 (Opcional)"></div></div>
            <div class="config-row"><div class="config-label">Umbral de Fragmentación:</div><div class="config-input"><select id="newFrag"><option value="off" selected>Apagado</option></select></div></div>
            <div class="config-row"><div class="config-label">Umbral RTS/CTS:</div><div class="config-input"><select id="newRts"><option value="off" selected>Apagado</option></select></div></div>
            <div class="config-row"><div class="config-label">Forzar modo 40 MHz:</div><div class="config-input"><select id="newForce40"><option value="" selected>Por defecto (No)</option><option value="1">Sí (HT40)</option><option value="0">No (HT20)</option></select></div></div>
            <div class="config-row"><div class="config-label">Intervalo de Baliza (Beacon):</div><div class="config-input"><input type="number" id="newBeaconInt" placeholder="Por defecto: 100 (Opcional)"></div></div>
        </div>
    </div>

    <div id="tab-filtromac" class="tab-content">
        <div class="config-group">
            <div class="config-row"><div class="config-label">Filtrar por dirección MAC:</div><div class="config-input"><select id="newMacPolicy"><option value="disable" selected>Desactivar</option><option value="allow">Permitir solo los de la lista (White-list)</option><option value="deny">Bloquear los de la lista (Black-list)</option></select></div></div>
        </div>
    </div>

    <div id="tab-avanzada2" class="tab-content">
        <div class="config-group">
            <div class="config-row"><div class="config-label">Aislar clientes (Client Isolation):</div><div class="config-input"><select id="newIsolate"><option value="" selected>Por defecto (No)</option><option value="1">Sí</option><option value="0">No</option></select></div></div>
            <div class="config-row"><div class="config-label">Intervalo DTIM:</div><div class="config-input"><input type="number" id="newDtimPeriod" placeholder="Ej: 2 (Opcional)"></div></div>
            <div class="config-row"><div class="config-label">Preámbulo Corto (Short Preamble):</div><div class="config-input"><select id="newShortPreamble"><option value="" selected>Por defecto (Sí)</option><option value="1">Sí</option><option value="0">No</option></select></div></div>
            <div class="config-row"><div class="config-label">Desactivar sondeo de inactividad:</div><div class="config-input"><select id="newDisassocLowAck"><option value="" selected>Por defecto (No)</option><option value="1">Sí (Desactivar)</option><option value="0">No (Activo)</option></select></div></div>
            <div class="config-row"><div class="config-label">Límite inactividad estación (seg):</div><div class="config-input"><input type="number" id="newMaxInactivity" placeholder="Ej: 300 (Opcional)"></div></div>
            <div class="config-row"><div class="config-label">Máximo permitido intervalo escucha:</div><div class="config-input"><input type="number" id="newMaxListenInt" placeholder="Ej: 100 (Opcional)"></div></div>
        </div>
    </div>

    <div class="button-bar">
        <button class="btn btn-gray" onclick="closeAddModal()">CANCELAR</button>
        <button class="btn btn-green" onclick="addNewInterface()">CREAR INTERFAZ</button>
    </div>
</div>

<div id="toast" class="toast" style="opacity: 0; display: none;"></div>

<script>
    let maxBSS = 4;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Inicializador al cargar la página
    window.onload = function() {
        loadWiFiInfo();
        loadDevices();
    };

    function showToast(msg, isError = false) {
        const t = document.getElementById('toast');
        t.innerText = msg;
        t.style.display = 'block';
        t.style.opacity = '1';
        t.style.background = isError ? '#e74c3c' : '#333';
        setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.style.display = 'none', 300); }, 3000);
    }

    function showView(view) {
        if (view === 'wifi') {
            document.getElementById('wifiView').style.display = 'block';
            document.getElementById('switchView').style.display = 'none';
        } else {
            document.getElementById('wifiView').style.display = 'none';
            document.getElementById('switchView').style.display = 'block';
        }
        document.querySelectorAll('.nav-item').forEach(item => item.classList.remove('active'));
        if(event) event.currentTarget.classList.add('active');
    }

    // Pestañas dinámicas del Modal
    function switchTab(tabId) {
        document.querySelectorAll('.modal-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        event.currentTarget.classList.add('active');
        document.getElementById(tabId).classList.add('active');
    }

    function togglePassword(val) {
        document.getElementById('pwdRow').style.display = (val === 'psk2') ? 'flex' : 'none';
    }

    // API POST Corregida: Soporta peticiones de lectura nativa sin provocar rechazos
    async function apiPost(cmd) {
        try {
            const response = await fetch('/api/router/apply-uci', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ commands: [cmd] })
            });
            const data = await response.json();
            return data.output || data.message || "OK";
        } catch(e) {
            return "Error";
        }
    }

    async function apiPostSwitch(cmd) {
        try {
            const response = await fetch('/api/router/switch', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ cmd: cmd })
            });
            const data = await response.json();
            return data.output || "";
        } catch(e) { return "Error"; }
    }

    // ESCANER ROBUSTO
    async function scanNetworks() {
        const out = document.getElementById('scanResult');
        out.innerHTML = '<div class="loading">🔍 Escaneando aire en tiempo real...</div>';
        try {
            const response = await fetch('/api/router/scan');
            const nets = await response.json();

            if (nets && Array.isArray(nets) && nets.length) {
                let html = '<table class="wifi-table"><thead><th>Señal</th><th>SSID</th><th>Canal</th><th>BSSID</th><th>Encriptación</th></thead><tbody>';
                for (let n of nets) {
                    let cls = '';
                    let sig = parseInt(n.signal) || -100;
                    if (sig >= -50) cls = 'signal-excellent';
                    else if (sig >= -60) cls = 'signal-good';
                    else if (sig >= -70) cls = 'signal-fair';
                    else cls = 'signal-poor';
                    html += `<tr>
                        <td class="${cls}">${n.signal} dBm</td>
                        <td><strong>${n.ssid}</strong></td>
                        <td>${n.channel}</td>
                        <td><small>${n.bssid}</small></td>
                        <td><small>${n.encryption}</small></td>
                    </tr>`;
                }
                html += '</tbody></table>';
                out.innerHTML = html;
            } else {
                out.innerHTML = '<div class="loading">❌ Antena saturada. Reintente en unos segundos.</div>';
            }
        } catch(e) { out.innerHTML = '<div class="loading">❌ Error: ' + e.message + '</div>'; }
    }

    async function restartWiFi() {
        showToast('🔄 Ejecutando apagado y encendido físico...');
        try {
            await fetch('/api/router/restart-wifi', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken } });
            showToast('✅ Secuencia completada con éxito');
            setTimeout(() => { loadWiFiInfo(); loadDevices(); }, 3500);
        } catch(e) { showToast('❌ Error al reiniciar', true); }
    }

    async function loadAllInterfaces() {
        const result = await apiPost('uci show wireless');
        if (result === "Error" || !result) {
            document.getElementById('additionalInterfaces').innerHTML = '<div class="info-note">📡 No hay redes adicionales configuradas</div>';
            return;
        }

        const lines = result.split('\n');
        const interfacesMap = {};

        lines.forEach(line => {
            const match = line.trim().match(/^wireless\.([^.]+)\.([^=]+)='?(.*?)'?$/);
            if (match) {
                const section = match[1];
                const option = match[2];
                let value = match[3];
                if (value.endsWith("'")) value = value.slice(0, -1);
                if (!interfacesMap[section]) interfacesMap[section] = {};
                interfacesMap[section][option] = value;
            }
        });

        const wifiIfaces = [];
        Object.keys(interfacesMap).forEach(sectionName => {
            const data = interfacesMap[sectionName];
            if (data.mode === 'ap' && sectionName !== 'default_radio0') {
                wifiIfaces.push({
                    section: sectionName,
                    ssid: data.ssid || 'SSID Oculto',
                    encryption: data.encryption || 'none'
                });
            }
        });

        const currentCount = wifiIfaces.length + 1;
        const remaining = maxBSS - currentCount;

        let limitHtml = `<div class="info-note" style="background:${currentCount >= maxBSS ? '#fef3f2' : '#e8f0fe'}">
        📡 <strong>Límite de redes:</strong> ${currentCount} / ${maxBSS} ${currentCount >= maxBSS ? '⚠️ LÍMITE ALCANZADO' : `(puedes agregar ${remaining} más)`}
    </div>`;

        if (wifiIfaces.length === 0) {
            document.getElementById('additionalInterfaces').innerHTML = limitHtml + '<div class="info-note">📡 No hay redes adicionales creadas</div>';
            document.getElementById('interfacesNote').innerHTML = `📡 Interfaces: ${currentCount}/${maxBSS}`;
            return;
        }

        let html = limitHtml + '<div class="new-interface"><strong>📡 Redes adicionales:</strong><br><br>';
        html += '<table class="wifi-table" style="width:100%"><thead><th>SSID</th><th>Encriptación</th><th>Acción</th></thead><tbody>';

        wifiIfaces.forEach(net => {
            const safeSsid = net.ssid.replace(/'/g, "\\'");
            html += `<tr>
            <td><strong>${net.ssid}</strong></td>
            <td>${net.encryption === 'none' ? '🔓 Abierta' : '🔒 ' + net.encryption}</td>
            <td>
                <button class="btn btn-red" style="padding:4px 12px; font-size:12px;" onclick="window.deleteInterfaceBySSID('${safeSsid}')">
                    🗑️ ELIMINAR
                </button>
            </td>
        </tr>`;
        });

        html += '</tbody></table></div>';
        document.getElementById('additionalInterfaces').innerHTML = html;
        document.getElementById('interfacesNote').innerHTML = `📡 Interfaces: ${currentCount}/${maxBSS}`;
    }

    async function deleteInterfaceBySSID(ssidName) {
        // Advertencia visual en lugar de confirm
        showToast(`⚠️ Eliminando "${ssidName}"...`, true);

        setTimeout(async () => {
            try {
                const safeSsid = ssidName.replace(/'/g, "'\\''");
                const findAndDeleteCmd = `uci show wireless | grep -i "ssid='${safeSsid}'" | awk -F'.' '{print $2}' | awk -F'=' '{print $1}' | while read sec; do uci delete wireless.$sec; done; uci commit wireless`;

                const response = await fetch('/api/router/apply-uci', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    body: JSON.stringify({ commands: [findAndDeleteCmd] })
                });

                const data = await response.json();

                if(data.status === 'success') {
                    showToast('🔄 Reiniciando WiFi...');
                    await fetch('/api/router/restart-wifi', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrfToken }
                    });
                    showToast(`✅ Red "${ssidName}" eliminada correctamente`);
                    setTimeout(() => {
                        loadWiFiInfo();
                        loadAllInterfaces();
                        scanNetworks();
                    }, 4000);
                } else {
                    showToast(`❌ Error: ${data.message}`, true);
                }
            } catch (e) {
                console.error('Error:', e);
                showToast(`❌ Error de comunicación`, true);
            }
        }, 500);
    }

    function showAddPanel() {
        document.getElementById('overlay').style.display='block';
        document.getElementById('addModal').style.display='block';
        switchTab('tab-basica');
    }

    document.getElementById('overlay').onclick = function() { closeAddModal(); };
    function closeAddModal() {
        document.getElementById('overlay').style.display='none';
        document.getElementById('addModal').style.display='none';
    }

    async function addNewInterface() {
        const essid = document.getElementById('newEssid').value.trim();
        const enc = document.getElementById('newEncryption').value;
        const pwd = document.getElementById('newPassword').value.trim();
        const channel = document.getElementById('newChannel').value;
        const txpower = document.getElementById('newTxPower').value;

        if(!essid){ showToast('❌ Ingrese un ESSID válido', true); return; }
        if(enc === 'psk2' && pwd.length < 8){ showToast('❌ La contraseña WPA2 requiere mínimo 8 caracteres', true); return; }

        showToast('⚙️ Generando ráfaga UCI avanzada...');
        let cmds = [];

        cmds.push(`uci set wireless.radio0.channel='${channel}'`);
        cmds.push(`uci set wireless.radio0.txpower='${txpower}'`);
        cmds.push("uci add wireless wifi-iface");
        cmds.push("uci set wireless.@wifi-iface[-1].device='radio0'");
        cmds.push("uci set wireless.@wifi-iface[-1].mode='ap'");
        cmds.push("uci set wireless.@wifi-iface[-1].network='lan'");
        cmds.push(`uci set wireless.@wifi-iface[-1].ssid='${essid.replace(/'/g, "'\\''")}'`);
        cmds.push(`uci set wireless.@wifi-iface[-1].encryption='${enc}'`);
        if(enc === 'psk2') {
            cmds.push(`uci set wireless.@wifi-iface[-1].key='${pwd.replace(/'/g, "'\\''")}'`);
        }

        const country = document.getElementById('newCountryCode').value.trim();
        if(country) cmds.push(`uci set wireless.radio0.country='${country.toUpperCase()}'`);

        const legacy = document.getElementById('newLegacyRates').value;
        if(legacy) cmds.push(`uci set wireless.@wifi-iface[-1].legacy_rates='${legacy}'`);

        const distance = document.getElementById('newDistance').value;
        if(distance) cmds.push(`uci set wireless.radio0.distance='${distance}'`);

        const force40 = document.getElementById('newForce40').value;
        if(force40 === '1') cmds.push("uci set wireless.radio0.htmode='HT40'");
        else if(force40 === '0') cmds.push("uci set wireless.radio0.htmode='HT20'");

        const beacon = document.getElementById('newBeaconInt').value;
        if(beacon) cmds.push(`uci set wireless.radio0.beacon_int='${beacon}'`);

        const macPolicy = document.getElementById('newMacPolicy').value;
        if(macPolicy !== 'disable') cmds.push(`uci set wireless.@wifi-iface[-1].macpolicy='${macPolicy}'`);

        const isolate = document.getElementById('newIsolate').value;
        if(isolate) cmds.push(`uci set wireless.@wifi-iface[-1].isolate='${isolate}'`);

        const dtim = document.getElementById('newDtimPeriod').value;
        if(dtim) cmds.push(`uci set wireless.@wifi-iface[-1].dtim_period='${dtim}'`);

        const shortPreamble = document.getElementById('newShortPreamble').value;
        if(shortPreamble) cmds.push(`uci set wireless.@wifi-iface[-1].short_preamble='${shortPreamble}'`);

        const disassocLowAck = document.getElementById('newDisassocLowAck').value;
        if(disassocLowAck) cmds.push(`uci set wireless.@wifi-iface[-1].disassoc_low_ack='${disassocLowAck}'`);

        const maxInactivity = document.getElementById('newMaxInactivity').value;
        if(maxInactivity) cmds.push(`uci set wireless.@wifi-iface[-1].max_inactivity='${maxInactivity}'`);

        const maxListen = document.getElementById('newMaxListenInt').value;
        if(maxListen) cmds.push(`uci set wireless.@wifi-iface[-1].max_listen_int='${maxListen}'`);

        // Guardar cambios
        cmds.push("uci commit wireless");

        try {
            // Primero enviar los comandos UCI
            const response = await fetch('/api/router/apply-uci', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ commands: cmds })
            });
            const data = await response.json();

            if(data.status === 'success') {
                // Luego reiniciar WiFi por separado
                showToast('🔄 Reiniciando WiFi para publicar la red...');

                const restartResponse = await fetch('/api/router/restart-wifi', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken }
                });

                if(restartResponse.ok) {
                    showToast(`✅ Red "${essid}" publicada con éxito`);
                } else {
                    showToast(`⚠️ Red creada pero el WiFi no se reinició`, true);
                }
            } else {
                showToast(`❌ Error: ${data.message}`, true);
            }
        } catch(e) {
            showToast('⚠️ Error al crear la red', true);
        }

        closeAddModal();
        setTimeout(() => { loadWiFiInfo(); loadAllInterfaces(); scanNetworks(); }, 6000);
    }

    async function loadWiFiInfo() {
        try {
            const response = await fetch('/api/router/wifi-info');
            const data = await response.json();

            document.getElementById('hardware').innerText = "MediaTek MT76x8 802.11bgn";

            if(data.status === 'success' && data.info) {
                const ch = data.info.match(/Channel:\s*(\d+)/);
                const freq = data.info.match(/(\d+\.\d+)\s*GHz/);
                if(ch) document.getElementById('channel').innerText = ch[1] + (freq ? ` (${freq[1]} GHz)` : "");

                const tp = data.info.match(/Tx-Power:\s*(\d+)/);
                if(tp) document.getElementById('txpower').innerText = tp[1] + " dBm";

                const br = data.info.match(/Bit Rate:\s*([\d.]+)\s+(\w+)/);
                document.getElementById('bitrate').innerText = br ? br[1]+" "+br[2] : "N/A";

                const ap = data.info.match(/Access Point:\s*([A-F0-9:]+)/);
                if(ap) document.getElementById('bssid').innerText = ap[1];

                document.getElementById('ssid').innerText = data.ssid || "Desconocido";
                document.getElementById('encryption').innerText = data.encryption || "none";
            }
            await loadAllInterfaces();
        } catch(e) { console.error(e); }
    }

    async function loadDevices() {
        try {
            const response = await fetch('/api/router/devices');
            const data = await response.json();
            const div = document.getElementById('devicesTable');

            if(data.leases && data.leases.trim()){
                let html = '<table class="wifi-table"><thead><th>Red</th><th>MAC</th><th>Host/IP</th></thead><tbody>';
                data.leases.split('\n').forEach(line => {
                    if(line.trim()){
                        const parts = line.split(' ');
                        if(parts.length >= 4) html += `<tr><td>LAN / AP Local</td><td>${parts[1]}</td><td>${parts[3]} (${parts[2]})</td></tr>`;
                    }
                });
                html += '</tbody></table>';
                div.innerHTML = html;
            } else div.innerHTML = '<div class="loading">📭 No hay dispositivos asociados por DHCP</div>';
        } catch(e) { document.getElementById('devicesTable').innerHTML = 'Error de lectura DHCP'; }
    }

    async function editWiFi() {
        const n = prompt('Nuevo SSID principal:', document.getElementById('ssid').innerText);
        if(n) { await apiPost(`uci set wireless.@wifi-iface[0].ssid='${n}'; uci commit wireless`); restartWiFi(); }
    }

    // =================================================================
    // NÚCLEO DE ASIGNACIÓN MATRICIAL - CONMUTADOR (OpenWrt Style)
    // =================================================================
    const switchPorts = [
        { id: 0, label: 'WAN' },
        { id: 1, label: 'LAN 1' },
        { id: 2, label: 'LAN 2' },
        { id: 3, label: 'LAN 3' },
        { id: 4, label: 'LAN 4' },
        { id: 5, label: 'CPU (eth0)' },
        { id: 6, label: 'CPU Principal' }
    ];
    // Array en memoria global para manipular las filas de la matriz de forma dinámica
    let currentVlansData = [
        { id: 1, modes: { 0: 'off', 1: 'u', 2: 'off', 3: 'off', 4: 'off', 5: 't', 6: 't' } },
        { id: 2, modes: { 0: 'u', 1: 'off', 2: 'off', 3: 'off', 4: 'off', 5: 't', 6: 't' } }
    ];

    async function apiSwitchPost(command) {
        try {
            const response = await fetch('/api/router/switch-cmd', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify({ cmd: command })
            });
            return await response.json();
        } catch (e) {
            console.error("Error en comunicación Switch:", e);
            return null;
        }
    }

    // 1. Carga los conectores físicos y velocidades en la cabecera
    async function loadPortsLinkStatus() {
        const container = document.getElementById('portsStatusRow');
        if (!container) return;

        try {
            const response = await fetch('/api/router/port-status');
            const data = await response.json();

            if (data.status !== 'success' || !data.ports) {
                await loadPortsLinkStatusFallback();
                return;
            }

            const portLabels = {
                0: 'WAN',
                1: 'LAN 1',
                2: 'LAN 2',
                3: 'LAN 3',
                4: 'LAN 4',
                5: 'CPU (eth0)',
                6: 'CPU Principal'
            };

            let html = '<td class="text-bold-label">Estado del puerto:👇</td>';

            for (let port of data.ports) {
                const label = portLabels[port.id] || `Puerto ${port.id}`;
                const isUp = port.link;
                const speedText = isUp ? `${port.speed} ${port.full_duplex ? 'Full dúplex' : 'Half dúplex'}` : "Sin enlace";
                const isYourPC = (port.id === 1 && isUp);

                html += `<td style="text-align: center;">
                <div class="port-wrapper" style="${isYourPC ? 'background: #e8f8f5; border-radius: 8px; padding: 8px;' : 'padding: 4px;'}">
                    <span style="font-size: 20px; color: ${isUp ? '#2ecc71' : '#95a5a6'}">${isUp ? '🔌' : '🔲'}</span>
                    <span style="font-size: 12px; font-weight: bold; display: block;">${label}</span>
                    <span class="port-text-status" style="color: ${isUp ? '#27ae60' : '#7f8c8d'}; font-size: 10px; display: block;">${speedText}</span>
                    ${isYourPC ? '<span style="font-size: 9px; background: #00b894; color: white; padding: 2px 6px; border-radius: 10px; display: inline-block; margin-top: 4px;">🔌 TU PC</span>' : ''}
                    <div style="margin-top: 8px; display: flex; gap: 4px; justify-content: center;">
                        <button class="btn btn-blue" style="padding: 4px 8px; font-size: 10px;" onclick="setPortState(${port.id}, 'up')">🔛 ACTIVAR</button>
                        <button class="btn btn-red" style="padding: 4px 8px; font-size: 10px;" onclick="setPortState(${port.id}, 'down')">🔒 DESACTIVAR</button>
                    </div>
                </div>
            </td>`;
            }

            html += '<td style="background: transparent;"></td>';
            container.innerHTML = html;

        } catch(e) {
            console.error('Error:', e);
            await loadPortsLinkStatusFallback();
        }
    }

    async function loadPortsLinkStatusFallback() {
        const container = document.getElementById('portsStatusRow');

        // Mapeo directo según tu consola
        const portsManual = [
            { id: 0, label: 'WAN', link: false, speed: '' },
            { id: 1, label: 'LAN 1', link: true, speed: '100baseT Full dúplex' },
            { id: 2, label: 'LAN 2', link: false, speed: '' },
            { id: 3, label: 'LAN 3', link: false, speed: '' },
            { id: 4, label: 'LAN 4', link: false, speed: '' },
            { id: 5, label: 'CPU (eth0)', link: false, speed: '' },
            { id: 6, label: 'CPU Principal', link: true, speed: '1000baseT Full dúplex' }
        ];

        let html = '<td class="text-bold-label">Estado del puerto:👇</td>';

        for (let port of portsManual) {
            const isYourPC = (port.id === 1 && port.link);

            html += `<td>
            <div class="port-wrapper" style="${isYourPC ? 'background: #e8f8f5; border-radius: 8px; padding: 8px;' : 'padding: 4px;'}">
                <span style="font-size: 20px; color: ${port.link ? '#2ecc71' : '#95a5a6'}">${port.link ? '🔌' : '🔲'}</span>
                <span style="font-size: 12px; font-weight: bold; display: block;">${port.label}</span>
                <span class="port-text-status" style="color: ${port.link ? '#27ae60' : '#7f8c8d'}; font-size: 10px; display: block;">${port.speed || 'Sin enlace'}</span>
                ${isYourPC ? '<span style="font-size: 9px; background: #00b894; color: white; padding: 2px 6px; border-radius: 10px; display: inline-block; margin-top: 4px;">🔌 TU PC</span>' : ''}
            </div>
        </td>`;
        }

        html += '<td style="background: transparent;"></td>';
        container.innerHTML = html;
    }


    // 2. Renderiza los selectores matriciales debajo de los puertos físicos
    function loadVlanMatrix() {
        const container = document.getElementById('vlanRowsContainer');
        if (!container) return;
        container.innerHTML = '';

        currentVlansData.forEach((vlan, index) => {
            let rowHtml = `<tr>
                <td><input type="number" class="vlan-id-input" value="${vlan.id}" onchange="updateVlanId(${index}, this.value)"></td>`;

            switchPorts.forEach(port => {
                const currentMode = vlan.modes[port.id] || 'off';
                rowHtml += `<td>
                    <select class="vlan-select" onchange="changePortVlanMode(${index}, ${port.id}, this.value)">
                        <option value="t" ${currentMode === 't' ? 'selected' : ''}>Etiquetado</option>
                        <option value="u" ${currentMode === 'u' ? 'selected' : ''}>Desetiquetado</option>
                        <option value="off" ${currentMode === 'off' ? 'selected' : ''}>Apagado</option>
                    </select>
                </td>`;
            });

            rowHtml += `<td>
                <button class="btn btn-red" style="padding: 4px 10px; font-size: 11px;" onclick="killVlanRow(${index})">ELIMINAR</button>
            </td></tr>`;

            container.innerHTML += rowHtml;
        });
    }

    function changePortVlanMode(rowIndex, portId, selectedMode) {
        currentVlansData[rowIndex].modes[portId] = selectedMode;
        console.log(`Fila interna [${rowIndex}] -> Puerto ${portId} mutado a: ${selectedMode}`);
    }

    function updateVlanId(rowIndex, newId) {
        currentVlansData[rowIndex].id = parseInt(newId) || 0;
    }

    function addNewVlanRow() {
        let nextVlanId = 1;
        if (currentVlansData.length > 0) {
            nextVlanId = Math.max(...currentVlansData.map(v => v.id)) + 1;
        }

        currentVlansData.push({
            id: nextVlanId,
            modes: { 5: 'off', 1: 'off', 2: 'off', 3: 'off', 4: 'off', 0: 'off' }
        });
        loadVlanMatrix();
        showToast("Nueva línea de asignación virtual agregada.");
    }

    function killVlanRow(rowIndex) {
        if(confirm(`¿Desea remover esta fila de asignación de VLAN?`)) {
            currentVlansData.splice(rowIndex, 1);
            loadVlanMatrix();
            showToast("Asignación virtual descartada.");
        }
    }

    function saveSwitchConfig() {
        showToast("💾 Sincronizando matriz de puertos con el router...");

        // Enviamos el array global 'currentVlansData' que mantiene el estado exacto de los selects
        fetch('/api/router/save-switch', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ vlans: currentVlansData })
        })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    showToast("✅ ¡Puertos aplicados y red reiniciada con éxito!");
                    // Recargamos el estado de los conectores físicos para ver si cambiaron los links
                    setTimeout(() => { loadPortsLinkStatus(); }, 3000);
                } else {
                    showToast("❌ Error al aplicar cambios: " + data.message, true);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                showToast("❌ Error de comunicación con el servidor Laravel", true);
            });
    }

    // Función unificada que se activa al pulsar la pestaña del menú lateral
    function loadSwitchView() {
        showView('switch');
        loadPortsLinkStatus();
        loadVlanMatrix();
    }

    async function setPortState(portId, action) {
        const actionText = action === 'up' ? 'activar' : 'desactivar';

        if (!confirm(`¿Estás seguro de ${actionText} el puerto ${getPortLabel(portId)}?`)) return;

        showToast(`⚙️ ${actionText === 'activar' ? 'Activando' : 'Desactivando'} puerto...`);

        try {
            const response = await fetch('/api/router/set-port-state', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    port: portId,
                    state: action
                })
            });

            const data = await response.json();

            if (data.status === 'success') {
                showToast(`✅ ${data.message}`);
                // Recargar el estado de los puertos
                setTimeout(() => loadPortsLinkStatus(), 500);
            } else {
                showToast(`❌ Error: ${data.message}`, true);
            }
        } catch(e) {
            showToast(`❌ Error de comunicación`, true);
        }
    }

    function getPortLabel(portId) {
        const labels = {
            0: 'WAN',
            1: 'LAN 1',
            2: 'LAN 2',
            3: 'LAN 3',
            4: 'LAN 4',
            5: 'CPU (eth0)',
            6: 'CPU Principal'
        };
        return labels[portId] || `Puerto ${portId}`;
    }

    // Delegación de eventos para botones de eliminar (funciona para elementos creados dinámicamente)

    window.deleteInterfaceBySSID = deleteInterfaceBySSID;

</script>
</body>
</html>
