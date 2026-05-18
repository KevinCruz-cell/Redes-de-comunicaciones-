@extends('layouts.app')

@section('title', 'DHCP y DNS - Archivos Resolv')
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
                <label class="luci-label">Usar <code>/etc/ethers</code></label>
                <div class="luci-field-group">
                    <input type="checkbox" name="usar_ethers" checked class="luci-checkbox">
                    <div class="luci-help">Leer <code>/etc/ethers</code> para configurar el servidor <a href="#">DHCP</a></div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Archivo de asignación</label>
                <div class="luci-field-group">
                    <input type="text" name="archivo_asignacion" value="/tmp/dhcp.leases" class="luci-input-text">
                    <div class="luci-help">archivo en donde se almacenará las asignaciones <a href="#">DHCP</a></div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Ignorar el archivo resolve</label>
                <div class="luci-field-group">
                    <input type="checkbox" name="ignorar_resolv" class="luci-checkbox">
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Archivo de resolución</label>
                <div class="luci-field-group">
                    <input type="text" name="archivo_resolucion" value="/tmp/resolv.conf.auto" class="luci-input-text">
                    <div class="luci-help">Archivo <a href="#">DNS</a> local</div>
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Ignorar <code>/etc/hosts</code></label>
                <div class="luci-field-group">
                    <input type="checkbox" name="ignorar_hosts" class="luci-checkbox">
                </div>
            </div>

            <div class="luci-form-row">
                <label class="luci-label">Archivos de hosts adicionales</label>
                <div class="luci-field-group">
                    <div class="luci-input-group-dynamic">
                        <input type="text" name="hosts_adicionales[]" value="" class="luci-input-text">
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
