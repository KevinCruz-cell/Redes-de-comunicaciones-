<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rutas Estáticas - OpenWrt</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; }
        .header { background: white; border-radius: 15px; padding: 20px 30px; margin-bottom: 30px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        .header h1 { color: #4e73df; display: flex; align-items: center; gap: 12px; }
        .info { background: #e8f4f8; border-radius: 8px; padding: 10px 20px; color: #2c3e50; font-size: 0.9em; }
        .tabs { display: flex; gap: 10px; margin-bottom: 25px; }
        .tab { padding: 12px 25px; background: white; border: none; border-radius: 10px; cursor: pointer; font-weight: 600; color: #5a5c69; transition: 0.3s; }
        .tab.active { background: #4e73df; color: white; }
        .card { background: white; border-radius: 15px; padding: 25px; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); }
        .card-title { font-size: 1.2em; color: #4e73df; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e3e6f0; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e3e6f0; }
        th { background: #f8f9fc; color: #4e73df; }
        tr:hover { background: #f8f9fc; }
        .empty { text-align: center; padding: 40px; color: #858796; font-style: italic; }
        .btn-primary { background: linear-gradient(135deg, #4e73df, #224abe); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; }
        .btn-danger { background: #e74a3b; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; }
        .btn-warning { background: #fd7e14; color: white; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; margin-right: 5px; }
        .btn-secondary { background: #e3e6f0; color: #5a5c69; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; }
        .form-grid { display: grid; grid-template-columns: repeat(2,1fr); gap: 20px; }
        .full-width { grid-column: span 2; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 8px; color: #5a5c69; font-weight: 500; }
        input, select { width: 100%; padding: 10px 12px; border: 2px solid #e3e6f0; border-radius: 8px; background: #f8f9fc; }
        input:focus, select:focus { outline: none; border-color: #4e73df; background: white; }
        .checkbox-group { display: flex; align-items: center; gap: 10px; }
        .checkbox-group input { width: auto; }
        .alert { padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; animation: slideIn 0.3s ease; }
        .alert.success { background: #d4edda; color: #155724; border-left: 5px solid #28a745; }
        .alert.error { background: #f8d7da; color: #721c24; border-left: 5px solid #dc3545; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        details summary { color: #4e73df; cursor: pointer; font-weight: 500; margin: 15px 0; padding: 10px 0; }
        .form-actions { margin-top: 25px; padding-top: 20px; border-top: 1px solid #e3e6f0; display: flex; gap: 15px; justify-content: flex-end; }
        @media (max-width: 768px) { .form-grid { grid-template-columns: 1fr; } .full-width { grid-column: span 1; } }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1><i class="fas fa-route"></i> Rutas Estáticas</h1>
        <div class="info"><i class="fas fa-sync-alt"></i> Datos sincronizados automáticamente</div>
    </div>

    @if(session('success'))
        <div class="alert success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif

    <div class="tabs">
        <button class="tab active" onclick="showTab('ipv4')"><i class="fas fa-globe"></i> Rutas IPv4 estáticas</button>
        <button class="tab" onclick="showTab('ipv6')"><i class="fas fa-globe"></i> Rutas IPv6 estáticas</button>
    </div>

    <!-- IPv4 -->
    <div id="tab-ipv4">
        <div class="card">
            <div class="card-title">
                <span><i class="fas fa-table"></i> Rutas IPv4 estáticas</span>
                <button class="btn-primary" onclick="mostrarFormulario('ipv4', 'add')"><i class="fas fa-plus"></i> AÑADIR</button>
            </div>
            <table>
                <thead>
                <tr>
                    <th>Interfaz</th><th>Destino</th><th>Máscara</th><th>Gateway</th><th>Métrica</th><th>MTU</th><th>Tabla</th><th>Acciones</th>
                </tr>
                </thead>
                <tbody id="tabla-ipv4-body">
                <tr class="empty"><td colspan="8">Cargando rutas IPv4...</td></tr>
                </tbody>
            </table>
        </div>
        <div id="form-ipv4" style="display:none;" class="card"></div>
    </div>

    <!-- IPv6 -->
    <div id="tab-ipv6" style="display:none;">
        <div class="card">
            <div class="card-title">
                <span><i class="fas fa-table"></i> Rutas IPv6 estáticas</span>
                <button class="btn-primary" onclick="mostrarFormulario('ipv6', 'add')"><i class="fas fa-plus"></i> AÑADIR</button>
            </div>
            <table>
                <thead>
                <tr>
                    <th>Interfaz</th><th>Destino</th><th>Gateway</th><th>Métrica</th><th>MTU</th><th>Tabla</th><th>Acciones</th>
                </tr>
                </thead>
                <tbody id="tabla-ipv6-body">
                <tr class="empty"><td colspan="7">Cargando rutas IPv6...</td></tr>
                </tbody>
            </table>
        </div>
        <div id="form-ipv6" style="display:none;" class="card"></div>
    </div>
</div>

<script>
    const interfaces = { lan: 'lan (br-lan)', wan: 'wan (eth0.2)', wwan: 'wwan (wlan0)' };
    const tablasRuta = { local: 'local (255)', main: 'main (254)', default: 'default (253)' };
    const tiposRuta = { unicast: 'unicast', local: 'local', broadcast: 'broadcast', multicast: 'multicast', unreachable: 'unreachable', prohibit: 'prohibit', blackhole: 'blackhole', anycast: 'anycast' };
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function showTab(tab) {
        document.getElementById('tab-ipv4').style.display = tab === 'ipv4' ? 'block' : 'none';
        document.getElementById('tab-ipv6').style.display = tab === 'ipv6' ? 'block' : 'none';
        document.querySelectorAll('.tab').forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
    }

    function generarOptions(obj, selected = '') {
        let html = '';
        for (const [key, value] of Object.entries(obj)) {
            const sel = (selected == key) ? 'selected' : '';
            html += `<option value="${key}" ${sel}>${value}</option>`;
        }
        return html;
    }

    async function cargarRutas() {
        try {
            const response = await fetch('/api/router3/rutas', {
                headers: { 'X-CSRF-TOKEN': csrfToken }
            });
            const data = await response.json();

            if (data.status === 'success') {
                // IPv4
                const ipv4Body = document.getElementById('tabla-ipv4-body');
                if (data.ipv4.length === 0) {
                    ipv4Body.innerHTML = '<tr class="empty"><td colspan="8">No hay rutas IPv4 configuradas</td></tr>';
                } else {
                    ipv4Body.innerHTML = data.ipv4.map(r => `
                        <tr>
                            <td>${escapeHtml(r.interface)}</td>
                            <td>${escapeHtml(r.target)}</td>
                            <td>${escapeHtml(r.netmask)}</td>
                            <td>${escapeHtml(r.gateway)}</td>
                            <td>${escapeHtml(r.metric)}</td>
                            <td>${escapeHtml(r.mtu)}</td>
                            <td>${escapeHtml(r.table)}</td>
                            <td>
                                <button class="btn-warning" onclick='editarRuta("ipv4", ${JSON.stringify(r)})'><i class="fas fa-edit"></i></button>
                                <button class="btn-danger" onclick="eliminarRuta('ipv4', '${r.target}', '${r.gateway}')"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    `).join('');
                }

                // IPv6
                const ipv6Body = document.getElementById('tabla-ipv6-body');
                if (data.ipv6.length === 0) {
                    ipv6Body.innerHTML = '<tr class="empty"><td colspan="7">No hay rutas IPv6 configuradas</td></tr>';
                } else {
                    ipv6Body.innerHTML = data.ipv6.map(r => `
                        <tr>
                            <td>${escapeHtml(r.interface)}</td>
                            <td>${escapeHtml(r.target)}</td>
                            <td>${escapeHtml(r.gateway)}</td>
                            <td>${escapeHtml(r.metric)}</td>
                            <td>${escapeHtml(r.mtu)}</td>
                            <td>${escapeHtml(r.table)}</td>
                            <td>
                                <button class="btn-warning" onclick='editarRuta("ipv6", ${JSON.stringify(r)})'><i class="fas fa-edit"></i></button>
                                <button class="btn-danger" onclick="eliminarRuta('ipv6', '${r.target}', '${r.gateway}')"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    `).join('');
                }
            }
        } catch (error) {
            console.error('Error cargando rutas:', error);
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    async function eliminarRuta(tipo, target, gateway) {
        if (!confirm('¿Eliminar esta ruta?')) return;

        try {
            const response = await fetch('/api/router3/ruta/eliminar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ tipo: tipo, target: target, gateway: gateway })
            });
            const data = await response.json();
            if (data.status === 'success') {
                alert(data.message);
                cargarRutas();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            alert('Error de comunicación');
        }
    }

    function mostrarFormulario(tipo, modo, datos = null) {
        const container = document.getElementById(`form-${tipo}`);
        const esEdicion = modo === 'edit';
        const accion = esEdicion ? 'update' : 'add';
        const titulo = esEdicion ? `Editar ruta ${tipo.toUpperCase()}` : `Agregar ruta ${tipo.toUpperCase()}`;

        const metrica = esEdicion ? datos.metric : '0';
        const mtu = esEdicion ? datos.mtu : '1500';
        const tabla = esEdicion ? datos.table : 'main';
        const tipo_ruta = esEdicion ? datos.type : 'unicast';
        const origen = esEdicion ? (datos.source || '') : '';
        const onlink = esEdicion && datos.onlink === '1';

        let html = `<h3><i class="fas fa-${esEdicion ? 'edit' : 'plus-circle'}"></i> ${titulo}</h3>
        <form onsubmit="return guardarRuta(event, '${tipo}', '${accion}')">
            ${esEdicion ? `<input type="hidden" id="ruta_id" value="${datos.id}">` : ''}
            <h4 style="color:#4e73df; margin:15px 0 10px 0;">Configuración general</h4>
            <div class="form-grid">`;

        if (tipo === 'ipv4') {
            const interfazSel = esEdicion ? datos.interface : 'lan';
            html += `
                <div class="form-group"><label>Interfaz</label><select id="interfaz">${generarOptions(interfaces, interfazSel)}</select></div>
                <div class="form-group"><label>Destino</label><input id="destino" value="${esEdicion ? datos.target : ''}" placeholder="192.168.20.0" required></div>
                <div class="form-group"><label>Máscara</label><input id="mascara" value="${esEdicion ? datos.netmask : '255.255.255.0'}" placeholder="255.255.255.0"></div>
                <div class="form-group"><label>Gateway</label><input id="gateway" value="${esEdicion ? datos.gateway : ''}" placeholder="192.168.10.211" required></div>`;
        } else {
            const interfazSel = esEdicion ? datos.interface : 'lan';
            html += `
                <div class="form-group"><label>Interfaz</label><select id="interfaz">${generarOptions(interfaces, interfazSel)}</select></div>
                <div class="form-group"><label>Destino (CIDR)</label><input id="destino" value="${esEdicion ? datos.target : ''}" placeholder="2001:db8::/32" required></div>
                <div class="form-group"><label>Gateway IPv6</label><input id="gateway" value="${esEdicion ? datos.gateway : ''}" placeholder="fe80::1" required></div>`;
        }

        html += `</div>
            <details>
                <summary><i class="fas fa-cog"></i> Configuración avanzada</summary>
                <div class="form-grid" style="margin-top:15px;">
                    <div class="form-group"><label>Métrica</label><input id="metrica" value="${metrica}" type="number"></div>
                    <div class="form-group"><label>MTU</label><input id="mtu" value="${mtu}" type="number"></div>
                    <div class="form-group"><label>Tipo de ruta</label><select id="tipo_ruta">${generarOptions(tiposRuta, tipo_ruta)}</select></div>
                    <div class="form-group"><label>Tabla de ruta</label><select id="tabla">${generarOptions(tablasRuta, tabla)}</select></div>
                    <div class="form-group"><label>Dirección de origen</label><input id="origen" value="${origen}" placeholder="Automático"></div>
                    <div class="form-group"><label class="checkbox-group"><input type="checkbox" id="onlink" ${onlink ? 'checked' : ''}> Ruta en enlace</label></div>
                </div>
            </details>
            <div class="form-actions">
                <button type="button" class="btn-secondary" onclick="cancelarFormulario('${tipo}')">Cancelar</button>
                <button type="submit" class="btn-primary">Guardar</button>
            </div>
        </form>`;

        container.innerHTML = html;
        container.style.display = 'block';
        container.scrollIntoView({ behavior: 'smooth' });
    }

    function cancelarFormulario(tipo) {
        document.getElementById(`form-${tipo}`).style.display = 'none';
    }

    function editarRuta(tipo, datos) {
        mostrarFormulario(tipo, 'edit', datos);
    }

    async function guardarRuta(event, tipo, accion) {
        event.preventDefault();

        const data = {
            tipo: tipo,
            accion: accion,
            interfaz: document.getElementById('interfaz').value,
            destino: document.getElementById('destino').value,
            gateway: document.getElementById('gateway').value,
            metrica: document.getElementById('metrica')?.value || '0',
            mtu: document.getElementById('mtu')?.value || '1500',
            tabla: document.getElementById('tabla')?.value || 'main',
            tipo_ruta: document.getElementById('tipo_ruta')?.value || 'unicast',
            origen: document.getElementById('origen')?.value || '',
            onlink: document.getElementById('onlink')?.checked ? '1' : '0'
        };

        if (tipo === 'ipv4') {
            data.mascara = document.getElementById('mascara')?.value || '255.255.255.255';
        }

        if (accion === 'update') {
            data.id = document.getElementById('ruta_id').value;
        }

        try {
            const response = await fetch(`/api/router3/ruta/${accion === 'add' ? 'agregar' : 'actualizar'}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.status === 'success') {
                alert(result.message);
                cancelarFormulario(tipo);
                cargarRutas();
            } else {
                alert('Error: ' + result.message);
            }
        } catch (error) {
            alert('Error de comunicación');
        }
    }

    // Cargar rutas al iniciar
    cargarRutas();
</script>
</body>
</html>
