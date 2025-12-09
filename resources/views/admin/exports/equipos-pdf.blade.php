<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Equipos - HackZone</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #4a148c;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #4a148c;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #666;
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #4a148c;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .id-badge {
            background-color: #4a148c;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Lista de Equipos - HackZone</h1>
        <p>Generado el: {{ date('d/m/Y H:i:s') }}</p>
        <p>Total de equipos: {{ $equipos->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nombre del Equipo</th>
                <th>ID</th>
                <th>Fecha de Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($equipos as $equipo)
            <tr>
                <td>{{ $equipo->nombre ?? 'Sin Nombre' }}</td>
                <td>
                    <span class="id-badge">{{ $equipo->id }}</span>
                </td>
                <td>{{ $equipo->created_at ? $equipo->created_at->format('d/m/Y H:i:s') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>HackZone - Sistema de Gestión de Competencias</p>
    </div>
</body>
</html>

