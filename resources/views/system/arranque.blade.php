@extends('layouts.app')

@section('title', 'Arranque')
@section('header_title', 'Arranque')

@push('styles')
<style>
    .boot-panel {
        background: white;
        border: 1px solid #d5d5d5;
        box-shadow: 0 2px 5px rgba(0,0,0,.12);
        padding: 15px;
    }

    .boot-tabs {
        display: flex;
        background: #d1d1d1;
        border: 1px solid #cccccc;
        border-bottom: none;
    }

    .boot-tab {
        padding: 11px 16px;
        border: none;
        background: #d1d1d1;
        cursor: pointer;
        font-size: 14px;
    }

    .boot-tab.active {
        background: white;
    }

    .boot-tab-content {
        border: 1px solid #cccccc;
        background: white;
        min-height: 430px;
        padding: 14px;
    }

    .hidden {
        display: none;
    }

    .boot-desc {
        margin: 0 0 10px;
        font-size: 13px;
        color: #111;
    }

    .boot-warning {
        font-weight: bold;
        font-size: 13px;
        margin-bottom: 10px;
    }

    .service-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .service-table th {
        background: #f7f7f7;
        border: 1px solid #d0d0d0;
        padding: 7px;
        text-align: center;
        font-weight: bold;
    }

    .service-table td {
        border: 1px solid #d8d8d8;
        padding: 7px;
        text-align: center;
        vertical-align: middle;
    }

    .service-table tbody tr:nth-child(even) {
        background: #eeeeee;
    }

    .service-table tbody tr:nth-child(odd) {
        background: #f8f8f8;
    }

    .service-name {
        font-weight: normal;
        color: #111;
    }

    .boot-btn {
        border: none;
        padding: 6px 12px;
        border-radius: 3px;
        color: white;
        cursor: pointer;
        font-weight: bold;
        font-size: 12px;
        margin-left: 5px;
    }

    .btn-enabled {
        background: #2b6cb0;
    }

    .btn-disabled {
        background: #777;
    }

    .btn-action {
        background: #4db6d3;
    }

    .btn-danger {
        background: #d84638;
    }

    .startup-label {
        font-weight: bold;
        margin-bottom: 4px;
        font-size: 13px;
    }

    .startup-textarea {
        width: 100%;
        min-height: 330px;
        border: 1px solid #999;
        padding: 8px;
        font-family: Consolas, monospace;
        font-size: 13px;
        resize: vertical;
        background: white;
    }

    .boot-actions {
        margin-top: 20px;
        border-top: 1px solid #ccc;
        padding-top: 18px;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn-main {
        background: #51bad8;
        color: white;
        border: none;
        padding: 13px 22px;
        border-radius: 3px;
        cursor: pointer;
        font-weight: bold;
    }

    .btn-blue {
        background: #1d6dae;
        color: white;
        border: none;
        padding: 13px 18px;
        border-radius: 3px;
        cursor: pointer;
        font-weight: bold;
    }

    .btn-red {
        background: #d84638;
        color: white;
        border: none;
        padding: 13px 18px;
        border-radius: 3px;
        cursor: pointer;
        font-weight: bold;
    }

    .status-box {
        margin-top: 15px;
        padding: 10px;
        display: none;
        border-radius: 3px;
    }

    .status-ok {
        background: #dff0d8;
        color: #2d662d;
        border: 1px solid #bad7af;
    }

    .status-error {
        background: #f2dede;
        color: #8a1f1f;
        border: 1px solid #e0b4b4;
    }

    .loading-message {
        display: none;
        position: fixed;
        top: 85px;
        left: 50%;
        transform: translateX(-50%);
        background: #5bb1c9;
        color: white;
        padding: 15px 30px;
        border-radius: 8px;
        font-weight: bold;
        z-index: 9999;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }
</style>
@endpush

@section('content')

<section class="boot-panel">
    <div class="boot-tabs">
        <button class="boot-tab active" type="button" onclick="cambiarTab('servicios', this)">Scripts de inicio</button>
        <button class="boot-tab" type="button" onclick="cambiarTab('local', this)">Arranque local</button>
    </div>

    <div id="tab-servicios" class="boot-tab-content">
        <p class="boot-desc">
            Puede activar o desactivar los scripts de inicio instalados aqui. Los cambios se aplicaran despues de que se reinicie el dispositivo.
        </p>

        <div class="boot-warning">
            Advertencia: Si desactivas los scripts de inicio esenciales como "network", ¡Tu dispositivo podria volverse inaccesible!
        </div>

        <table class="service-table">
            <thead>
            <tr>
                <th style="width: 180px;">Prioridad de inicio</th>
                <th>Nombre del script de inicio</th>
                <th style="width: 420px;"></th>
            </tr>
            </thead>
            <tbody id="servicesBody">
            <tr>
                <td colspan="3">Cargando servicios...</td>
            </tr>
            </tbody>
        </table>
    </div>

    <div id="tab-local" class="boot-tab-content hidden">
        <div class="startup-label">
            Contenido de /etc/rc.local. Ponga sus propios comandos aqui (antes de 'exit 0') para ejecutarlos al final del proceso de inicio.
        </div>

        <textarea id="localStartup" class="startup-textarea"></textarea>
    </div>

    <div id="statusBox" class="status-box"></div>
</section>

<div class="boot-actions">
    <button class="btn-main" type="button" onclick="guardarActual()">GUARDAR Y APLICAR ▾</button>
    <button class="btn-blue" type="button" onclick="guardarActual()">GUARDAR</button>
    <button class="btn-red" type="button" onclick="cargarDatos()">RESTABLECER</button>
</div>

<div id="loadingMessage" class="loading-message">
    Aplicando configuracion...
</div>

@endsection

@push('scripts')
<script>
    let tabActual = 'servicios';

    document.addEventListener('DOMContentLoaded', () => {
        cargarDatos();
    });

    function cambiarTab(tab, button) {
        tabActual = tab;

        document.querySelectorAll('.boot-tab-content').forEach(item => item.classList.add('hidden'));
        document.querySelectorAll('.boot-tab').forEach(item => item.classList.remove('active'));

        document.getElementById('tab-' + tab).classList.remove('hidden');
        button.classList.add('active');
    }

    function mostrarCarga() {
        document.getElementById('loadingMessage').style.display = 'block';
    }

    function ocultarCarga() {
        document.getElementById('loadingMessage').style.display = 'none';
    }

    function mostrarMensaje(tipo, mensaje) {
        const box = document.getElementById('statusBox');

        box.className = 'status-box ' + (tipo === 'ok' ? 'status-ok' : 'status-error');
        box.textContent = mensaje;
        box.style.display = 'block';

        setTimeout(() => {
            box.style.display = 'none';
        }, 4500);
    }

    async function cargarDatos() {
        try {
            const response = await fetch('/arranque/data', {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (!response.ok || !result.ok) {
                throw new Error(result.error || 'No se pudieron cargar los datos de arranque.');
            }

            renderServicios(result.services || []);
            if (result.local_startup !== undefined) {
                document.getElementById('localStartup').value = result.local_startup;
            }

        } catch (error) {
            mostrarMensaje('error', error.message);
        }
    }

    function renderServicios(services) {
        const tbody = document.getElementById('servicesBody');

        if (!services.length) {
            tbody.innerHTML = '<tr><td colspan="3">No se encontraron servicios.</td></tr>';
            return;
        }

        tbody.innerHTML = '';

        services.forEach(service => {
            const tr = document.createElement('tr');

            const estadoBtn = service.enabled
                ? `<button class="boot-btn btn-enabled" type="button" onclick="accionServicio('${escapeJs(service.name)}', 'disable')">ACTIVADO</button>`
                : `<button class="boot-btn btn-disabled" type="button" onclick="accionServicio('${escapeJs(service.name)}', 'enable')">DESACTIVADO</button>`;

            tr.innerHTML = `
                <td>${escapeHtml(service.priority || '-')}</td>
                <td class="service-name">${escapeHtml(service.name)}</td>
                <td>
                    ${estadoBtn}
                    <button class="boot-btn btn-action" type="button" onclick="accionServicio('${escapeJs(service.name)}', 'start')">INICIAR</button>
                    <button class="boot-btn btn-action" type="button" onclick="accionServicio('${escapeJs(service.name)}', 'restart')">REINICIAR</button>
                    <button class="boot-btn btn-action" type="button" onclick="accionServicio('${escapeJs(service.name)}', 'stop')">DETENER</button>
                </td>
            `;

            tbody.appendChild(tr);
        });
    }

    async function accionServicio(service, action) {
        if ((service === 'network' || service === 'firewall' || service === 'dropbear') && action === 'disable') {
            const confirmar = confirm('Cuidado: desactivar ' + service + ' puede dejar el router inaccesible. ¿Deseas continuar?');

            if (!confirmar) {
                return;
            }
        }

        try {
            mostrarCarga();

            const response = await fetch('/arranque/action', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    service: service,
                    action: action
                })
            });

            const result = await response.json();

            ocultarCarga();

            if (!response.ok || !result.ok) {
                throw new Error(result.error || 'No se pudo aplicar la accion.');
            }

            mostrarMensaje('ok', result.message || 'Accion aplicada correctamente.');
            await cargarDatos();

        } catch (error) {
            ocultarCarga();
            mostrarMensaje('error', error.message);
        }
    }

    async function guardarActual() {
        if (tabActual === 'local') {
            await guardarLocalStartup();
        } else {
            mostrarMensaje('ok', 'Para scripts de inicio usa los botones de cada servicio.');
        }
    }

    async function guardarLocalStartup() {
        try {
            mostrarCarga();

            const contenidoActual = document.getElementById('localStartup').value;

            const response = await fetch('/arranque/local-startup', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    local_startup: contenidoActual
                })
            });

            const result = await response.json();

            ocultarCarga();

            if (!response.ok || !result.ok) {
                throw new Error(result.error || 'No se pudo guardar el arranque local.');
            }

            if (result.local_startup !== undefined && result.local_startup.trim() !== '') {
                document.getElementById('localStartup').value = result.local_startup;
            } else {
                document.getElementById('localStartup').value = contenidoActual;
            }

            mostrarMensaje('ok', result.message || 'Arranque local guardado correctamente.');

        } catch (error) {
            ocultarCarga();
            mostrarMensaje('error', error.message);
        }
    }

    function escapeHtml(text) {
        return String(text || '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function escapeJs(text) {
        return String(text || '')
            .replaceAll('\\', '\\\\')
            .replaceAll("'", "\\'");
    }
</script>
@endpush
