@extends('layouts.app')

@section('title', 'DHCP y DNS - Asignaciones Estáticas')
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
            <a href="/dhcp/estaticas" class="luci-tab {{ request()->is('dhcp/estaticas') ? 'active' : '' }}">Asignaciones estáticas</a>
        </div>

        <div class="luci-form-area" style="padding: 15px 0 0 0;">
            <p style="font-size: 11px; color: #666; padding: 0 15px; line-height: 1.5;">
                Las asignaciones estáticas se usan para asignar direcciones IP fijas y nombres identificativos de dispositivos a clientes DHCP. También son necesarias para configuraciones de interfaces no dinámicas en las que a cada dispositivo siempre se le quiere dar la misma dirección IP.<br>
                Utilice el botón <em>Añadir</em> para agregar una nueva entrada de asignación. La <em>Dirección MAC</em> identifica el host, la <em>Dirección IPv4</em> especifica la dirección fija a utilizar y <em>Nombre de host</em> se asigna como nombre simbólico a el anfitrión solicitante. El <em>Tiempo de asignación</em> opcional se puede utilizar para establecer un tiempo de asignación específico de host no estándar, p. Ej. 12h, 3d o infinite (infinito).
            </p>

            <div class="luci-table-wrapper" style="margin-top: 15px;">
                <table class="luci-table">
                    <thead>
                    <tr>
                        <th>Nombre de host</th>
                        <th>Dirección <abbr title="Media Access Control">MAC</abbr></th>
                        <th>Dirección <abbr title="Internet Protocol Version 4">IPv4</abbr></th>
                        <th>Tiempo de asignación</th>
                        <th>DUID</th>
                        <th>Sufijo (hex)<abbr title="Internet Protocol Version 6">IPv6</abbr></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="6" class="luci-table-empty">Esta sección aún no contiene valores</td>
                    </tr>
                    </tbody>
                </table>
                <div style="margin-top: 10px;">
                    <button type="button" class="btn-luci-small">AÑADIR</button>
                </div>
            </div>

            <hr style="border: 0; border-top: 1px solid #ccc; margin: 20px 0;">

            <h3 class="luci-section-title">Asignaciones DHCP activas</h3>
            <div class="luci-table-wrapper">
                <table class="luci-table">
                    <thead>
                    <tr>
                        <th>Nombre de host</th>
                        <th>Dirección IPv4</th>
                        <th>Dirección MAC</th>
                        <th>Tiempo de asignación restante</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><strong>Misa3lo</strong></td>
                        <td>192.168.10.104</td>
                        <td>D8:5E:D3:E9:26:C6</td>
                        <td>11h 45m 33s</td>
                    </tr>
                    <tr>
                        <td>?</td>
                        <td>192.168.10.172</td>
                        <td>22:0C:2A:21:DE:93</td>
                        <td>11h 52m 28s</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="luci-footer-actions">
                <button type="submit" name="action" value="apply" class="btn-luci btn-cyan">GUARDAR Y APLICAR ▾</button>
                <button type="submit" name="action" value="save" class="btn-luci btn-blue">GUARDAR</button>
                <button type="reset" class="btn-luci btn-red">RESTABLECER</button>
            </div>
        </div>
    </section>

    <div class="luci-footer-note">
        Powered by LuCI openwrt-19.07 branch / OpenWrt 19.07.9
    </div>
@endsection
