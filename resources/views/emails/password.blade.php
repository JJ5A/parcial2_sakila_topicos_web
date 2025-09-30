<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido al Sistema Sakila</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 5px 5px;
            border: 1px solid #dee2e6;
        }
        .credentials {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            margin-top: 30px;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeeba;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎬 Sistema de Rentas Sakila</h1>
        <p>¡Bienvenido al equipo!</p>
    </div>
    
    <div class="content">
        <h2>Hola {{ $user->first_name }} {{ $user->last_name }},</h2>
        
        <p>¡Bienvenido al Sistema de Rentas Sakila! Tu cuenta de empleado ha sido creada exitosamente.</p>
        
        <div class="credentials">
            <h3>📋 Tus credenciales de acceso:</h3>
            <p><strong>Usuario:</strong> {{ $user->username }}</p>
            <p><strong>Contraseña temporal:</strong> <code>{{ $password }}</code></p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Tienda asignada:</strong> {{ $user->store->store_id }}</p>
        </div>
        
        <div class="warning">
            <strong>⚠️ Importante:</strong> Esta es una contraseña temporal. Por seguridad, te recomendamos cambiarla después de tu primer inicio de sesión.
        </div>
        
        <p>Puedes acceder al sistema usando el siguiente enlace:</p>
        
        <a href="{{ $loginUrl }}" class="button">Iniciar Sesión</a>
        
        <h3>🎯 ¿Qué puedes hacer en el sistema?</h3>
        <ul>
            <li>🎬 Gestionar rentas de películas</li>
            <li>👥 Administrar clientes</li>
            <li>💰 Procesar pagos y devoluciones</li>
            <li>📊 Ver reportes y estadísticas</li>
            <li>🔍 Consultar inventario disponible</li>
        </ul>
        
        <p>Si tienes alguna pregunta o necesitas ayuda, no dudes en contactar al administrador del sistema.</p>
        
        <p>¡Esperamos que tengas una excelente experiencia trabajando con nuestro sistema!</p>
        
        <p>Saludos cordiales,<br>
        <strong>Equipo Sakila</strong></p>
    </div>
    
    <div class="footer">
        <p>Este es un mensaje automático del Sistema de Rentas Sakila.</p>
        <p>Por favor, no respondas a este correo.</p>
    </div>
</body>
</html>