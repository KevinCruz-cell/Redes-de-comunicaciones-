<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Prueba de router</title>
</head>
<body>
<h1>Prueba de conexion al router</h1>

@if($result['ok'])
    <p style="color: green;">
        {{ $result['message'] }} Estado HTTP: {{ $result['status'] }}
    </p>
@else
    <p style="color: red;">
        {{ $result['message'] }}
    </p>
@endif

<a href="/login">Ir al login</a>
</body>
</html>
