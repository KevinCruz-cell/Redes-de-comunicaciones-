@extends('layouts.app')

@section('title', 'DHCP y DNS - Archivos Resolv')
@section('header_title', 'DHCP y DNS')

@section('content')
    <p class="desc">
        Dnsmasq es un programa que combina un servidor <a href="#">DHCP</a> y un reenviador <a href="#">DNS</a> para cortafuegos <a href="#">NAT</a>
    </p>

    <section class="panel">
        <div class="tabs" style="display: flex; background: #ddd; border-bottom: 1px solid #ccc;">
            <div class="tab" style="padding: 10px 20px;">Configuración general</div>
            <div class="tab active" style="padding: 10px 20px; background: #fff; border: 1px solid #ccc; border-bottom: none; font-weight: bold;">Archivos Resolv y Hosts</div>
            <div class="tab" style="padding: 10px 20px;">Configuración TFTP</div>
            <div class="tab" style="padding: 10px 20px;">Configuración avanzada</div>
            <div class="tab" style="padding: 10px 20px;">Asignaciones estáticas</div>
        </div>

        <div class="form-area" style="padding-top: 20px;">

            <div class="form-row" style="display: flex; margin-bottom: 15px;">
                <label style="width: 250px; text-align: right; padding-right: 20px;">Usar <code style="background:#eee; padding:2px;">/etc/ethers</code></label>
                <div class="field-group">
                    <input type="checkbox" checked>
                    <div class="help" style="font-size: 11px; color: #777;">Leer <code style="background:#eee;">/etc/ethers</code> para configurar el servidor <a href="#">DHCP</a></div>
                </div>
            </div>

            <div class="form-row" style="display: flex; margin-bottom: 15px;">
                <label style="width: 250px; text-align: right; padding-right: 20px;">Archivo de asignación</label>
                <div class="field-group">
                    <input type="text" value="/tmp/dhcp.leases" style="width: 300px;">
                    <div class="help" style="font-size: 11px; color: #777;">archivo en donde se almacenará las asignaciones <a href="#">DHCP</a></div>
                </div>
            </div>

            <div class="form-row" style="display: flex; margin-bottom: 15px;">
                <label style="width: 250px; text-align: right; padding-right: 20px;">Ignorar el archivo resolve</label>
                <div class="field-group">
                    <input type="checkbox">
                </div>
            </div>

            <div class="form-row" style="display: flex; margin-bottom: 15px;">
                <label style="width: 250px; text-align: right; padding-right: 20px;">Archivo de resolución</label>
                <div class="field-group">
                    <input type="text" value="/tmp/resolv.conf.auto" style="width: 300px;">
                    <div class="help" style="font-size: 11px; color: #777;">Archivo <a href="#">DNS</a> local</div>
                </div>
            </div>

            <div class="form-row" style="display: flex; margin-bottom: 15px;">
                <label style="width: 250px; text-align: right; padding-right: 20px;">Ignorar <code style="background:#eee; padding:2px;">/etc/hosts</code></label>
                <div class="field-group">
                    <input type="checkbox">
                </div>
            </div>

            <div class="form-row" style="display: flex; margin-bottom: 15px;">
                <label style="width: 250px; text-align: right; padding-right: 20px;">Archivos de hosts adicionales</label>
                <div class="field-group">
                    <div class="input-group" style="display: flex; align-items: center; gap: 5px;">
                        <input type="text" value="" style="width: 300px;">
                        <button type="button" style="background: #337ab7; color: white; border: none; padding: 2px 8px; cursor: pointer;">+</button>
                    </div>
                </div>
            </div>

            <div class="footer-actions" style="margin-top: 40px; text-align: right; border-top: 1px solid #ccc; padding: 20px 0;">
                <button class="btn-cyan" style="background: #5bc0de; color: white; border: none; padding: 8px 15px; cursor: pointer;">GUARDAR Y APLICAR ▾</button>
                <button class="btn-blue" style="background: #337ab7; color: white; border: none; padding: 8px 15px; cursor: pointer;">GUARDAR</button>
                <button class="btn-red" style="background: #d9534f; color: white; border: none; padding: 8px 15px; cursor: pointer;">RESTABLECER</button>
            </div>
        </div>
    </section>

    <div class="footer-note" style="font-size: 10px; color: #aaa; text-align: right; margin-top: 10px;">
        Powered by LuCI openwrt-19.07 branch (git-22.045.73925-36e5c1c) / OpenWrt 19.07.9 r11405-2a3558b0de
    </div>
@endsection
