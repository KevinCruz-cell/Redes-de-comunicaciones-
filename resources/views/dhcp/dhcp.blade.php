@extends('layouts.app')

@section('title', 'DHCP y DNS')
@section('header_title', 'DHCP y DNS')

@section('content')
    <div class="warning" style="background-color: #fcf3ad; border: 1px solid #eddb3c; padding: 15px; margin-bottom: 20px;">
        <strong>¡Sin contraseña!</strong>
        <p>No hay ninguna contraseña establecida en este enrutador. Configure una contraseña de root para proteger la interfaz web.</p>
    </div>

    <p class="desc">
        Dnsmasq es un programa que combina un servidor <a href="#">DHCP</a> y un reenviador <a href="#">DNS</a> para cortafuegos <a href="#">NAT</a>
    </p>

    <section class="panel">
        <div class="tabs" style="display: flex; background: #ddd; border-bottom: 1px solid #ccc;">
            <div class="tab active" style="padding: 10px 20px; background: #fff; border: 1px solid #ccc; border-bottom: none;">Configuración general</div>
            <div class="tab" style="padding: 10px 20px;">Archivos Resolv y Hosts</div>
            <div class="tab" style="padding: 10px 20px;">Configuración TFTP</div>
            <div class="tab" style="padding: 10px 20px;">Configuración avanzada</div>
            <div class="tab" style="padding: 10px 20px;">Asignaciones estáticas</div>
        </div>

        <div class="form-area" style="padding-top: 20px;">
            <div class="form-row" style="display: flex; margin-bottom: 15px;">
                <label style="width: 250px; text-align: right; padding-right: 20px;">Requerir dominio</label>
                <div class="field-group">
                    <input type="checkbox" checked>
                    <div class="help" style="font-size: 11px; color: #777;">No reenviar peticiones de DNS sin un nombre de DNS</div>
                </div>
            </div>

            <div class="form-row" style="display: flex; margin-bottom: 15px;">
                <label style="width: 250px; text-align: right; padding-right: 20px;">Servidor local</label>
                <div class="field-group">
                    <input type="text" value="/lan/" style="width: 300px;">
                    <div class="help" style="font-size: 11px; color: #777;">Especificación de dominio local...</div>
                </div>
            </div>

            <div class="footer-actions" style="margin-top: 40px; text-align: right; border-top: 1px solid #ccc; padding-top: 20px;">
                <button style="background: #5bc0de; color: white; border: none; padding: 10px;">GUARDAR Y APLICAR ▾</button>
                <button style="background: #337ab7; color: white; border: none; padding: 10px;">GUARDAR</button>
                <button style="background: #d9534f; color: white; border: none; padding: 10px;">RESTABLECER</button>
            </div>
        </div>
    </section>
@endsection
