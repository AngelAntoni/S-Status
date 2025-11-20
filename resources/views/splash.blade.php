<!DOCTYPE html>
<html lang="es" style="background: #111 !important;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Splash</title>
    
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
    </style>
</head>
<body class="fade-transition">

    <div class="splash">
        <img src="{{ asset('img/STATUS.png') }}" alt="Logo">
        <h2>Bienvenido</h2>
    </div>

    <script>
        setTimeout(() => {
            document.body.style.background = 'linear-gradient(to bottom, #111, #222)';
            setTimeout(() => {
                window.location.href = "{{ route('hub') }}"; 
            }, 300);
        }, 4000); 
    </script>

</body>
</html>