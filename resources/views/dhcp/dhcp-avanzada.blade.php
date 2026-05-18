@extends('layouts.app')

@section('title', 'DHCP y DNS - Avanzada')
@section('header_title', 'DHCP y DNS')

@section('content')
    <div class="luci-warning-box">
        <strong>¡Sin contraseña!</strong>
        <p>No hay ninguna contraseña establecida en este enrutador. Configure una contraseña de root para proteger la interfaz web.</p>
    </div>

    <p class="luci-desc">
        Dnsmasq es un programa que combina un servidor <a href="#">DHCP</a> y un reenviador <a href="#">DNS</a> para cortafuegos <a href="#">NAT</a>
    </p>

    <section class="luci-panel">
        <div class="luci-tabs-header">
            <a href="/dhcp" class="luci-tab {{ request()->is('dhcp') ? 'active' : '' }}">Configuración general</a>
            <a href="/dhcp/resolv" class="luci-tab {{ request()->is('dhcp/resolv') ? 'active' : '' }}">Archivos Resolv y Hosts</a>
            <a href="/dhcp/tftp" class="luci-tab {{ request()->is('dhcp/tftp') ? 'active' : '' }}">Configuración TFTP</a>
            <a href="/dhcp/avanzada" class="luci-tab {{ request()->is('dhcp/avanzada') ? 'active' : '' }}">Configuración avanzada</a>
            <a href="/dhcp/estaticas" class="luci-tab {{ request()->is('dhcp/estaticas') ? 'active' : '' }}">Asignaciones estáticas</a>        </div>

        <form action="#" method="POST" class="luci-form-area">
            @csrf

            <div class="luci-form-row">
                <label class="luci-label">Filtrar peticiones</label>
                <div class="luci-field-group">
                    <input type="checkbox" name="suprimir_registro" class="luci-checkbox"> <span style="font-size: 13px;">Suprime el registro</span>
                    <div class="luci-help">Suprime el registro de peticiones DNS comunes y aburridas para el funcionamiento normal.</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Asignar IP secuencialmente</label>
                <div class="luci-field-group">
                    <input type="checkbox" name="ip_secuencial" class="luci-checkbox">
                    <div class="luci-help">Las direcciones IP se proporcionan secuencialmente, comenzando desde la dirección más baja disponible.</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Filtro de archivos</label>
                <div class="luci-field-group">
                    <input type="checkbox" name="filtro_privado" checked class="luci-checkbox"> <span style="font-size: 13px;">Filtro privado</span>
                    <div class="luci-help">No reenviar consultas inversas para redes locales privadas.</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Filtro local</label>
                <div class="luci-field-group">
                    <input type="checkbox" name="filtro_local" checked class="luci-checkbox">
                    <div class="luci-help">No reenviar peticiones que no se pueden responder por servidores de nombres públicos.</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Localizar consultas</label>
                <div class="luci-field-group">
                    <input type="checkbox" name="localizar_consultas" class="luci-checkbox">
                    <div class="luci-help">Localizar consultas enviadas a servidores de nombres de dominios que solo están disponibles localmente.</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Expandir hosts</label>
                <div class="luci-field-group">
                    <input type="checkbox" name="expandir_hosts" checked class="luci-checkbox">
                    <div class="luci-help">Añadir el sufijo de dominio local a los nombres simples leídos desde el archivo de hosts.</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Consultas de registro</label>
                <div class="luci-field-group">
                    <input type="checkbox" name="consultas_registro" class="luci-checkbox">
                    <div class="luci-help">No guardar respuestas negativas para evitar mensajes innecesarios.</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Archivo de servidores adicionales</label>
                <div class="luci-field-group">
                    <input type="text" name="archivo_adicional" class="luci-input-text" placeholder="ej. /etc/dnsmasq.d/local.conf">
                    <div class="luci-help">Usar un archivo de configuración adicional para servidores y opciones adicionales.</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Doble entrada</label>
                <div class="luci-field-group">
                    <input type="checkbox" name="doble_entrada" checked class="luci-checkbox">
                    <div class="luci-help">No permitir que un host local use el nombre de un host que ya aparece en el archivo hosts.</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Todos los servidores</label>
                <div class="luci-field-group">
                    <input type="checkbox" name="todos_servidores" class="luci-checkbox">
                    <div class="luci-help">Consultar todos los servidores DNS al mismo tiempo para cada consulta.</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Ignorar dominio de la interfaz</label>
                <div class="luci-field-group">
                    <input type="checkbox" name="ignorar_dominio_interfaz" class="luci-checkbox">
                    <div class="luci-help">Lista de dispositivos que proporcionan servidores de dominio RFC1918.</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Puerto del servidor DNS</label>
                <div class="luci-field-group">
                    <input type="text" name="puerto_dns" value="53" class="luci-input-text" style="width: 100px;">
                    <div class="luci-help">Puerto en el que Dnsmasq escucha por peticiones.</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Puerto de consultas de DNS</label>
                <div class="luci-field-group">
                    <input type="text" name="puerto_consultas" class="luci-input-text" style="width: 100px;">
                    <div class="luci-help">Puerto desde el que se enviarán las peticiones de DNS.</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Máximo de arrendamientos DHCP</label>
                <div class="luci-field-group">
                    <input type="number" name="max_dhcp" value="150" class="luci-input-text" style="width: 100px;">
                    <div class="luci-help">Número máximo permitido de asignaciones DHCP activas.</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Máximo de consultas DNS en curso</label>
                <div class="luci-field-group">
                    <input type="number" name="max_dns_queries" value="150" class="luci-input-text" style="width: 100px;">
                    <div class="luci-help">Número máximo de consultas DNS simultáneas.</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Log consultas simultáneas</label>
                <div class="luci-field-group">
                    <input type="checkbox" name="log_queries" class="luci-checkbox">
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Tamaño de la caché de consultas DNS</label>
                <div class="luci-field-group">
                    <input type="number" name="cache_size" value="150" class="luci-input-text" style="width: 100px;">
                    <div class="luci-help">Número de entradas DNS guardadas (el valor predeterminado es 150).</div>
                </div>
            </div>

            <div class="luci-footer-actions">
                <button type="submit" name="action" value="apply" class="btn-luci btn-cyan">GUARDAR Y APLICAR ▾</button>
                <button type="submit" name="action" value="save" class="btn-luci btn-blue">GUARDAR</button>
                <button type="reset" class="btn-luci btn-red">RESTABLECER</button>
            </div>
        </form>
    </section>

    <div class="luci-footer-note">
        Powered by LuCI openwrt-19.07 branch / OpenWrt 19.07.9
    </div>
@endsection
