<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Reinicio de Router</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans antialiased">

<div class="min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg overflow-hidden">

        <div class="bg-sky-600 p-6 text-white text-center">
            <i class="fas fa-router fa-3x mb-3"></i>
            <h1 class="text-2xl font-bold uppercase tracking-wide">Gestión de Red</h1>
            <p class="text-sky-100 text-sm">Dispositivo: NuupNet Router</p>
        </div>

        <div class="p-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <p class="text-sm font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="text-center mb-8">
                <h2 class="text-gray-800 text-lg font-semibold">Reiniciar Dispositivo</h2>
                <p class="text-gray-500 text-sm mt-2">
                    Esta acción cerrará todas las conexiones activas y reiniciará el hardware en la IP <span class="font-mono font-bold text-gray-700">192.168.10.1</span>.
                </p>
            </div>

            <form action="{{ route('router2.reboot') }}" method="POST" id="rebootForm">
                @csrf
                <button type="submit"
                        id="btnSubmit"
                        class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-4 rounded-lg transition duration-300 ease-in-out transform hover:scale-105 flex items-center justify-center shadow-md">
                    <i class="fas fa-power-off mr-2"></i>
                    REINICIAR AHORA
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ url('/') }}" class="text-sky-600 hover:underline text-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Volver al tablero
                </a>
            </div>
        </div>

        <div class="bg-gray-50 p-4 border-t border-gray-100 text-center">
            <span class="text-xs text-gray-400 uppercase tracking-widest">Estado: Online</span>
        </div>
    </div>
</div>

<script>
    document.getElementById('rebootForm').onsubmit = function(e) {
        // Confirmación antes de proceder
        const confirmar = confirm("¿Estás seguro de que deseas reiniciar el router ahora mismo?");

        if (!confirmar) {
            e.preventDefault();
            return false;
        }

        // Deshabilitar botón para evitar múltiples clics
        const btn = document.getElementById('btnSubmit');
        btn.disabled = true;
        btn.classList.replace('bg-red-500', 'bg-gray-400');
        btn.classList.remove('hover:bg-red-600', 'hover:scale-105');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Procesando...';
    };
</script>

</body>
</html>
