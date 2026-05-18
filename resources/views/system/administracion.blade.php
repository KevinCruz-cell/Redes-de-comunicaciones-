@extends('layouts.app')

@section('title', 'Administracion')
@section('header_title', 'Administracion')

@push('styles')
<style>
    .help-text {
        color: #777;
        margin-bottom: 24px;
    }

    .admin-panel {
        background: white;
        border: 1px solid #d5d5d5;
        box-shadow: 0 2px 5px rgba(0,0,0,.12);
        padding: 15px;
    }

    .admin-tabs {
        display: flex;
        background: #d1d1d1;
        border: 1px solid #cccccc;
        border-bottom: none;
    }

    .admin-tab {
        padding: 13px 16px;
        border: none;
        background: #d1d1d1;
        cursor: pointer;
        font-size: 14px;
    }

    .admin-tab.active {
        background: white;
    }

    .admin-tab-content {
        border: 1px solid #cccccc;
        padding: 18px 0 28px;
        min-height: 430px;
        background: white;
    }

    .hidden {
        display: none;
    }

    .form-row {
        display: flex;
        align-items: flex-start;
        margin: 12px 0;
    }

    .form-label {
        width: 300px;
        text-align: right;
        padding: 7px 12px 0 0;
        color: #000;
    }

    .form-field {
        width: 380px;
    }

    .form-field.wide {
        width: 680px;
    }

    .input-line,
    .select-line {
        width: 100%;
        border: none;
        border-bottom: 1px solid #999;
        padding: 6px 0;
        font-size: 14px;
        outline: none;
        background: transparent;
    }

    .input-line:hover,
    .select-line:hover {
        border-bottom: 2px solid #00a3cc;
    }

    .input-line:focus,
    .select-line:focus {
        border-bottom: 2px solid #00a3cc;
    }

    .checkbox-field {
        transform: scale(1.2);
        margin-top: 8px;
    }

    .field-help {
        color: #777;
        margin-top: 6px;
        line-height: 1.4;
    }

    .key-item {
        margin-bottom: 14px;
        padding-bottom: 12px;
        border-bottom: 1px solid #cfcfcf;
    }

    .key-title {
        font-weight: bold;
        color: #666;
        margin-bottom: 4px;
    }

    .key-type {
        color: #555;
        margin-bottom: 4px;
    }

    .key-preview {
        display: inline-block;
        background: #e1e1e1;
        color: #111;
        padding: 2px 5px;
        margin: 2px 0;
        font-family: Consolas, monospace;
        font-size: 12px;
        max-width: 420px;
        overflow: hidden;
        white-space: nowrap;
        vertical-align: middle;
    }

    .key-delete {
        background: #d84638;
        color: white;
        border: none;
        padding: 4px 9px;
        border-radius: 3px;
        cursor: pointer;
        margin-left: 10px;
        font-weight: bold;
    }

    .key-delete:hover {
        background: #c0392b;
    }

    .admin-actions {
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
        margin: 15px 20px 0;
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

<p class="help-text">
    Aqui puede cambiar la contrasena del enrutador, configurar el acceso SSH y administrar claves publicas.
</p>

<section class="admin-panel">
    <div class="admin-tabs">
        <button class="admin-tab active" type="button" onclick="cambiarTab('password', this)">Contrasena del enrutador</button>
        <button class="admin-tab" type="button" onclick="cambiarTab('ssh', this)">Acceso SSH</button>
        <button class="admin-tab" type="button" onclick="cambiarTab('keys', this)">Claves SSH</button>
    </div>

    <div id="tab-password" class="admin-tab-content">
        <div class="form-row">
            <label class="form-label" for="pass1">Nueva contrasena</label>
            <div class="form-field">
                <input class="input-line" type="password" id="pass1">
            </div>
        </div>

        <div class="form-row">
            <label class="form-label" for="pass2">Confirmar contrasena</label>
            <div class="form-field">
                <input class="input-line" type="password" id="pass2">
                <div class="field-help">
                    La nueva contrasena se aplicara al usuario root del router. Si la cambias, actualiza tambien ROUTER_PASSWORD en tu archivo .env.
                </div>
            </div>
        </div>
    </div>

    <div id="tab-ssh" class="admin-tab-content hidden">
        <div class="form-row">
            <label class="form-label" for="port">Puerto</label>
            <div class="form-field">
                <input class="input-line" type="number" id="port" min="1" max="65535">
            </div>
        </div>

        <div class="form-row">
            <label class="form-label" for="interface">Interfaz</label>
            <div class="form-field">
                <input class="input-line" type="text" id="interface" placeholder="lan">
                <div class="field-help">Puede dejarse vacio para usar la configuracion predeterminada.</div>
            </div>
        </div>

        <div class="form-row">
            <label class="form-label" for="passAuth">Autenticacion por contrasena</label>
            <div class="form-field">
                <input class="checkbox-field" type="checkbox" id="passAuth">
            </div>
        </div>

        <div class="form-row">
            <label class="form-label" for="rootLogin">Permitir root con contrasena</label>
            <div class="form-field">
                <input class="checkbox-field" type="checkbox" id="rootLogin">
            </div>
        </div>

        <div class="form-row">
            <label class="form-label" for="gateway">Puertos del gateway</label>
            <div class="form-field">
                <input class="checkbox-field" type="checkbox" id="gateway">
            </div>
        </div>
    </div>

    <div id="tab-keys" class="admin-tab-content hidden">
        <div class="form-row">
            <label class="form-label">Claves SSH guardadas</label>
            <div class="form-field wide">
                <div id="keysList">
                    Cargando claves...
                </div>
            </div>
        </div>

        <div class="form-row">
            <label class="form-label" for="ssh_key">Nueva clave SSH publica</label>
            <div class="form-field wide">
                <input class="input-line" type="file" id="ssh_key" accept=".pub,.txt">
                <div class="field-help">
                    Selecciona una clave publica SSH, por ejemplo id_rsa.pub o id_ed25519.pub.
                </div>
            </div>
        </div>
    </div>

    <div id="statusBox" class="status-box"></div>
</section>

<div class="admin-actions">
    <button class="btn-main" type="button" onclick="guardarActual()">GUARDAR Y APLICAR ▾</button>
    <button class="btn-blue" type="button" onclick="guardarActual()">GUARDAR</button>
    <button class="btn-red" type="button" onclick="refrescarActual()">RESTABLECER</button>
</div>

<div id="loadingMessage" class="loading-message">
    Aplicando configuracion...
</div>

@endsection

@push('scripts')
<script>
    let tabActual = 'password';

    document.addEventListener('DOMContentLoaded', () => {
        cargarDatos();
    });

    function cambiarTab(tab, button) {
        tabActual = tab;

        document.querySelectorAll('.admin-tab-content').forEach(item => item.classList.add('hidden'));
        document.querySelectorAll('.admin-tab').forEach(item => item.classList.remove('active'));

        document.getElementById('tab-' + tab).classList.remove('hidden');
        button.classList.add('active');

        if (tab === 'ssh') {
            cargarDatos();
        }

        if (tab === 'keys') {
            cargarKeys();
        }
    }

    function refrescarActual() {
        if (tabActual === 'keys') {
            cargarKeys();
        } else {
            cargarDatos();
        }
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
            const response = await fetch('/administracion/data', {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('No se pudieron cargar los datos de administracion.');
            }

            const data = await response.json();

            document.getElementById('port').value = data.port || '22';
            document.getElementById('interface').value = data.interface || '';

            document.getElementById('passAuth').checked = data.passAuth === 'on' || data.passAuth === '1';
            document.getElementById('rootLogin').checked = data.rootLogin === '1' || data.rootLogin === 'on';
            document.getElementById('gateway').checked = data.gateway === 'on' || data.gateway === '1';

        } catch (error) {
            mostrarMensaje('error', error.message);
        }
    }

    async function cargarKeys() {
        try {
            const response = await fetch('/administracion/keys', {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (!response.ok || !result.ok) {
                throw new Error(result.error || 'No se pudieron cargar las claves SSH.');
            }

            const box = document.getElementById('keysList');

            if (!result.keys || result.keys.length === 0) {
                box.innerHTML = '<div class="field-help">No hay claves SSH guardadas.</div>';
                return;
            }

            box.innerHTML = '';

            result.keys.forEach(key => {
                const div = document.createElement('div');
                div.className = 'key-item';

                div.innerHTML = `
                    <div class="key-title">${escapeHtml(key.label)}</div>
                    <div class="key-type">${escapeHtml(key.bits)}</div>
                    <div>
                        <span class="key-preview">${escapeHtml(key.preview1)}</span><br>
                        <span class="key-preview">${escapeHtml(key.preview2)}</span>
                        <button class="key-delete" type="button">×</button>
                    </div>
                `;

                div.querySelector('.key-delete').addEventListener('click', () => {
                    eliminarKey(key.full);
                });

                box.appendChild(div);
            });

        } catch (error) {
            mostrarMensaje('error', error.message);
        }
    }

    async function guardarActual() {
        if (tabActual === 'password') {
            await updatePassword();
        } else if (tabActual === 'ssh') {
            await updateSSH();
        } else if (tabActual === 'keys') {
            await uploadKey();
        }
    }

    async function updatePassword() {
        const p1 = document.getElementById('pass1').value;
        const p2 = document.getElementById('pass2').value;

        if (!p1 || !p2) {
            mostrarMensaje('error', 'Escribe y confirma la nueva contrasena.');
            return;
        }

        if (p1 !== p2) {
            mostrarMensaje('error', 'Las contrasenas no coinciden.');
            return;
        }

        try {
            mostrarCarga();

            const response = await fetch('/administracion/password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    password: p1
                })
            });

            const result = await response.json();

            ocultarCarga();

            if (!response.ok || !result.ok) {
                throw new Error(result.error || 'No se pudo actualizar la contrasena.');
            }

            document.getElementById('pass1').value = '';
            document.getElementById('pass2').value = '';

            mostrarMensaje('ok', result.message || 'Contrasena actualizada correctamente.');

        } catch (error) {
            ocultarCarga();
            mostrarMensaje('error', error.message);
        }
    }

    async function updateSSH() {
        try {
            mostrarCarga();

            const response = await fetch('/administracion/ssh', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    port: document.getElementById('port').value,
                    interface: document.getElementById('interface').value,
                    passAuth: document.getElementById('passAuth').checked,
                    rootLogin: document.getElementById('rootLogin').checked,
                    gateway: document.getElementById('gateway').checked
                })
            });

            const result = await response.json();

            ocultarCarga();

            if (!response.ok || !result.ok) {
                throw new Error(result.error || 'No se pudo actualizar SSH.');
            }

            mostrarMensaje('ok', result.message || 'SSH actualizado correctamente.');
            await cargarDatos();

        } catch (error) {
            ocultarCarga();
            mostrarMensaje('error', error.message);
        }
    }

    async function uploadKey() {
        const file = document.getElementById('ssh_key').files[0];

        if (!file) {
            mostrarMensaje('error', 'Selecciona una clave publica SSH.');
            return;
        }

        try {
            mostrarCarga();

            const formData = new FormData();
            formData.append('ssh_key', file);

            const response = await fetch('/administracion/key', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            const result = await response.json();

            ocultarCarga();

            if (!response.ok || !result.ok) {
                throw new Error(result.error || 'No se pudo subir la clave.');
            }

            document.getElementById('ssh_key').value = '';

            mostrarMensaje('ok', result.message || 'Clave SSH subida correctamente.');
            await cargarKeys();

        } catch (error) {
            ocultarCarga();
            mostrarMensaje('error', error.message);
        }
    }

    async function eliminarKey(key) {
        if (!confirm('¿Seguro que quieres eliminar esta clave SSH?')) {
            return;
        }

        try {
            mostrarCarga();

            const response = await fetch('/administracion/key/delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    key: key
                })
            });

            const result = await response.json();

            ocultarCarga();

            if (!response.ok || !result.ok) {
                throw new Error(result.error || 'No se pudo eliminar la clave.');
            }

            mostrarMensaje('ok', result.message || 'Clave SSH eliminada correctamente.');
            await cargarKeys();

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
</script>
@endpush
