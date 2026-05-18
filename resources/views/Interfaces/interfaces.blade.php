<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>NuupNet - Interfaces de Red</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .modal { transition: opacity 0.25s ease; }
        body { background-color: #f3f4f6; }
        /* Estilos específicos para la sección de edición detallada estilo LuCI */
        .tab-sub { border-bottom: 1px solid #ddd; background: #eee; color: #555; transition: all 0.2s; }
        .tab-sub-active { background: #fff !important; border: 1px solid #ddd !important; border-bottom: 1px solid #fff !important; color: #000 !important; font-weight: bold; }
        .form-label { width: 240px; text-align: right; padding-right: 20px; font-size: 14px; color: #333; }
        .status-box { background-color: #f9f9f9; border: 1px solid #ddd; padding: 10px; border-radius: 4px; font-size: 13px; line-height: 1.4; }
    </style>
</head>
<body class="p-6 md:p-10 pb-32">
@php
    if (!function_exists('formatBytes')) {
        function formatBytes($bytes, $precision = 2) {
            if ($bytes == 0) return '0 B';
            $units = ['B', 'KB', 'MB', 'GB'];
            $exp = floor(log($bytes, 1024));
            return round($bytes / pow(1024, $exp), $precision) . ' ' . $units[$exp];
        }
    }
@endphp

<div class="max-w-7xl mx-auto bg-white rounded-xl shadow-md" id="main-wrapper">

    <div id="view-list">
        <div class="bg-[#0088cc] px-6 py-4 flex flex-wrap justify-between items-center text-white rounded-t-xl">
            <h1 class="text-2xl font-semibold">NuupNet</h1>
            <a href="{{ route('router2.interfaces') }}" class="bg-[#4dc1f5] hover:bg-[#3baee0] px-4 py-2 rounded text-sm transition flex items-center gap-2 uppercase tracking-wider font-semibold">
                REFRESCAR
            </a>
        </div>

        @if(session('success'))
            <div class="mx-6 mt-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mx-6 mt-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="mx-6 mt-6 bg-[#fcf8e3] border border-[#fbeed5] rounded p-4 text-gray-800 shadow-sm">
            <h4 class="font-bold text-lg text-black mb-1">¡Sin contraseña!</h4>
            <p class="text-sm">No hay ninguna contraseña establecida en este enrutador. Configure una contraseña de root para proteger la interfaz web.</p>
        </div>

        <div class="mx-6 mt-6 border-b border-gray-200 flex flex-wrap bg-gray-100 rounded-t-lg overflow-hidden">
            <button onclick="switchTab('interfaces')" id="btn-tab-interfaces" class="px-5 py-3 text-sm font-medium transition-all bg-white border-b-2 border-[#0088cc] text-gray-800 shadow-sm font-semibold">
                Interfaces
            </button>
            <button onclick="switchTab('global')" id="btn-tab-global" class="px-5 py-3 text-sm font-medium transition-all text-gray-500 hover:bg-gray-200 hover:text-gray-700">
                Opciones globales de red
            </button>
        </div>

        <div id="content-interfaces" class="p-6">
            <h2 class="text-2xl font-normal text-gray-800 mb-4">Interfaces</h2>

            @if(count($interfaces) === 0)
                <div class="text-center py-12 text-gray-500 italic bg-gray-50 rounded-lg">
                    No hay interfaces creadas. Haz clic en "Añadir nueva interfaz" para comenzar.
                </div>
            @else
                <div class="space-y-4">
                    @foreach($interfaces as $iface)
                        <div class="border border-gray-200 rounded-lg p-4 bg-white hover:shadow-sm transition" id="iface-{{ $iface['name'] }}">
                            <div class="flex flex-wrap justify-between items-start gap-2">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="text-xl font-bold text-gray-800">{{ strtoupper($iface['name']) }}</h3>
                                        @if(isset($iface['type']) && $iface['type'] == 'bridge')
                                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-0.5 rounded-full">Puente</span>
                                        @endif
                                        <span class="text-sm text-gray-500">
                                        <i class="fas fa-network-wired"></i> {{ $iface['device'] ?? $iface['name'] }}
                                    </span>
                                    </div>
                                    <div class="text-sm text-gray-600 mt-1">
                                        Protocolo: {{ ucfirst($iface['protocol']) }}
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <form action="{{ route('router2.iface.restart', $iface['name']) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-xs font-semibold px-3 py-1.5 rounded transition flex items-center gap-1">
                                            <i class="fas fa-play"></i> Reiniciar
                                        </button>
                                    </form>
                                    <form action="{{ route('router2.iface.stop', $iface['name']) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 py-1.5 rounded transition flex items-center gap-1">
                                            <i class="fas fa-stop"></i> Detener
                                        </button>
                                    </form>
                                    <button onclick="openEditInterfaceView('{{ $iface['name'] }}')" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded transition flex items-center gap-1">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <form action="{{ route('router2.delete', $iface['name']) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar la interfaz {{ $iface['name'] }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white text-xs font-semibold px-3 py-1.5 rounded transition flex items-center gap-1">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mt-4 text-sm text-gray-700 border-t pt-3">
                                <div><span class="font-semibold">IPv4:</span> {{ $iface['ipv4'] ?? '—' }}</div>
                                <div><span class="font-semibold">MAC:</span> {{ $iface['mac'] ?? '—' }}</div>
                                <div><span class="font-semibold">Estado:</span> <span class="text-green-600"><i class="fas fa-circle text-[10px]"></i> Activa</span></div>
                                <div><span class="font-semibold">Uptime sistema:</span> —</div>
                                <div><span class="font-semibold">RX:</span> —</div>
                                <div><span class="font-semibold">TX:</span> —</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-8">
                <button onclick="openCreateModal()" class="bg-[#0088cc] hover:bg-[#0077b3] text-white px-5 py-2 rounded text-sm font-semibold transition shadow flex items-center gap-2 uppercase tracking-wide">
                    Añadir nueva interfaz...
                </button>
            </div>

            <div class="mt-8 p-4 bg-gray-100 flex flex-wrap justify-end gap-2 items-center border border-gray-200 rounded-lg shadow-sm">
                <div class="relative inline-flex rounded-md shadow-sm">
                    <button type="button" onclick="executeAction('interfaces', 'save_apply')" class="inline-flex items-center rounded-l bg-[#4dc1f5] hover:bg-[#3baee0] px-4 py-2 text-sm font-semibold text-white transition uppercase tracking-wider">
                        Guardar y aplicar
                    </button>
                    <button type="button" onclick="toggleDropdown(event, 'dropdown-menu-interfaces')" class="inline-flex items-center rounded-r bg-[#4dc1f5] hover:bg-[#3baee0] px-3 py-2 text-sm font-semibold text-white border-l border-[#3baee0] transition">
                        <i class="fas fa-caret-down text-xs"></i>
                    </button>

                    <div id="dropdown-menu-interfaces" class="hidden absolute right-0 top-full mt-1 min-w-[210px] bg-white border border-gray-400 shadow-[0_4px_10px_rgba(0,0,0,0.15)] rounded z-[100] text-black">
                        <button type="button" onclick="executeAction('interfaces', 'save_apply')" class="w-full text-left block px-4 py-2 text-xs font-semibold hover:bg-[#b2d7ff] uppercase tracking-wide border-b border-gray-100">
                            Guardar y aplicar
                        </button>
                        <button type="button" onclick="executeAction('interfaces', 'apply_unrestricted')" class="w-full text-left block px-4 py-2 text-xs font-semibold hover:bg-[#b2d7ff] uppercase tracking-wide">
                            Aplicar sin restricción
                        </button>
                    </div>
                </div>
                <button type="button" onclick="executeAction('interfaces', 'save')" class="bg-[#2475a0] hover:bg-[#1d5e82] text-white px-4 py-2 rounded text-sm font-semibold transition uppercase tracking-wider shadow-sm">Guardar</button>
                <button type="button" onclick="executeAction('interfaces', 'reset')" class="bg-[#d9534f] hover:bg-[#c9302c] text-white px-4 py-2 rounded text-sm font-semibold transition uppercase tracking-wider shadow-sm">Restablecer</button>
            </div>
        </div>

        <div id="content-global" class="hidden p-6">
            <div class="border border-gray-200 rounded-lg bg-white shadow-sm">
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <p class="text-sm italic text-gray-800 font-normal">Esta sección aún no contiene valores</p>
                </div>
                <div class="p-4 bg-gray-100 flex flex-wrap justify-end gap-2 items-center border-t border-gray-200">
                    <div class="relative inline-flex rounded-md shadow-sm">
                        <button type="button" onclick="executeAction('global', 'save_apply')" class="inline-flex items-center rounded-l bg-[#4dc1f5] hover:bg-[#3baee0] px-4 py-2 text-sm font-semibold text-white transition uppercase tracking-wider">Guardar y aplicar</button>
                        <button type="button" onclick="toggleDropdown(event, 'dropdown-menu-global')" class="inline-flex items-center rounded-r bg-[#4dc1f5] hover:bg-[#3baee0] px-3 py-2 text-sm font-semibold text-white border-l border-[#3baee0] transition"><i class="fas fa-caret-down text-xs"></i></button>
                        <div id="dropdown-menu-global" class="hidden absolute right-0 top-full mt-1 min-w-[210px] bg-white border border-gray-400 shadow-[0_4px_10px_rgba(0,0,0,0.15)] rounded z-[100] text-black">
                            <button type="button" onclick="executeAction('global', 'save_apply')" class="w-full text-left block px-4 py-2 text-xs font-semibold hover:bg-[#b2d7ff] uppercase tracking-wide border-b border-gray-100">Guardar y aplicar</button>
                            <button type="button" onclick="executeAction('global', 'apply_unrestricted')" class="w-full text-left block px-4 py-2 text-xs font-semibold hover:bg-[#b2d7ff] uppercase tracking-wide">Aplicar sin restricción</button>
                        </div>
                    </div>
                    <button type="button" onclick="executeAction('global', 'save')" class="bg-[#2475a0] hover:bg-[#1d5e82] text-white px-4 py-2 rounded text-sm font-semibold transition uppercase tracking-wider shadow-sm">Guardar</button>
                    <button type="button" onclick="executeAction('global', 'reset')" class="bg-[#d9534f] hover:bg-[#c9302c] text-white px-4 py-2 rounded text-sm font-semibold transition uppercase tracking-wider shadow-sm">Restablecer</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="editModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50 p-4">
    <div class="bg-white max-w-5xl w-full rounded shadow-xl overflow-y-auto max-h-[95vh]">
        <div class="bg-gray-100 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-xl font-normal text-black">Interfaces » <span id="edit-iface-title">LAN</span></h2>
            <button onclick="closeEditInterfaceView()" class="text-gray-500 hover:text-gray-700 text-2xl">&times;</button>
        </div>

        <div class="flex flex-wrap bg-gray-100 border-b border-gray-300">
            <button type="button" onclick="switchSubTab('general')" id="tab-sub-general" class="tab-sub tab-sub-active px-4 py-2 text-sm border-r border-gray-300">Configuración general</button>
            <button type="button" onclick="switchSubTab('avanzada')" id="tab-sub-avanzada" class="tab-sub px-4 py-2 text-sm border-r border-gray-300">Configuración avanzada</button>
            <button type="button" onclick="switchSubTab('fisica')" id="tab-sub-fisica" class="tab-sub px-4 py-2 text-sm border-r border-gray-300">Configuración física</button>
            <button type="button" onclick="switchSubTab('cortafuegos')" id="tab-sub-cortafuegos" class="tab-sub px-4 py-2 text-sm border-r border-gray-300">Configuración del cortafuegos</button>
            <button type="button" onclick="switchSubTab('dhcp')" id="tab-sub-dhcp" class="tab-sub px-4 py-2 text-sm">Servidor DHCP</button>
        </div>

        <form id="general-edit-form" method="POST">
            @csrf
            @method('PUT')

            <div class="p-8 space-y-8">
                <div class="flex flex-col sm:flex-row gap-4 items-start">
                    <label class="form-label font-bold sm:w-48">Estado</label>
                    <div class="status-box flex-1 max-w-md flex gap-4">
                        <div class="text-3xl text-gray-400 pt-2"><i class="fas fa-network-wired"></i></div>
                        <div>
                            <p><strong>Dispositivo:</strong> <span id="stat-device">br-lan</span></p>
                            <p><strong>Tiempo de actividad:</strong> 0h 28m 39s</p>
                            <p><strong>MAC:</strong> <span id="stat-mac">EC:75:0C:48:B8:F7</span></p>
                            <p><strong>RX:</strong> 485.66 KB (3862 Paq.)</p>
                            <p><strong>TX:</strong> 2.37 MB (3657 Paq.)</p>
                            <p><strong>IPv4:</strong> <span id="stat-ipv4">192.168.10.1/24</span></p>
                        </div>
                    </div>
                </div>

                <div id="subcontent-general" class="space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center">
                        <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Protocolo</label>
                        <div class="flex-1 max-w-md">
                            <select name="proto" id="edit-proto-select" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm bg-white focus:outline-none text-black font-normal">
                                <option value="dhcp">Cliente DHCP</option>
                                <option value="static" selected>Dirección estática</option>
                                <option value="ppp">PPP</option>
                                <option value="pppoe">PPPoE</option>
                                <option value="none">No administrado</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-start">
                        <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0 pt-0.5">Iniciar en el arranque</label>
                        <div class="flex-1 max-w-md">
                            <input type="checkbox" name="auto" checked class="accent-[#3071a9] h-4 w-4 border-gray-300 rounded">
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center">
                        <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Dirección IPv4</label>
                        <div class="flex-1 max-w-md flex">
                            <input type="text" name="ipaddr" id="edit-ipaddr" class="flex-1 border border-gray-300 rounded-l px-2 py-1.5 text-sm focus:outline-none" value="192.168.10.1">
                            <button type="button" class="bg-gray-100 border border-l-0 border-gray-300 px-3 py-1 text-gray-600 rounded-r">...</button>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center">
                        <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Máscara de red IPv4</label>
                        <div class="flex-1 max-w-md">
                            <select name="netmask" id="edit-netmask" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm bg-white focus:outline-none text-black">
                                <option value="">Sin especificar</option>
                                <option value="255.255.255.0" selected>255.255.255.0</option>
                                <option value="255.255.0.0">255.255.0.0</option>
                                <option value="255.0.0.0">255.0.0.0</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center">
                        <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Puerta de enlace IPv4</label>
                        <div class="flex-1 max-w-md">
                            <input type="text" name="gateway" id="edit-gateway" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none" value="192.168.20.254 (soto)">
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center">
                        <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Difusión IPv4</label>
                        <div class="flex-1 max-w-md">
                            <input type="text" name="broadcast" id="edit-broadcast" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none" value="192.168.10.255">
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center">
                        <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Usar servidores DNS personalizados</label>
                        <div class="flex-1 max-w-md flex">
                            <input type="text" name="dns" id="edit-dns" placeholder="-- Personalizado --" class="flex-1 border border-gray-300 rounded-l px-2 py-1.5 text-sm focus:outline-none">
                            <button type="button" class="bg-[#3071a9] text-white px-3 py-1 rounded-r"><i class="fas fa-plus text-xs"></i></button>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-start">
                        <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0 pt-0.5">Longitud de asignación de IPv6</label>
                        <div class="flex-1 max-w-md">
                            <select class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm bg-white focus:outline-none text-black">
                                <option selected>Desactivado</option>
                                <option>64</option>
                            </select>
                            <p class="text-xs text-gray-400 mt-1.5 font-normal">Asigna una parte de la longitud dada de cada prefijo IPv6 público a esta interfaz</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center">
                        <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Dirección IPv6</label>
                        <div class="flex-1 max-w-md flex">
                            <input type="text" placeholder="Añadir dirección IPv6..." class="flex-1 border border-gray-300 rounded-l px-2 py-1.5 text-sm italic focus:outline-none">
                            <button type="button" class="bg-[#3071a9] text-white px-3 py-1 rounded-r"><i class="fas fa-plus text-xs"></i></button>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center">
                        <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Puerta de enlace IPv6</label>
                        <div class="flex-1 max-w-md">
                            <input type="text" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none">
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-start">
                        <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0 pt-0.5">Prefijo IPv6 enrutado</label>
                        <div class="flex-1 max-w-md">
                            <input type="text" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none">
                            <p class="text-xs text-gray-400 mt-1.5 font-normal">Prefijo público enrutado a este dispositivo para su distribución a los clientes.</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-start">
                        <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0 pt-0.5">Sufijo IPv6</label>
                        <div class="flex-1 max-w-md">
                            <input type="text" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none" value="::1">
                            <p class="text-xs text-gray-400 mt-1.5 font-normal">Opcional. Valores permitidos: 'eui64', 'random', valor fijo como '::1' o '::1:2'. Cuando se recibe un prefijo IPv6 (como 'a:b:c:d::') desde un servidor delegante, use el sufijo (como '::1') para formar la dirección IPv6 ('a:b:c:d::1') para la interfaz.</p>
                        </div>
                    </div>
                </div>

                <div id="subcontent-avanzada" class="hidden space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-start">
                        <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0 pt-0.5">Utilizar la gestión integrada de IPv6</label>
                        <div class="flex-1 max-w-md">
                            <input type="checkbox" name="dns6" checked class="accent-[#3071a9] h-4 w-4 border-gray-300 rounded">
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-start">
                        <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0 pt-0.5">Forzar enlace</label>
                        <div class="flex-1 max-w-md">
                            <input type="checkbox" name="force_link" checked class="accent-[#3071a9] h-4 w-4 border-gray-300 rounded">
                            <p class="text-xs text-gray-400 mt-1.5 font-normal">Configura las propiedades de la interfaz independientemente del operador de enlace (si está configurado, los eventos de detección de operador no invocan los controladores de conexión en caliente).</p>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center">
                        <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Reemplazar dirección MAC</label>
                        <div class="flex-1 max-w-md">
                            <input type="text" name="macaddr" id="edit-adv-mac" placeholder="EC:75:0C:48:B8:F7" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none">
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center">
                        <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Reemplazar MTU</label>
                        <div class="flex-1 max-w-md">
                            <input type="text" name="mtu" id="edit-adv-mtu" placeholder="1500" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none">
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center">
                        <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Usar métrica de puerta de enlace</label>
                        <div class="flex-1 max-w-md">
                            <input type="text" name="metric" id="edit-adv-metric" placeholder="0" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none">
                        </div>
                    </div>
                </div>

                <div id="subcontent-fisica" class="hidden space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-start">
                        <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0 pt-0.5">Puentear interfaces</label>
                        <div class="flex-1 max-w-md">
                            <input type="checkbox" id="edit-phy-bridge" name="bridge" checked onchange="togglePhyBridgeOptions()" class="accent-[#3071a9] h-4 w-4 border-gray-300 rounded">
                            <p class="text-xs text-gray-400 mt-1.5 font-normal">Crea un puente sobre la interfaz o interfaces asociadas</p>
                        </div>
                    </div>

                    <div id="phy-bridge-fields" class="space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-start">
                            <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0 pt-0.5">Activar <span class="underline decoration-dotted cursor-help" title="Spanning Tree Protocol">STP</span></label>
                            <div class="flex-1 max-w-md">
                                <input type="checkbox" name="stp" class="accent-[#3071a9] h-4 w-4 border-gray-300 rounded">
                                <p class="text-xs text-gray-400 mt-1.5 font-normal">Activa el protocolo Spanning Tree en este puente</p>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-start">
                            <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0 pt-0.5">Activar <span class="underline decoration-dotted cursor-help" title="Internet Group Management Protocol">IGMP</span> Snooping</label>
                            <div class="flex-1 max-w-md">
                                <input type="checkbox" name="igmp" class="accent-[#3071a9] h-4 w-4 border-gray-300 rounded">
                                <p class="text-xs text-gray-400 mt-1.5 font-normal">Activa el protocolo IGMP Snooping en este puente</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-start">
                        <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0 pt-0.5">Interfaz</label>
                        <div class="flex-1 max-w-md">
                            <div class="border border-gray-300 rounded p-2 bg-white space-y-2 max-h-48 overflow-y-auto">
                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer p-0.5 hover:bg-gray-50">
                                    <input type="checkbox" name="ifnames[]" value="eth0.1" checked class="accent-[#3071a9]">
                                    <span class="flex items-center gap-1.5"><i class="fas fa-network-wired text-xs text-gray-500"></i> Switch VLAN: "eth0.1" (lan, silbana)</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer p-0.5 hover:bg-gray-50">
                                    <input type="checkbox" name="ifnames[]" value="eth0.2" class="accent-[#3071a9]">
                                    <span class="flex items-center gap-1.5"><i class="fas fa-network-wired text-xs text-gray-500"></i> Switch VLAN: "eth0.2" (wan)</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer p-0.5 hover:bg-gray-50">
                                    <input type="checkbox" name="ifnames[]" value="eth0" class="accent-[#3071a9]">
                                    <span class="flex items-center gap-1.5"><i class="fas fa-toggle-on text-xs text-gray-500"></i> Conmutador ethernet: "eth0" (soto)</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer p-0.5 hover:bg-gray-50">
                                    <input type="checkbox" name="ifnames[]" value="wlan0" checked class="accent-[#3071a9]">
                                    <span class="flex items-center gap-1.5"><i class="fas fa-wifi text-xs text-gray-500"></i> Red Wi-Fi: Master "NuupNet" (lan)</span>
                                </label>
                                <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer p-0.5 hover:bg-gray-50">
                                    <input type="checkbox" name="ifnames[]" value="custom" class="accent-[#3071a9]">
                                    <span class="flex items-center gap-1.5"><i class="fas fa-edit text-xs text-gray-500"></i> -- Personalizado --</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="subcontent-cortafuegos" class="hidden space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-start">
                        <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0 pt-0.5">Crear / Asignar zona de cortafuegos</label>
                        <div class="flex-1 max-w-md">
                            <p class="text-xs text-gray-500 mb-2 font-normal">Elija la zona de cortafuegos que desea asignar a esta interfaz. Seleccione <em>- no especificado -</em> para quitar la interfaz de la zona actual o llene el campo <em>- personalizado -</em> para crear una nueva y asignar la interfaz a la misma.</p>
                            <select name="firewall_zone" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm bg-white focus:outline-none text-black font-normal">
                                <option value="unspecified">-- no especificado --</option>
                                <option value="lan" selected>lan: lan</option>
                                <option value="wan">wan: wan wan6</option>
                                <option value="guest">guest: guest</option>
                                <option value="custom">-- personalizado --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="subcontent-dhcp" class="hidden">
                    <div class="flex flex-wrap bg-gray-100 border border-gray-200 rounded-t mb-4">
                        <button type="button" onclick="switchDhcpSubTab('dhcp-general')" id="tab-dhcp-general" class="tab-sub tab-sub-active px-4 py-2 text-sm border-r border-gray-200">Configuración general</button>
                        <button type="button" onclick="switchDhcpSubTab('dhcp-avanzada')" id="tab-dhcp-avanzada" class="tab-sub px-4 py-2 text-sm border-r border-gray-200">Configuración avanzada</button>
                        <button type="button" onclick="switchDhcpSubTab('dhcp-ipv6')" id="tab-dhcp-ipv6" class="tab-sub px-4 py-2 text-sm">Configuración IPv6</button>
                    </div>

                    <div id="content-dhcp-general" class="space-y-6 pt-2">
                        <div class="flex flex-col sm:flex-row sm:items-start">
                            <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0 pt-0.5">Ignorar interfaz</label>
                            <div class="flex-1 max-w-md">
                                <input type="checkbox" name="dhcp_ignore" class="accent-[#3071a9] h-4 w-4 border-gray-300 rounded">
                                <p class="text-xs text-gray-400 mt-1.5 font-normal">Desactivar DHCP para esta interfaz.</p>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Inicio</label>
                            <div class="flex-1 max-w-md">
                                <input type="text" name="dhcp_start" value="100" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none">
                                <p class="text-xs text-gray-400 mt-1.5 font-normal">Dirección base para alquileres de red asignados.</p>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Límite</label>
                            <div class="flex-1 max-w-md">
                                <input type="text" name="dhcp_limit" value="150" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none">
                                <p class="text-xs text-gray-400 mt-1.5 font-normal">Cantidad de alquileres máximos ofrecidos.</p>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Tiempo de concesión</label>
                            <div class="flex-1 max-w-md">
                                <input type="text" name="dhcp_leasetime" value="12h" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none">
                                <p class="text-xs text-gray-400 mt-1.5 font-normal">Tiempo de vida de los alquileres. (ej: 12h, 1d)</p>
                            </div>
                        </div>
                    </div>

                    <div id="content-dhcp-avanzada" class="hidden space-y-6 pt-2">
                        <div class="flex flex-col sm:flex-row sm:items-start">
                            <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0 pt-0.5">DHCP dinámico</label>
                            <div class="flex-1 max-w-md">
                                <input type="checkbox" name="dhcp_dynamic" checked class="accent-[#3071a9] h-4 w-4 border-gray-300 rounded">
                                <p class="text-xs text-gray-400 mt-1.5 font-normal">Asignar dinámicamente direcciones DHCP a clientes que no están listados estáticamente.</p>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-start">
                            <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0 pt-0.5">Servidor DHCP autoritativo</label>
                            <div class="flex-1 max-w-md">
                                <input type="checkbox" name="dhcp_force" checked class="accent-[#3071a9] h-4 w-4 border-gray-300 rounded">
                                <p class="text-xs text-gray-400 mt-1.5 font-normal">Forzar DHCP en esta red aunque se detecte otro servidor.</p>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Sufijo de dominio IPv4 local</label>
                            <div class="flex-1 max-w-md">
                                <input type="text" name="dhcp_domain" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none">
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Opciones DHCP</label>
                            <div class="flex-1 max-w-md flex">
                                <input type="text" name="dhcp_options" class="flex-1 border border-gray-300 rounded-l px-2 py-1.5 text-sm focus:outline-none" placeholder="3,192.168.1.1">
                                <button type="button" class="bg-[#3071a9] text-white px-3 py-1 rounded-r"><i class="fas fa-plus text-xs"></i></button>
                            </div>
                        </div>
                    </div>

                    <div id="content-dhcp-ipv6" class="hidden space-y-6 pt-2">
                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Servicio de Router Advertisement</label>
                            <div class="flex-1 max-w-md">
                                <select name="ra_service" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm bg-white focus:outline-none text-black">
                                    <option value="disabled">deshabilitado</option>
                                    <option value="server" selected>modo servidor</option>
                                    <option value="relay">modo retransmisión</option>
                                    <option value="hybrid">modo híbrido</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Servicio DHCPv6</label>
                            <div class="flex-1 max-w-md">
                                <select name="dhcpv6_service" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm bg-white focus:outline-none text-black">
                                    <option value="disabled">deshabilitado</option>
                                    <option value="server" selected>modo servidor</option>
                                    <option value="relay">modo retransmisión</option>
                                    <option value="hybrid">modo híbrido</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Servicio NDP</label>
                            <div class="flex-1 max-w-md">
                                <select name="ndp_service" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm bg-white focus:outline-none text-black">
                                    <option value="disabled" selected>deshabilitado</option>
                                    <option value="relay">modo retransmisión</option>
                                    <option value="hybrid">modo híbrido</option>
                                </select>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Servidores DNS IPv6 anunciados</label>
                            <div class="flex-1 max-w-md flex">
                                <input type="text" name="dns6_announced" placeholder="-- Personalizado --" class="flex-1 border border-gray-300 rounded-l px-2 py-1.5 text-sm focus:outline-none">
                                <button type="button" class="bg-[#3071a9] text-white px-3 py-1 rounded-r"><i class="fas fa-plus text-xs"></i></button>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center">
                            <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Dominios de búsqueda IPv6 anunciados</label>
                            <div class="flex-1 max-w-md flex">
                                <input type="text" name="domains6_announced" placeholder="-- Personalizado --" class="flex-1 border border-gray-300 rounded-l px-2 py-1.5 text-sm focus:outline-none">
                                <button type="button" class="bg-[#3071a9] text-white px-3 py-1 rounded-r"><i class="fas fa-plus text-xs"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-100 pt-6">
                    <button type="button" onclick="closeEditInterfaceView()" class="bg-[#ebebeb] hover:bg-gray-200 text-black px-6 py-2 rounded text-xs font-semibold uppercase tracking-wider transition">Descartar</button>
                    <button type="submit" class="bg-[#3071a9] hover:bg-[#285e8e] text-white px-6 py-2 rounded text-xs font-semibold uppercase tracking-wider transition">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="createModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50 p-4">
    <div class="bg-white max-w-5xl w-full p-8 rounded shadow-xl overflow-y-auto max-h-[95vh]">
        <h3 class="text-2xl font-normal text-black mb-8">Añadir nueva interfaz...</h3>
        <form action="{{ route('router2.crear') }}" method="POST" class="space-y-6">
            @csrf
            <div class="flex flex-col sm:flex-row sm:items-center">
                <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Nombre</label>
                <div class="flex-1 max-w-md">
                    <input type="text" name="nombre" required placeholder="Nuevo nombre de interfaz..." class="w-full border-b border-red-500 text-sm text-gray-800 focus:outline-none py-1">
                </div>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center">
                <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Protocolo</label>
                <div class="flex-1 max-w-md">
                    <select name="proto" id="createProto" class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm bg-white focus:outline-none text-black font-normal">
                        <option value="dhcp" selected>Cliente DHCP</option>
                        <option value="static">Dirección estática</option>
                        <option value="ppp">PPP</option>
                        <option value="pppoe">PPPoE</option>
                        <option value="none">No administrado</option>
                    </select>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-start">
                <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0 pt-0.5">Puentear interfaces</label>
                <div class="flex-1 max-w-md">
                    <input type="checkbox" name="bridge" id="createBridge" class="h-4 w-4 border-gray-300 rounded accent-[#3071a9]">
                    <p class="text-sm text-gray-400 mt-2 font-normal">Crea un puente sobre la interfaz o interfaces asociadas</p>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center">
                <label class="w-full sm:w-48 text-left sm:text-right pr-4 text-sm font-normal text-black mb-1 sm:mb-0">Interfaz</label>
                <div class="flex-1 max-w-md">
                    <select name="ifname" required class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm bg-white focus:outline-none text-gray-700 italic font-semibold">
                        <option value="" disabled selected class="not-italic font-normal text-black">Sin especificar</option>
                        @foreach($dispositivos as $dev)
                            <option value="{{ $dev }}" class="not-italic font-normal text-black">{{ $dev }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div id="staticFieldsCreate" style="display: none;" class="border-t border-dashed pt-4 space-y-4 max-w-md sm:ml-48">
                <h4 class="font-semibold text-sm text-gray-700">Configuración estática</h4>
                <div class="space-y-3">
                    <div><label class="block text-xs font-medium text-gray-600">Dirección IP</label><input type="text" name="ipaddr" class="mt-1 w-full border border-gray-300 rounded p-1.5 text-sm" placeholder="192.168.1.2"></div>
                    <div><label class="block text-xs font-medium text-gray-600">Máscara de red</label><input type="text" name="netmask" class="mt-1 w-full border border-gray-300 rounded p-1.5 text-sm" placeholder="255.255.255.0"></div>
                    <div><label class="block text-xs font-medium text-gray-600">Puerta de enlace</label><input type="text" name="gateway" class="mt-1 w-full border border-gray-300 rounded p-1.5 text-sm" placeholder="192.168.1.1"></div>
                    <div><label class="block text-xs font-medium text-gray-600">DNS</label><input type="text" name="dns" class="mt-1 w-full border border-gray-300 rounded p-1.5 text-sm" placeholder="8.8.8.8"></div>
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-8 border-t border-gray-100">
                <button type="button" onclick="closeCreateModal()" class="bg-[#ebebeb] hover:bg-gray-200 text-black px-4 py-2 rounded text-xs font-normal uppercase tracking-wider transition">Cancelar</button>
                <button type="submit" class="bg-[#3071a9] hover:bg-[#285e8e] text-white px-4 py-2 rounded text-xs font-normal uppercase tracking-wider transition">Crear interfaz</button>
            </div>
        </form>
    </div>
</div>

<script>
    /* NAVEGACIÓN DE PESTAÑAS PRINCIPALES */
    function switchTab(tab) {
        const contentInterfaces = document.getElementById('content-interfaces');
        const contentGlobal = document.getElementById('content-global');
        const btnInterfaces = document.getElementById('btn-tab-interfaces');
        const btnGlobal = document.getElementById('btn-tab-global');

        const activeClass = "px-5 py-3 text-sm font-semibold bg-white border-b-2 border-[#0088cc] text-gray-800 shadow-sm";
        const inactiveClass = "px-5 py-3 text-sm font-medium text-gray-500 hover:bg-gray-200 hover:text-gray-700 transition-all";

        if (tab === 'interfaces') {
            contentInterfaces.classList.remove('hidden');
            contentGlobal.classList.add('hidden');
            btnInterfaces.className = activeClass;
            btnGlobal.className = inactiveClass;
        } else {
            contentGlobal.classList.remove('hidden');
            contentInterfaces.classList.add('hidden');
            btnGlobal.className = activeClass;
            btnInterfaces.className = inactiveClass;
        }
    }

    /* CONTROL DE PESTAÑAS INTERNAS DEL MODAL DE EDICIÓN */
    function switchSubTab(subTab) {
        const sections = ['general', 'avanzada', 'fisica', 'cortafuegos', 'dhcp'];
        sections.forEach(sec => {
            document.getElementById(`subcontent-${sec}`).classList.add('hidden');
            document.getElementById(`tab-sub-${sec}`).classList.remove('tab-sub-active');
        });

        document.getElementById(`subcontent-${subTab}`).classList.remove('hidden');
        document.getElementById(`tab-sub-${subTab}`).classList.add('tab-sub-active');
    }

    /* CONTROL DE SUB-PESTAÑAS DEL SERVIDOR DHCP */
    function switchDhcpSubTab(subTab) {
        const sections = ['dhcp-general', 'dhcp-avanzada', 'dhcp-ipv6'];
        sections.forEach(sec => {
            document.getElementById(`content-${sec}`).classList.add('hidden');
            document.getElementById(`tab-${sec}`).classList.remove('tab-sub-active');
        });

        document.getElementById(`content-${subTab}`).classList.remove('hidden');
        document.getElementById(`tab-${subTab}`).classList.add('tab-sub-active');
    }

    /* OCULTAR / MOSTRAR OPCIONES DE PUENTE FÍSICO */
    function togglePhyBridgeOptions() {
        const isChecked = document.getElementById('edit-phy-bridge').checked;
        document.getElementById('phy-bridge-fields').style.display = isChecked ? 'block' : 'none';
    }

    /* CONTROL DE DROPDOWNS */
    function toggleDropdown(event, id) {
        event.stopPropagation();
        const interfacesMenu = document.getElementById('dropdown-menu-interfaces');
        const globalMenu = document.getElementById('dropdown-menu-global');

        if (id === 'dropdown-menu-interfaces') {
            globalMenu.classList.add('hidden');
            interfacesMenu.classList.toggle('hidden');
        } else {
            interfacesMenu.classList.add('hidden');
            globalMenu.classList.toggle('hidden');
        }
    }

    function executeAction(section, action) {
        document.getElementById('dropdown-menu-interfaces').classList.add('hidden');
        document.getElementById('dropdown-menu-global').classList.add('hidden');
        alert(`Ejecutando acción en ${section}: ${action.toUpperCase()}`);
    }

    /* MANEJO DE FORMULARIO DE CREACIÓN */
    const createProto = document.getElementById('createProto');
    const staticFieldsCreate = document.getElementById('staticFieldsCreate');
    function toggleCreateStatic() {
        staticFieldsCreate.style.display = createProto.value === 'static' ? 'block' : 'none';
    }
    if(createProto) createProto.addEventListener('change', toggleCreateStatic);

    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
        document.getElementById('createModal').classList.add('flex');
    }
    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
        document.getElementById('createModal').classList.remove('flex');
    }

    /* LÓGICA DE APERTURA DEL MODAL DE EDICIÓN AVANZADA */
    function openEditInterfaceView(ifaceName) {
        fetch(`/interfaz2/${ifaceName}/editar`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit-iface-title').innerText = ifaceName.toUpperCase();
                document.getElementById('stat-device').innerText = data.ifname || ifaceName;
                document.getElementById('stat-mac').innerText = data.mac || '—';
                document.getElementById('stat-ipv4').innerText = data.ipaddr ? `${data.ipaddr}/${data.netmask || '24'}` : '—';

                // Campos generales
                document.getElementById('edit-proto-select').value = data.proto || 'dhcp';
                document.getElementById('edit-ipaddr').value = data.ipaddr || '';
                document.getElementById('edit-netmask').value = data.netmask || '';
                document.getElementById('edit-gateway').value = data.gateway || '';
                document.getElementById('edit-dns').value = data.dns || '';

                // Campos Avanzados
                document.getElementById('edit-adv-mac').value = data.macaddr || '';
                document.getElementById('edit-adv-mtu').value = data.mtu || '';
                document.getElementById('edit-adv-metric').value = data.metric || '';

                // Configurar acción del formulario
                document.getElementById('general-edit-form').action = `/interfaz2/${ifaceName}`;

                // Reiniciar a la pestaña General al abrir
                switchSubTab('general');
                switchDhcpSubTab('dhcp-general');
                togglePhyBridgeOptions();

                const editModal = document.getElementById('editModal');
                editModal.classList.remove('hidden');
                editModal.classList.add('flex');
            })
            .catch(err => {
                alert('Error al cargar datos de la interfaz: ' + err);
            });
    }

    function closeEditInterfaceView() {
        const editModal = document.getElementById('editModal');
        editModal.classList.add('hidden');
        editModal.classList.remove('flex');
    }

    window.onclick = function(event) {
        if (event.target === document.getElementById('createModal')) closeCreateModal();
        if (event.target === document.getElementById('editModal')) closeEditInterfaceView();
        if (!event.target.closest('.relative.inline-flex')) {
            document.getElementById('dropdown-menu-interfaces').classList.add('hidden');
            document.getElementById('dropdown-menu-global').classList.add('hidden');
        }
    }
</script>
</body>
</html>
