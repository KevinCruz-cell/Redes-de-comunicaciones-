@extends('layouts.app')

@section('title', 'Nombres de host')
@section('header_title', 'Nombres de host')

@section('content')
    <div class="warning-banner" style="background-color: #fcf3ad; border: 1px solid #eddb3c; padding: 15px; margin-bottom: 20px; font-size: 13px;">
        <strong>¡Sin contraseña!</strong>
        <p style="margin: 5px 0 0 0;">No hay ninguna contraseña establecida en este enrutador. Configure una contraseña de root para proteger la interfaz web.</p>
    </div>

    <section class="panel" style="background: #fff; border: 1px solid #ccc; border-radius: 3px; padding: 20px;">
        <h2 style="font-size: 18px; font-weight: normal; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
            Entradas de host
        </h2>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 13px;">
            <thead>
            <tr style="background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                <th style="padding: 10px; text-align: center; width: 50%;">Nombre de host</th>
                <th style="padding: 10px; text-align: center; width: 50%;">Dirección IP</th>
            </tr>
            </thead>
            <tbody>
            <tr style="border-bottom: 1px solid #eee;">
                <td colspan="2" style="padding: 20px; text-align: center; color: #777; font-style: italic;">
                    Esta sección aún no contiene valores
                </td>
            </tr>
            </tbody>
        </table>

        <div class="actions-left">
            <button type="button" style="background-color: #337ab7; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                AÑADIR
            </button>
        </div>
    </section>

    <div style="margin-top: 30px; text-align: right;">
        <div class="footer-actions" style="margin-bottom: 15px;">
            <button class="btn-cyan" style="background: #5bc0de; color: white; border: none; padding: 8px 15px; border-radius: 3px; cursor: pointer;">GUARDAR Y APLICAR ▾</button>
            <button class="btn-blue" style="background: #337ab7; color: white; border: none; padding: 8px 15px; border-radius: 3px; cursor: pointer;">GUARDAR</button>
            <button class="btn-red" style="background: #d9534f; color: white; border: none; padding: 8px 15px; border-radius: 3px; cursor: pointer;">RESTABLECER</button>
        </div>
        <p style="font-size: 10px; color: #aaa;">
            Powered by LuCI openwrt-19.07 branch (git-22.045.73925-36e5c1c) / OpenWrt 19.07.9 r11405-2a3558b0de
        </p>
    </div>
@endsection
