<!DOCTYPE html>
<html lang="es" style="background: #111 !important;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Splash</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            height: 100% !important;
            width: 100% !important;
            background: #111 !important;
            overflow: hidden !important;
        }
        
        body {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        body.loaded {
            opacity: 1;
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('loaded');
        });
    </script>

    <style>
        .splash {
            display: flex;
            flex-direction: column;   
            align-items: center;
            justify-content: center;
            height: 100vh;
            width: 100%;
            background: linear-gradient(135deg, #1a1a1a, #0d0d0d);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999;
            animation: smoothFadeOut 1.5s ease 3s forwards;
        }

        .splash img {
            width: 250px;
            animation: bounce 2s infinite;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.5));
        }

        .splash h2 {
            margin-top: 20px;
            font-family: Arial, sans-serif;
            font-size: 1.5rem;
            color: rgb(255, 255, 255); 
            opacity: 0;
            animation: fadeIn 2s ease forwards 1s;
            text-shadow: 0 2px 10px rgba(0,0,0,0.7);
        }

        @keyframes smoothFadeOut {
            0% {
                opacity: 1;
                transform: scale(1);
            }
            70% {
                opacity: 0.3;
                transform: scale(0.95);
            }
            100% {
                opacity: 0;
                transform: scale(0.9);
                visibility: hidden;
            }
        }

        @keyframes bounce {
            0%, 100% { 
                transform: translateY(0); 
            }
            50% { 
                transform: translateY(-20px); 
            }
        }

        @keyframes fadeIn {
            from { 
                opacity: 0;
                transform: translateY(20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-transition {
            transition: all 0.8s ease-in-out;
        }

        .login-container {
            display: none;
            position: relative;
            height: 100vh;
            width: 100%;
            background: linear-gradient(135deg, #111, #222);
        }
        .login-wrap {
            max-width: 420px;
            margin: 0 auto;
            padding-top: 8vh;
        }
        .login-card {
            border-radius: 16px;
            background: #1f2125;
            color: #e0e0e0;
            border: 1px solid #333;
            box-shadow: 0 16px 40px rgba(0,0,0,.25);
        }
        .login-card .form-label { color: #f1f1f1; }
        .login-card .form-control { background-color: #2a2d31; border: 1px solid #444; color: #fff; }
        .login-card .form-control::placeholder { color: #bbb; }
        .login-card .btn-success { background: #198754; border: none; }
        .login-card .btn-success:hover { background: #157347; }
</style>
</head>
<body class="fade-transition">

    <div class="splash">
        <img src="{{ asset('img/STATUS.png') }}" alt="Logo">
        <h2>Bienvenido</h2>
    </div>

    <div class="login-container">
        <div class="login-wrap">
            <div class="card login-card p-4">
                <h5 class="fw-bold text-center mb-3">Iniciar sesión</h5>
                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" name="email" class="form-control" placeholder="usuario@ejemplo.com" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Recordarme</label>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="/" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success">Acceder</button>
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('register') }}" class="text-decoration-none">
                            Registrarse
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        setTimeout(() => {
            document.body.style.background = 'linear-gradient(to bottom, #111, #222)';
            const splash = document.querySelector('.splash');
            const login = document.querySelector('.login-container');
            if (splash) splash.style.display = 'none';
            if (login) {
                login.style.display = 'block';
                const emailInput = login.querySelector('input[type="email"]');
                if (emailInput) emailInput.focus();
            }
        }, 4000);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>