<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs PostgreSQL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .log-box {
            background: #111;
            color: #00ff88;
            padding: 15px;
            border-radius: 10px;
            min-height: 500px;
            max-height: 700px;
            overflow-y: auto;
            white-space: pre-wrap;
            font-family: Consolas, monospace;
            font-size: 14px;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Visor de Logs de PostgreSQL</h2>
            <p class="text-muted">Aqui puedes ver los archivos .log generados por PostgreSQL.</p>
        </div>
    </div>

    @if ($error)
        <div class="alert alert-danger">
            {{ $error }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header fw-bold">Archivos de log</div>
                <div class="card-body">
                    @if (count($files) > 0)
                        <ul class="list-group">
                            @foreach ($files as $file)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <a href="{{ route('logs.postgresql', ['file' => $file->getFilename()]) }}"
                                       class="text-decoration-none {{ $selectedFile === $file->getFilename() ? 'fw-bold text-primary' : '' }}">
                                        {{ $file->getFilename() }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">No se encontraron archivos .log</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header fw-bold">
                    Contenido del log
                    @if ($selectedFile)
                        <span class="text-muted">- {{ $selectedFile }}</span>
                    @endif
                </div>
                <div class="card-body">
                    @if ($content)
                        <div class="log-box">{{ $content }}</div>
                    @else
                        <p class="text-muted mb-0">No hay contenido para mostrar.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
