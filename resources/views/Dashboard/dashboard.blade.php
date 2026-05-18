<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NuupNet Dashboard</title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial, Helvetica, sans-serif;
        }

        :root{
            --bg:#0f172a;
            --card:#1e293b;
            --border:#334155;
            --accent:#38bdf8;
            --text:#e2e8f0;
        }

        body{
            background:var(--bg);
            color:var(--text);
            overflow:hidden;
        }

        .navbar-top{
            height:60px;
            background:#1e293b;
            border-bottom:1px solid #334155;
            display:flex;
            align-items:center;
            justify-content:space-between;
            padding:0 20px;
        }

        .navbar-brand{
            display:flex;
            align-items:center;
            gap:10px;
        }

        .brand-link{
            display:flex;
            align-items:center;
            text-decoration:none;
            color:white;
            gap:10px;
        }

        .logo-small{
            width:40px;
            height:40px;
            object-fit:contain;
        }

        .brand-text{
            font-size:22px;
            font-weight:bold;
            color:#38bdf8;
        }

        .btn-refresh{
            background:#38bdf8;
            border:none;
            color:white;
            padding:10px 18px;
            border-radius:6px;
            cursor:pointer;
            font-weight:bold;
        }

        .btn-refresh:hover{
            opacity:0.9;
        }

        .container{
            display:flex;
            height:calc(100vh - 60px);
        }

        .sidebar{
            width:260px;
            background:#1e293b;
            border-right:1px solid #334155;
            overflow-y:auto;
        }

        .sidebar-nav{
            padding:15px 0;
        }

        .menu-group{
            margin-bottom:10px;
        }

        .group-toggle{
            width:100%;
            background:none;
            border:none;
            color:#fff;
            padding:14px 20px;
            text-align:left;
            cursor:pointer;
            font-size:14px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            transition:.3s;
        }

        .group-toggle:hover{
            background:#334155;
        }

        .submenu{
            list-style:none;
            display:none;
            background:#0f172a;
        }

        .menu-group.expanded .submenu{
            display:block;
        }

        .submenu li a{
            display:block;
            padding:12px 35px;
            text-decoration:none;
            color:#cbd5e1;
            border-left:4px solid transparent;
            transition:.3s;
            font-size:13px;
            cursor:pointer;
        }

        .submenu li a:hover{
            background:#334155;
            color:#38bdf8;
            border-left-color:#38bdf8;
        }

        .submenu li a.active{
            background:#334155;
            color:#38bdf8;
            border-left-color:#38bdf8;
        }

        .sidebar-footer{
            padding:20px;
            border-top:1px solid #334155;
        }

        .logout-btn{
            width:100%;
            padding:12px;
            border:none;
            background:#ef4444;
            color:white;
            border-radius:6px;
            cursor:pointer;
            font-weight:bold;
        }

        .logout-btn:hover{
            opacity:0.9;
        }

        .main-body{
            flex:1;
            overflow-y:auto;
            padding:20px;
        }

        .page-header{
            margin-bottom:20px;
        }

        .page-header h2{
            font-size:24px;
            border-left:5px solid #38bdf8;
            padding-left:12px;
        }

        .content-section{
            display:none;
        }

        .content-section.active{
            display:block;
        }

        .card-data{
            background:var(--card);
            border-radius:12px;
            border:1px solid var(--border);
            padding:20px;
            margin-bottom:20px;
        }

        .card-data h3{
            color:var(--accent);
            margin-bottom:15px;
            font-size:18px;
        }

        .data-content{
            background:var(--bg);
            border-radius:8px;
            padding:15px;
            font-family:'Courier New', monospace;
            font-size:12px;
            overflow-x:auto;
            white-space:pre-wrap;
            word-break:break-all;
            max-height:500px;
            overflow-y:auto;
            color:#10b981;
        }

        .stats-grid{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));
            gap:15px;
        }

        .stat-card{
            background:var(--bg);
            border-radius:8px;
            padding:12px;
            border:1px solid var(--border);
        }

        .stat-card strong{
            color:var(--accent);
            font-size:12px;
            display:block;
            margin-bottom:6px;
            text-transform:uppercase;
            letter-spacing:1px;
        }

        .stat-card span{
            font-size:14px;
            word-break:break-word;
        }

        .memory-bar{
            background:#334155;
            border-radius:4px;
            height:8px;
            margin-top:8px;
            overflow:hidden;
        }

        .memory-bar-fill{
            background:#38bdf8;
            height:100%;
            border-radius:4px;
            width:0%;
        }

        .realtime-stats{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));
            gap:15px;
            margin-bottom:20px;
        }

        .realtime-card{
            background:var(--bg);
            border-radius:8px;
            padding:15px;
            text-align:center;
            border:1px solid var(--border);
        }

        .realtime-card h4{
            color:var(--accent);
            font-size:12px;
            margin-bottom:10px;
        }

        .realtime-card .value{
            font-size:24px;
            font-weight:bold;
            color:white;
        }

        .realtime-card .unit{
            font-size:12px;
            color:#94a3b8;
        }

        .grid-realtime{
            display:grid;
            grid-template-columns:1fr;
            gap:25px;
        }

        .big-card{
            background:white;
            border-radius:10px;
            overflow:hidden;
            border:1px solid #334155;
            height:700px;
        }

        .big-card-header{
            background:#334155;
            color:#38bdf8;
            padding:14px 20px;
            font-size:12px;
            font-weight:bold;
            text-transform:uppercase;
        }

        .big-card iframe{
            width:100%;
            height:calc(100% - 45px);
            border:none;
        }

        .luci-version{
            margin-top:20px;
            color:#94a3b8;
            text-align:center;
            font-size:12px;
        }

        .system-stats{
            background:var(--card);
            border-radius:12px;
            border:1px solid var(--border);
            padding:20px;
            margin-bottom:20px;
        }

        .system-stats h3{
            color:var(--accent);
            margin-bottom:15px;
            font-size:18px;
        }

        iframe{
            width:100%;
            height:100%;
            border:none;
        }

        .iframe-card{
            background:white;
            border-radius:10px;
            overflow:hidden;
            border:1px solid #334155;
            height:calc(100vh - 170px);
        }
    </style>
</head>
<body>

<header class="navbar-top">
    <div class="navbar-brand">
        <a href="#" class="brand-link">
            <img src="{{ asset('nuupwisp.png') }}" alt="NuupNet" class="logo-small">
            <span class="brand-text">NuupNet</span>
        </a>
    </div>
    <div class="navbar-actions">
        <button class="btn-refresh" onclick="location.reload()">REFRESCAR</button>
    </div>
</header>

<div class="container">
    <aside class="sidebar">
        <nav class="sidebar-nav">
            <div class="menu-group expanded">
                <button class="group-toggle">
                    Estado
                    <span class="arrow">︿</span>
                </button>
                <ul class="submenu">
                    <li><a href="#" class="active" onclick="showSection('vision', this)">Visión general</a></li>
                    <li><a href="#" onclick="showSection('firewall', this)">Cortafuegos</a></li>
                    <li><a href="#" onclick="showSection('rutas', this)">Rutas</a></li>
                    <li><a href="#" onclick="showSection('syslog', this)">Registro del sistema</a></li>
                    <li><a href="#" onclick="showSection('kernel', this)">Registro del núcleo</a></li>
                    <li><a href="#" onclick="showSection('procesos', this)">Procesos</a></li>
                    <li><a href="#" onclick="showSection('realtime', this)">Gráficos en tiempo real</a></li>
                </ul>
            </div>
        </nav>
        <div class="sidebar-footer">
            <form action="{{ route('router.logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">➡️ Cerrar sesión</button>
            </form>
        </div>
    </aside>

    <main class="main-body">
        <div class="page-header">
            <h2 id="pageTitle">Visión general</h2>
        </div>

        <!-- VISIÓN GENERAL -->
        <section id="vision" class="content-section active">
            <div class="system-stats">
                <h3>📊 Estado del Sistema</h3>
                <div id="systemData" class="stats-grid">
                    <div class="stat-card">Cargando datos del router...</div>
                </div>
            </div>
            <div class="iframe-card">
                <iframe src="/cgi-bin/luci/admin/status/overview" onload="cleanIframe(this)"></iframe>
            </div>
        </section>

        <!-- CORTAFUEGOS -->
        <section id="firewall" class="content-section">
            <div class="card-data">
                <h3>🔥 Reglas de Cortafuegos (iptables)</h3>
                <div id="firewallData" class="data-content">Cargando reglas...</div>
            </div>
            <div class="iframe-card">
                <iframe src="/cgi-bin/luci/admin/status/iptables" onload="cleanIframe(this)"></iframe>
            </div>
        </section>

        <!-- RUTAS -->
        <section id="rutas" class="content-section">
            <div class="card-data">
                <h3>🛣️ Tabla de Rutas</h3>
                <div id="routesData" class="data-content">Cargando rutas...</div>
            </div>
            <div class="iframe-card">
                <iframe src="/cgi-bin/luci/admin/status/routes" onload="cleanIframe(this)"></iframe>
            </div>
        </section>

        <!-- REGISTRO DEL SISTEMA -->
        <section id="syslog" class="content-section">
            <div class="card-data">
                <h3>📝 Registro del Sistema (syslog)</h3>
                <div id="syslogData" class="data-content">Cargando logs...</div>
            </div>
            <div class="iframe-card">
                <iframe src="/cgi-bin/luci/admin/status/syslog" onload="cleanIframe(this)"></iframe>
            </div>
        </section>

        <!-- REGISTRO DEL NÚCLEO -->
        <section id="kernel" class="content-section">
            <div class="card-data">
                <h3>🐧 Registro del Núcleo (dmesg)</h3>
                <div id="kernelData" class="data-content">Cargando logs...</div>
            </div>
            <div class="iframe-card">
                <iframe src="/cgi-bin/luci/admin/status/dmesg" onload="cleanIframe(this)"></iframe>
            </div>
        </section>

        <!-- PROCESOS -->
        <section id="procesos" class="content-section">
            <div class="card-data">
                <h3>⚙️ Procesos del Sistema</h3>
                <div id="processesData" class="data-content">Cargando procesos...</div>
            </div>
            <div class="iframe-card">
                <iframe src="/cgi-bin/luci/admin/status/processes" onload="cleanIframe(this)"></iframe>
            </div>
        </section>

        <!-- GRÁFICOS EN TIEMPO REAL -->
        <section id="realtime" class="content-section">
            <div class="card-data">
                <h3>📈 Estadísticas en Tiempo Real</h3>
                <div id="realtimeStats" class="realtime-stats">
                    <div class="realtime-card">Cargando estadísticas...</div>
                </div>
            </div>
            <div class="grid-realtime">
                <div class="big-card">
                    <div class="big-card-header">Ancho de Banda</div>
                    <iframe src="/cgi-bin/luci/admin/status/realtime/bandwidth/wlan0" onload="cleanIframe(this)"></iframe>
                </div>
                <div class="big-card">
                    <div class="big-card-header">CPU / Sistema</div>
                    <iframe src="/cgi-bin/luci/admin/status/realtime/load" onload="cleanIframe(this)"></iframe>
                </div>
                <div class="big-card">
                    <div class="big-card-header">Rendimiento Wi-Fi</div>
                    <iframe src="/cgi-bin/luci/admin/status/realtime/wireless/radio0.network1" onload="cleanIframe(this)"></iframe>
                </div>
                <div class="big-card">
                    <div class="big-card-header">Conexiones Activas</div>
                    <iframe src="/cgi-bin/luci/admin/status/realtime/connections" onload="cleanIframe(this)"></iframe>
                </div>
            </div>
        </section>

        <footer class="luci-version">
            Powered by LuCI openwrt-19.07 branch / OpenWrt 19.07.9
        </footer>
    </main>
</div>

<script>
    document.querySelectorAll('.group-toggle').forEach(toggle => {
        toggle.addEventListener('click', function(){
            const parent = this.parentElement;
            parent.classList.toggle('expanded');
            const arrow = this.querySelector('.arrow');
            if(parent.classList.contains('expanded')){
                arrow.textContent = '︿';
            }else{
                arrow.textContent = '﹀';
            }
        });
    });

    function showSection(id, el){
        document.querySelectorAll('.content-section').forEach(section => {
            section.classList.remove('active');
        });
        document.querySelectorAll('.submenu a').forEach(link => {
            link.classList.remove('active');
        });
        document.getElementById(id).classList.add('active');
        el.classList.add('active');
        document.getElementById('pageTitle').innerText = el.innerText;

        // Cargar datos específicos según la sección
        if(id === 'firewall') cargarFirewall();
        if(id === 'rutas') cargarRutas();
        if(id === 'syslog') cargarSyslog();
        if(id === 'kernel') cargarKernel();
        if(id === 'procesos') cargarProcesos();
        if(id === 'realtime') cargarRealtimeStats();
    }

    function cleanIframe(frame){
        try{
            const doc = frame.contentDocument || frame.contentWindow.document;
            const style = doc.createElement('style');
            style.textContent = `
                header, footer, aside, .sidebar, #sidebar, .showSide, .modemenu,
                .dropdown, .main-left, #mainmenu, .nav, .login-text{ display:none !important; }
                .main, .main-right, #maincontent, .container{
                    width:100% !important; margin:0 !important; padding:15px !important;
                    float:none !important;
                }
                body{ padding:0 !important; background:white !important; }
                embed, svg, canvas{ width:100% !important; min-height:400px !important; }
            `;
            doc.head.appendChild(style);
        } catch(e){ console.log(e); }
    }

    async function cargarDatosSistema(){
        try{
            const response = await fetch('/api/dashboard/data');
            const data = await response.json();
            if(data.status === 'success'){
                const info = data.data;
                const memUsed = info.memory?.mem?.used || 0;
                const memTotal = info.memory?.mem?.total || 1;
                const memPercent = (memUsed / memTotal) * 100;
                const html = `
                    <div class="stat-card"><strong>🏠 Hostname</strong><span>${info.hostname || 'N/A'}</span></div>
                    <div class="stat-card"><strong>⏱️ Uptime</strong><span>${info.uptime || 'N/A'}</span></div>
                    <div class="stat-card"><strong>📅 Fecha/Hora</strong><span>${info.time || 'N/A'}</span></div>
                    <div class="stat-card"><strong>📈 Carga Promedio</strong><span>${info.load || 'N/A'}</span></div>
                    <div class="stat-card">
                        <strong>💾 Memoria RAM</strong>
                        <span>${memUsed} MB / ${memTotal} MB</span>
                        <div class="memory-bar"><div class="memory-bar-fill" style="width: ${memPercent}%"></div></div>
                    </div>
                    <div class="stat-card"><strong>🔄 CPU</strong><span>${info.cpu || 'N/A'}</span></div>
                    <div class="stat-card"><strong>🐧 Kernel</strong><span>${info.kernel || 'N/A'}</span></div>
                    <div class="stat-card"><strong>📡 Modelo</strong><span>${info.model || 'N/A'}</span></div>
                `;
                document.getElementById('systemData').innerHTML = html;
            }
        } catch(e){ console.error(e); }
    }

    async function cargarFirewall(){
        try{
            const response = await fetch('/api/dashboard/firewall');
            const data = await response.json();
            if(data.status === 'success'){
                document.getElementById('firewallData').innerHTML = `<pre>${escapeHtml(data.data)}</pre>`;
            } else {
                document.getElementById('firewallData').innerHTML = `Error: ${data.message}`;
            }
        } catch(e){
            document.getElementById('firewallData').innerHTML = `Error: ${e.message}`;
        }
    }

    async function cargarRutas(){
        try{
            const response = await fetch('/api/dashboard/routes');
            const data = await response.json();
            if(data.status === 'success'){
                document.getElementById('routesData').innerHTML = `<pre>${escapeHtml(data.data)}</pre>`;
            } else {
                document.getElementById('routesData').innerHTML = `Error: ${data.message}`;
            }
        } catch(e){
            document.getElementById('routesData').innerHTML = `Error: ${e.message}`;
        }
    }

    async function cargarSyslog(){
        try{
            const response = await fetch('/api/dashboard/syslog');
            const data = await response.json();
            if(data.status === 'success'){
                document.getElementById('syslogData').innerHTML = `<pre>${escapeHtml(data.data)}</pre>`;
            } else {
                document.getElementById('syslogData').innerHTML = `Error: ${data.message}`;
            }
        } catch(e){
            document.getElementById('syslogData').innerHTML = `Error: ${e.message}`;
        }
    }

    async function cargarKernel(){
        try{
            const response = await fetch('/api/dashboard/kernel');
            const data = await response.json();
            if(data.status === 'success'){
                document.getElementById('kernelData').innerHTML = `<pre>${escapeHtml(data.data)}</pre>`;
            } else {
                document.getElementById('kernelData').innerHTML = `Error: ${data.message}`;
            }
        } catch(e){
            document.getElementById('kernelData').innerHTML = `Error: ${e.message}`;
        }
    }

    async function cargarProcesos(){
        try{
            const response = await fetch('/api/dashboard/processes');
            const data = await response.json();
            if(data.status === 'success'){
                document.getElementById('processesData').innerHTML = `<pre>${escapeHtml(data.data)}</pre>`;
            } else {
                document.getElementById('processesData').innerHTML = `Error: ${data.message}`;
            }
        } catch(e){
            document.getElementById('processesData').innerHTML = `Error: ${e.message}`;
        }
    }

    async function cargarRealtimeStats(){
        try{
            const response = await fetch('/api/dashboard/realtime');
            const data = await response.json();
            if(data.status === 'success'){
                const stats = data.data;
                const html = `
                    <div class="realtime-card"><h4>📊 CPU</h4><div class="value">${stats.cpu || '0'}%</div></div>
                    <div class="realtime-card"><h4>💾 RAM Usada</h4><div class="value">${stats.memory || '0'} MB</div><div class="unit">de ${stats.memory_total || '0'} MB</div></div>
                    <div class="realtime-card"><h4>📈 Carga Promedio</h4><div class="value">${stats.load || '0'}</div></div>
                    <div class="realtime-card"><h4>🔌 Conexiones</h4><div class="value">${stats.connections || '0'}</div></div>
                    <div class="realtime-card"><h4>⏱️ Uptime</h4><div class="value">${stats.uptime || '0'}</div></div>
                `;
                document.getElementById('realtimeStats').innerHTML = html;
            }
        } catch(e){
            console.error(e);
        }
    }

    function escapeHtml(str){
        if(!str) return '';
        return str.replace(/[&<>]/g, function(m){
            if(m === '&') return '&amp;';
            if(m === '<') return '&lt;';
            if(m === '>') return '&gt;';
            return m;
        });
    }

    // Inicialización
    cargarDatosSistema();
    setInterval(cargarDatosSistema, 30000);
    setInterval(cargarRealtimeStats, 5000);
</script>
</body>
</html>
