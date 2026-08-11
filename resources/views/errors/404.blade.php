<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Página no encontrada | Fundación YAMA</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            text-align: center;
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 24px;
            padding: 60px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        }

        .logo {
            width: 120px;
            height: 120px;
            object-fit: contain;
            margin: 0 auto 24px;
            display: block;
        }

        .error-code {
            font-size: 96px;
            font-weight: 800;
            background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 16px;
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
        }

        p {
            font-size: 16px;
            color: #6b7280;
            line-height: 1.6;
            margin-bottom: 32px;
        }

        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%);
            color: white;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 4px 15px rgba(124, 58, 237, 0.3);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(124, 58, 237, 0.4);
        }

        .btn-secondary {
            display: inline-block;
            background: transparent;
            color: #4f46e5;
            text-decoration: none;
            padding: 14px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            border: 2px solid #4f46e5;
            margin-left: 12px;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .btn-secondary:hover {
            background: #4f46e5;
            color: white;
        }

        @media (max-width: 480px) {
            .container {
                padding: 40px 24px;
            }

            .error-code {
                font-size: 72px;
            }

            .btn, .btn-secondary {
                display: block;
                width: 100%;
                margin: 0 0 12px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="{{ asset('images/logo.png') }}" alt="Logo Fundación YAMA" class="logo">

        <div class="error-code">404</div>

        <h1>Página no encontrada</h1>

        <p>
            Lo sentimos, la página que estás buscando no existe o ha sido movida.<br>
            Pero no te preocupes, la educación sigue aquí para ti.
        </p>

        <a href="{{ url('/') }}" class="btn">Volver al Inicio</a>
        <a href="{{ route('login') }}" class="btn-secondary">Iniciar Sesión</a>
    </div>
</body>
</html>
