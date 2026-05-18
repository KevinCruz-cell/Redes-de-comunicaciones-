@extends('layouts.app')

@section('title', 'DHCP y DNS - General')
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
                <label class="luci-label">Requerir dominio</label>
                <div class="luci-field-group">
                    <input type="checkbox" name="requerir_dominio" checked class="luci-checkbox">
                    <div class="luci-help">No reenviar peticiones de DNS sin un nombre de DNS</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Autorizar</label>
                <div class="luci-field-group">
                    <input type="checkbox" name="autorizar" checked class="luci-checkbox">
                    <div class="luci-help">Este es el único servidor DHCP en la red de área local</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Servidor local</label>
                <div class="luci-field-group">
                    <input type="text" name="servidor_local" value="/lan/" class="luci-input-text">
                    <div class="luci-help">Especificación de dominio local. Los nombres que coinciden con este dominio nunca se reenvían...</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Dominio local</label>
                <div class="luci-field-group">
                    <input type="text" name="dominio_local" value="lan" class="luci-input-text">
                    <div class="luci-help">Sufijo del dominio local que se añade a los nombres DHCP...</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Registrar consultas</label>
                <div class="luci-field-group">
                    <input type="checkbox" name="registrar_consultas" class="luci-checkbox">
                    <div class="luci-help">Escribe las peticiones de DNS recibidas en el registro del sistema</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Reenvíos de DNS</label>
                <div class="luci-field-group">
                    <div class="luci-input-group-dynamic">
                        <input type="text" name="reenvios_dns[]" placeholder="/example.org/10.1.2.3" class="luci-input-text">
                        <button type="button" class="btn-luci-add">+</button>
                    </div>
                    <div class="luci-help">Lista de servidores DNS a los que enviar solicitudes</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Protección contra reasociación</label>
                <div class="luci-field-group">
                    <input type="checkbox" name="proteccion_reasociacion" checked class="luci-checkbox">
                    <div class="luci-help">Descartar respuestas RFC1918 ascendentes</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Permitir host local</label>
                <div class="luci-field-group">
                    <input type="checkbox" name="permitir_host_local" checked class="luci-checkbox">
                    <div class="luci-help">Permitir respuestas aguas arriba en el rango 127.0.0.0/8</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Lista blanca de dominios</label>
                <div class="luci-field-group">
                    <div class="luci-input-group-dynamic">
                        <input type="text" name="lista_blanca[]" value="ihost.netflix.com" class="luci-input-text">
                        <button type="button" class="btn-luci-add">+</button>
                    </div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Solo servicio local</label>
                <div class="luci-field-group">
                    <input type="checkbox" name="solo_servicio_local" checked class="luci-checkbox">
                    <div class="luci-help">Limita el servicio de DNS a las subredes de interfaces...</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Sin comodín</label>
                <div class="luci-field-group">
                    <input type="checkbox" name="sin_comodin" checked class="luci-checkbox">
                    <div class="luci-help">Enlace dinámico a las interfaces en lugar de la dirección del comodín</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Interfaces de escucha</label>
                <div class="luci-field-group">
                    <div class="luci-input-group-dynamic">
                        <input type="text" name="interfaces_escucha[]" class="luci-input-text" placeholder="ej. eth0">
                        <button type="button" class="btn-luci-add">+</button>
                    </div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Excluir interfaces</label>
                <div class="luci-field-group">
                    <div class="luci-input-group-dynamic">
                        <input type="text" name="excluir_interfaces[]" class="luci-input-text" placeholder="ej. br-lan">
                        <button type="button" class="btn-luci-add">+</button>
                    </div>
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
