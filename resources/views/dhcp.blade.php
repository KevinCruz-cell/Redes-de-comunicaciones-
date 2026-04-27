<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>NuupNet - DHCP y DNS</title>

    @vite('resources/css/dhcp.css')
</head>
<body>
<header class="topbar">
    <div class="brand">
        <div class="logo">NUUP</div>
        <span>NuupNet</span>
        <span class="brand-secondary">NuupNet</span>
    </div>
    <button class="btn-refresh" onclick="location.reload()">Refrescar</button>
</header>

<div class="layout">
    <aside class="sidebar">
        <div class="menu-title">Estado <span>﹀</span></div>
        <div class="menu-title">Sistema <span>﹀</span></div>
        <div class="menu-title">Red <span>︿</span></div>

        <div class="submenu">
            <a href="#">Interfaces</a>
            <a href="#">Wi-Fi</a>
            <a href="#">Conmutador</a>
            <a href="/dhcp" class="active">DHCP y DNS</a>
            <a href="#">Nombres de host</a>
            <a href="#">Rutas estaticas</a>
            <a href="#">Diagnosticos</a>
        </div>

        <a class="logout" href="/login">Cerrar sesion</a>
    </aside>

    <main class="content">
        <div class="warning">
            <strong>¡Sin contraseña!</strong>
            <p>No hay ninguna contraseña establecida en este enrutador. Configure una contraseña de root para proteger la interfaz web.</p>
        </div>

        <h1>DHCP y DNS</h1>
        <p class="desc">
            Dnsmasq es un programa que combina un servidor
            <a href="#">DHCP</a> y un reenviador
            <a href="#">DNS</a> para cortafuegos
            <a href="#">NAT</a>
        </p>

        <section class="panel">
            <div class="tabs">
                <div class="tab active">Configuracion general</div>
                <div class="tab">Archivos Resolv y Hosts</div>
                <div class="tab">Configuracion TFTP</div>
                <div class="tab">Configuracion avanzada</div>
                <div class="tab">Asignaciones estaticas</div>
            </div>

            <div class="form-area">
                <div class="form-row">
                    <label>Requerir dominio</label>
                    <div class="field-group">
                        <div class="checkbox-wrap"><input type="checkbox" checked></div>
                        <div class="help">No reenviar peticiones de DNS sin un nombre de DNS</div>
                    </div>
                </div>

                <div class="form-row">
                    <label>Autorizar</label>
                    <div class="field-group">
                        <div class="checkbox-wrap"><input type="checkbox" checked></div>
                        <div class="help">Este es el unico servidor DHCP en la red de area local</div>
                    </div>
                </div>

                <div class="form-row">
                    <label>Servidor local</label>
                    <div class="field-group">
                        <input type="text" value="/lan/">
                        <div class="help">Especificacion de dominio local. Los nombres que coinciden con este dominio nunca se reenvian y se resuelven solo desde archivos DHCP o hosts.</div>
                    </div>
                </div>

                <div class="form-row">
                    <label>Dominio local</label>
                    <div class="field-group">
                        <input type="text" value="lan">
                        <div class="help">Sufijo del dominio local que se añade a los nombres DHCP y a las entradas del archivo de dispositivos.</div>
                    </div>
                </div>

                <div class="form-row">
                    <label>Registrar consultas</label>
                    <div class="field-group">
                        <div class="checkbox-wrap"><input type="checkbox"></div>
                        <div class="help">Escribe las peticiones de DNS recibidas en el registro del sistema.</div>
                    </div>
                </div>

                <div class="form-row">
                    <label>Reenvios de DNS</label>
                    <div class="field-group">
                        <div class="dns-inline">
                            <input type="text" value="/example.org/10.1.2.3">
                            <button class="plus-btn" type="button">+</button>
                        </div>
                        <div class="help">Lista de servidores DNS a los que enviar solicitudes.</div>
                    </div>
                </div>

                <div class="form-row">
                    <label>Proteccion contra reasociacion</label>
                    <div class="field-group">
                        <div class="checkbox-wrap"><input type="checkbox" checked></div>
                        <div class="help">Descartar respuestas RFC1918 ascendentes.</div>
                    </div>
                </div>

                <div class="form-row">
                    <label>Permitir host local</label>
                    <div class="field-group">
                        <div class="checkbox-wrap"><input type="checkbox" checked></div>
                        <div class="help">Permitir respuestas aguas arriba en el rango 127.0.0.0/8, por ejemplo para servicios RBL.</div>
                    </div>
                </div>

                <div class="form-row">
                    <label>Lista blanca de dominios</label>
                    <div class="field-group">
                        <div class="dns-inline">
                            <input type="text" value="ihost.netflix.com">
                            <button class="plus-btn" type="button">+</button>
                        </div>
                        <div class="help">Lista de dominios a los que se permiten respuestas RFC1918.</div>
                    </div>
                </div>

                <div class="form-row">
                    <label>Solo servicio local</label>
                    <div class="field-group">
                        <div class="checkbox-wrap"><input type="checkbox" checked></div>
                        <div class="help">Limita el servicio de DNS a las subredes de interfaces en las que estamos sirviendo DNS.</div>
                    </div>
                </div>

                <div class="form-row">
                    <label>Sin comodin</label>
                    <div class="field-group">
                        <div class="checkbox-wrap"><input type="checkbox" checked></div>
                        <div class="help">Enlace dinamico a las interfaces en lugar de la direccion del comodin.</div>
                    </div>
                </div>

                <div class="form-row">
                    <label>Interfaces de escucha</label>
                    <div class="field-group">
                        <div class="dns-inline">
                            <input type="text" class="field-long" value="">
                            <button class="plus-btn" type="button">+</button>
                        </div>
                        <div class="help">Limita la escucha de estas interfaces y el bucle de retorno.</div>
                    </div>
                </div>

                <div class="form-row">
                    <label>Excluir interfaces</label>
                    <div class="field-group">
                        <div class="dns-inline">
                            <input type="text" class="field-long" value="">
                            <button class="plus-btn" type="button">+</button>
                        </div>
                        <div class="help">Evita escuchar en estas interfaces.</div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<div class="powered">
    Powered by LuCI openwrt-19.07 branch (git-22.045.73925-36e5c1c) / OpenWrt 19.07.9 r11405-2a3558b0de
</div>

<div class="footer-actions">
    <button class="btn btn-primary">Guardar y aplicar ▾</button>
    <button class="btn btn-secondary">Guardar</button>
    <button class="btn btn-danger" onclick="window.location.href='/login'">Restablecer</button>
</div>
</body>
</html>
