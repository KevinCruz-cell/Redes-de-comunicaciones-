@extends('layouts.app')

@section('title', 'Sistema')
@section('header_title', 'Sistema')

@push('styles')
<style>
    .help-text {
        color: #777;
        margin-bottom: 24px;
    }

    .system-panel {
        background: white;
        border: 1px solid #d5d5d5;
        box-shadow: 0 2px 5px rgba(0,0,0,.12);
        padding: 15px;
    }

    .system-tabs {
        display: flex;
        background: #d1d1d1;
        border: 1px solid #cccccc;
        border-bottom: none;
    }

    .system-tab {
        padding: 13px 16px;
        border: none;
        background: #d1d1d1;
        cursor: pointer;
        font-size: 14px;
    }

    .system-tab.active {
        background: white;
    }

    .system-tab-content {
        border: 1px solid #cccccc;
        padding: 14px 0 22px;
        min-height: 470px;
        background: white;
    }

    .form-row {
        display: flex;
        align-items: flex-start;
        margin: 10px 0;
    }

    .form-label {
        width: 270px;
        text-align: right;
        padding: 7px 12px 0 0;
        color: #000;
    }

    .form-field {
        width: 360px;
    }

    .form-field.wide {
        width: 520px;
    }

    .input-line,
    .textarea-field,
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
    .select-line:hover,
    .textarea-field:hover {
        border-bottom: 2px solid #00a3cc;
    }

    .input-line:focus,
    .select-line:focus {
        border-bottom: 2px solid #00a3cc;
    }

    .textarea-field {
        height: 225px;
        border: 1px solid #aaa;
        resize: vertical;
        padding: 8px;
    }

    .field-help {
        color: #777;
        margin-top: 6px;
    }

    .small-btn {
        background: #51bad8;
        color: white;
        border: none;
        padding: 9px 13px;
        border-radius: 3px;
        cursor: pointer;
        margin-right: 10px;
    }

    .system-actions {
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

    .hidden {
        display: none;
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
    Aqui puede configurar los aspectos basicos de su dispositivo, como el nombre del host o la zona horaria.
</p>

<section class="system-panel">
    <div class="system-tabs">
        <button class="system-tab active" type="button" onclick="cambiarTab('general', this)">Configuracion general</button>
        <button class="system-tab" type="button" onclick="cambiarTab('login', this)">Inicio de sesion</button>
        <button class="system-tab" type="button" onclick="cambiarTab('ntp', this)">Sincronizacion horaria</button>
        <button class="system-tab" type="button" onclick="cambiarTab('idioma', this)">Idioma y Estilo</button>
    </div>

    <!-- CONFIGURACION GENERAL -->
    <div id="tab-general" class="system-tab-content">
        <div class="form-row">
            <label class="form-label">Hora local</label>
            <div class="form-field">
                <span id="localTime">Cargando...</span>
            </div>
        </div>

        <div class="form-row">
            <label class="form-label"></label>
            <div class="form-field wide">
                <button class="small-btn" type="button" onclick="sincronizarNavegador()">SINCRONIZAR CON EL NAVEGADOR</button>
                <button class="small-btn" type="button" onclick="sincronizarNtp()">SINCRONIZAR CON EL SERVIDOR NTP</button>
            </div>
        </div>

        <div class="form-row">
            <label class="form-label" for="hostname">Nombre de host</label>
            <div class="form-field">
                <input class="input-line" type="text" id="hostname" name="hostname">
            </div>
        </div>

        <div class="form-row">
            <label class="form-label" for="description">Descripcion</label>
            <div class="form-field">
                <input class="input-line" type="text" id="description" name="description">
                <div class="field-help">Una breve descripcion opcional de este dispositivo</div>
            </div>
        </div>

        <div class="form-row">
            <label class="form-label" for="notes">Notas</label>
            <div class="form-field">
                <textarea class="textarea-field" id="notes" name="notes"></textarea>
                <div class="field-help">Notas opcionales de forma libre sobre este dispositivo</div>
            </div>
        </div>

        <div class="form-row">
            <label class="form-label" for="zonename">Zona horaria</label>
            <div class="form-field">
                <select class="select-line" id="zonename" name="zonename"></select>
            </div>
        </div>
    </div>

    <!-- INICIO DE SESION -->
    <div id="tab-login" class="system-tab-content hidden">
        <div class="form-row">
            <label class="form-label" for="log_size">Tamano del buffer de registro del sistema</label>
            <div class="form-field">
                <input class="input-line" type="text" id="log_size" name="log_size">
            </div>
        </div>

        <div class="form-row">
            <label class="form-label" for="log_ip">Servidor externo de registro del sistema</label>
            <div class="form-field">
                <input class="input-line" type="text" id="log_ip" name="log_ip">
            </div>
        </div>

        <div class="form-row">
            <label class="form-label" for="log_port">Puerto del servidor externo de registro del sistema</label>
            <div class="form-field">
                <input class="input-line" type="text" id="log_port" name="log_port">
            </div>
        </div>

        <div class="form-row">
            <label class="form-label" for="log_proto">Protocolo de servidor de registro externo</label>
            <div class="form-field">
                <select class="select-line" id="log_proto" name="log_proto">
                    <option value="udp">UDP</option>
                    <option value="tcp">TCP</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <label class="form-label" for="log_file">Escribe el registro del sistema al archivo</label>
            <div class="form-field">
                <input class="input-line" type="text" id="log_file" name="log_file">
            </div>
        </div>

        <div class="form-row">
            <label class="form-label" for="log_level">Nivel de registro</label>
            <div class="form-field">
                <select class="select-line" id="log_level" name="log_level">
                    <option>Depurar</option>
                    <option>Info</option>
                    <option>Aviso</option>
                    <option>Advertencia</option>
                    <option>Error</option>
                    <option>Critico</option>
                    <option>Alerta</option>
                    <option>Emergencia</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <label class="form-label" for="cron_level">Nivel de registro del cron</label>
            <div class="form-field">
                <select class="select-line" id="cron_level" name="cron_level">
                    <option>Depurar</option>
                    <option>Normal</option>
                    <option>Advertencia</option>
                </select>
            </div>
        </div>
    </div>

    <!-- SINCRONIZACION HORARIA -->
    <div id="tab-ntp" class="system-tab-content hidden">
        <div class="form-row">
            <label class="form-label">Activar cliente NTP</label>
            <div class="form-field">
                <select class="select-line" id="ntp_enabled">
                    <option value="1">Activado</option>
                    <option value="0">Desactivado</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <label class="form-label">Servidor NTP candidato</label>
            <div class="form-field">
                <input class="input-line" id="ntp_server_1" value="0.openwrt.pool.ntp.org">
            </div>
        </div>

        <div class="form-row">
            <label class="form-label">Servidor NTP candidato</label>
            <div class="form-field">
                <input class="input-line" id="ntp_server_2" value="1.openwrt.pool.ntp.org">
            </div>
        </div>

        <div class="form-row">
            <label class="form-label">Servidor NTP candidato</label>
            <div class="form-field">
                <input class="input-line" id="ntp_server_3" value="2.openwrt.pool.ntp.org">
            </div>
        </div>

        <div class="form-row">
            <label class="form-label">Servidor NTP candidato</label>
            <div class="form-field">
                <input class="input-line" id="ntp_server_4" value="3.openwrt.pool.ntp.org">
            </div>
        </div>
    </div>

    <!-- IDIOMA Y ESTILO -->
    <div id="tab-idioma" class="system-tab-content hidden">
        <div class="form-row">
            <label class="form-label">Idioma</label>
            <div class="form-field">
                <select class="select-line" id="language">
                    <option value="es">Español</option>
                    <option value="en">English</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <label class="form-label">Diseño</label>
            <div class="form-field">
                <select class="select-line" id="theme">
                    <option value="material">Material</option>
                    <option value="bootstrap">Bootstrap</option>
                    <option value="openwrt">OpenWrt</option>
                </select>
            </div>
        </div>
    </div>

    <div id="statusBox" class="status-box"></div>
</section>

<div class="system-actions">
    <button class="btn-main" type="button" onclick="guardarCambios(true)">GUARDAR Y APLICAR ▾</button>
    <button class="btn-blue" type="button" onclick="guardarCambios(false)">GUARDAR</button>
    <button class="btn-red" type="button" onclick="cargarDatos()">RESTABLECER</button>
</div>

<div id="loadingMessage" class="loading-message">
    Esperando a que se aplique la configuracion...
    <span id="countdown">60s</span>
</div>

@endsection

@push('scripts')
<script>
    let editing = false;

    const TIMEZONES = [
        { name: 'UTC', value: 'UTC0' },

        { name: 'Africa/Abidjan', value: 'GMT0' },
        { name: 'Africa/Accra', value: 'GMT0' },
        { name: 'Africa/Addis_Ababa', value: 'EAT-3' },
        { name: 'Africa/Algiers', value: 'CET-1' },
        { name: 'Africa/Asmara', value: 'EAT-3' },
        { name: 'Africa/Bamako', value: 'GMT0' },
        { name: 'Africa/Bangui', value: 'WAT-1' },
        { name: 'Africa/Banjul', value: 'GMT0' },
        { name: 'Africa/Bissau', value: 'GMT0' },
        { name: 'Africa/Blantyre', value: 'CAT-2' },
        { name: 'Africa/Brazzaville', value: 'WAT-1' },
        { name: 'Africa/Bujumbura', value: 'CAT-2' },
        { name: 'Africa/Cairo', value: 'EET-2' },
        { name: 'Africa/Casablanca', value: '<+01>-1' },
        { name: 'Africa/Ceuta', value: 'CET-1CEST,M3.5.0,M10.5.0/3' },
        { name: 'Africa/Conakry', value: 'GMT0' },
        { name: 'Africa/Dakar', value: 'GMT0' },
        { name: 'Africa/Dar_es_Salaam', value: 'EAT-3' },
        { name: 'Africa/Djibouti', value: 'EAT-3' },
        { name: 'Africa/Douala', value: 'WAT-1' },
        { name: 'Africa/El_Aaiun', value: '<+01>-1' },
        { name: 'Africa/Freetown', value: 'GMT0' },
        { name: 'Africa/Gaborone', value: 'CAT-2' },
        { name: 'Africa/Harare', value: 'CAT-2' },
        { name: 'Africa/Johannesburg', value: 'SAST-2' },
        { name: 'Africa/Juba', value: 'CAT-2' },

        { name: 'America/Mexico_City', value: 'CST6' },
        { name: 'America/Cancun', value: 'EST5' },
        { name: 'America/Mazatlan', value: 'MST7' },
        { name: 'America/Tijuana', value: 'PST8PDT,M3.2.0,M11.1.0' },
        { name: 'America/New_York', value: 'EST5EDT,M3.2.0,M11.1.0' },
        { name: 'America/Chicago', value: 'CST6CDT,M3.2.0,M11.1.0' },
        { name: 'America/Denver', value: 'MST7MDT,M3.2.0,M11.1.0' },
        { name: 'America/Los_Angeles', value: 'PST8PDT,M3.2.0,M11.1.0' },
        { name: 'America/Bogota', value: '<-05>5' },
        { name: 'America/Lima', value: '<-05>5' },
        { name: 'America/Caracas', value: '<-04>4' },
        { name: 'America/Santiago', value: '<-04>4<-03>,M9.1.6/24,M4.1.6/24' },
        { name: 'America/Argentina/Buenos_Aires', value: '<-03>3' },

        { name: 'Europe/London', value: 'GMT0BST,M3.5.0/1,M10.5.0' },
        { name: 'Europe/Madrid', value: 'CET-1CEST,M3.5.0,M10.5.0/3' },
        { name: 'Europe/Paris', value: 'CET-1CEST,M3.5.0,M10.5.0/3' },
        { name: 'Europe/Berlin', value: 'CET-1CEST,M3.5.0,M10.5.0/3' },
        { name: 'Europe/Rome', value: 'CET-1CEST,M3.5.0,M10.5.0/3' },

        { name: 'Asia/Tokyo', value: 'JST-9' },
        { name: 'Asia/Shanghai', value: 'CST-8' },
        { name: 'Asia/Seoul', value: 'KST-9' },
        { name: 'Asia/Dubai', value: '<+04>-4' },

        { name: 'Australia/Sydney', value: 'AEST-10AEDT,M10.1.0,M4.1.0/3' },
        { name: 'Pacific/Auckland', value: 'NZST-12NZDT,M9.5.0,M4.1.0/3' }
    ];

    document.addEventListener('DOMContentLoaded', () => {
        llenarZonas();
        activarProteccionEdicion();
        cargarDatos();
        setInterval(cargarDatos, 15000);
    });

    function activarProteccionEdicion() {
        document.querySelectorAll("input, select, textarea").forEach(el => {
            el.addEventListener("focus", () => editing = true);
            el.addEventListener("blur", () => editing = false);
        });
    }

    function cambiarTab(tab, button) {
        document.querySelectorAll('.system-tab-content').forEach(item => item.classList.add('hidden'));
        document.querySelectorAll('.system-tab').forEach(item => item.classList.remove('active'));

        document.getElementById('tab-' + tab).classList.remove('hidden');
        button.classList.add('active');
    }

    function llenarZonas() {
        const select = document.getElementById('zonename');
        select.innerHTML = '';

        TIMEZONES.forEach(zone => {
            const option = document.createElement('option');
            option.value = zone.name;
            option.dataset.timezone = zone.value;
            option.textContent = zone.name;
            select.appendChild(option);
        });
    }

    function obtenerTimezoneSeleccionado() {
        const select = document.getElementById('zonename');
        const option = select.options[select.selectedIndex];

        return option ? option.dataset.timezone : 'UTC0';
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
        if (editing) return;

        try {
            const response = await fetch('/sistema/data', {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('No se pudieron cargar los datos del router.');
            }

            const data = await response.json();

            document.getElementById('hostname').value = data.hostname || '';
            document.getElementById('localTime').textContent = data.time || 'Sin datos';

            document.getElementById('description').value = data.description || '';
            document.getElementById('notes').value = data.notes || '';

            document.getElementById('log_size').value = data.log_size || '';
            document.getElementById('log_ip').value = data.log_ip || '';
            document.getElementById('log_port').value = data.log_port || '';
            document.getElementById('log_proto').value = data.log_proto || 'udp';
            document.getElementById('log_file').value = data.log_file || '';

            document.getElementById('log_level').value = data.log_level || 'Depurar';
            document.getElementById('cron_level').value = data.cron_level || 'Depurar';

            const zona = document.getElementById('zonename');

            if (data.zonename) {
                const existe = Array.from(zona.options).some(option => option.value === data.zonename);

                if (!existe) {
                    const option = document.createElement('option');
                    option.value = data.zonename;
                    option.dataset.timezone = data.timezone || 'UTC0';
                    option.textContent = data.zonename;
                    zona.insertBefore(option, zona.firstChild);
                }

                zona.value = data.zonename;
            } else {
                zona.value = 'UTC';
            }

        } catch (error) {
            mostrarMensaje('error', error.message);
        }
    }

    async function guardarCambios(aplicar) {
        try {
            mostrarLoading();

            const datos = {
                hostname: document.getElementById('hostname').value,
                description: document.getElementById('description').value,
                notes: document.getElementById('notes').value,
                zonename: document.getElementById('zonename').value,
                timezone: obtenerTimezoneSeleccionado(),

                log_size: document.getElementById('log_size').value,
                log_ip: document.getElementById('log_ip').value,
                log_port: document.getElementById('log_port').value,
                log_proto: document.getElementById('log_proto').value,
                log_file: document.getElementById('log_file').value,
                log_level: document.getElementById('log_level').value,
                cron_level: document.getElementById('cron_level').value,

                apply: aplicar ? 1 : 0
            };

            const response = await fetch('/sistema/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(datos)
            });

            const result = await response.json();

            ocultarLoading();

            if (!response.ok || !result.success) {
                throw new Error(result.error || 'No se pudo guardar la configuracion.');
            }

            mostrarMensaje('ok', result.message || 'Configuracion guardada correctamente.');
            editing = false;
            await cargarDatos();

        } catch (error) {
            ocultarLoading();
            mostrarMensaje('error', error.message);
        }
    }

    function mostrarLoading() {
        const loading = document.getElementById('loadingMessage');
        const countdownElement = document.getElementById('countdown');
        let countdown = 60;

        loading.style.display = 'block';
        countdownElement.textContent = countdown + 's';

        if (window.loadingInterval) {
            clearInterval(window.loadingInterval);
        }

        window.loadingInterval = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown + 's';

            if (countdown <= 0) {
                clearInterval(window.loadingInterval);
            }
        }, 1000);
    }

    function ocultarLoading() {
        document.getElementById('loadingMessage').style.display = 'none';

        if (window.loadingInterval) {
            clearInterval(window.loadingInterval);
        }
    }

    function sincronizarNavegador() {
        const now = new Date();
        document.getElementById('localTime').textContent = now.toLocaleString();
        mostrarMensaje('ok', 'Hora sincronizada visualmente con el navegador.');
    }

    function sincronizarNtp() {
        mostrarMensaje('ok', 'Solicitud de sincronizacion NTP preparada.');
    }
</script>
@endpush
