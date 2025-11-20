<!doctype html>
<html lang="en"> <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>S-Status</title>

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            
            if (savedTheme === 'dark') {
                document.documentElement.classList.add('dark-mode');
                document.body.classList.add('dark-mode');
            }
        })();
    </script>
    
    <style>
      html, body {
        margin: 0 !important;
        padding: 0 !important;
      }

      body {
        opacity: 0;
        transition: opacity 0.4s ease;
      }
      
      body.loaded {
        opacity: 1;
      }
      
    </style>

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
          document.body.classList.add('loaded');
        }, 50);
      });
      
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
          setTimeout(function() {
            document.body.classList.add('loaded');
          }, 50);
        });
      } else {
        setTimeout(function() {
          document.body.classList.add('loaded');
        }, 50);
      }
    </script>

    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />

    <link rel="stylesheet" href="{{ asset('css/style1.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dark.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
      html, body {
        height: 100%;
        margin: 0;
      }

      body {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
      }

      main {
        flex: 1;
      }

      html {
        scroll-behavior: smooth;
      }
    </style>
  </head>
  <body class="@yield('body_class')">
  <header class="mb-3">
  <nav class="navbar navbar-expand-lg bg-light shadow-sm py-1">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-2">
        <div class="navbar-brand d-flex align-items-center">
          <img src="{{ asset('img/STATUS.png') }}" alt="Logo" width="180" height="90" class="me-1">
        </div>

        <button id="darkModeToggle" class="btn btn-outline-light ms-2">
          <i class="bi bi-moon-stars-fill"></i>
        </button>

<!--         <button class="btn btn-outline-dark" type="button" data-bs-toggle="offcanvas" data-bs-target="#menuLateral" aria-controls="menuLateral">
          <i class="bi bi-list"></i>
        </button> -->
      </div>
    </div>
  </nav>
</header>
<!--     <div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="menuLateral" aria-labelledby="menuLateralLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="menuLateralLabel">Menú</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="list-unstyled">
          <li><a href="/detalles" class="text-white d-block py-2 text-decoration-none"><i class="bi bi-graph-up me-2"></i>Reportes</a></li>
          <li><a href="#" class="text-white d-block py-2 text-decoration-none"><i class="bi bi-gear-fill me-2"></i>Configuración</a></li>
        </ul>
      </div>
    </div> -->

    <main class="container my-4">
      @yield('content')
    </main>

    <footer class="bg-light py-2 text-start text-body-secondary border-top mt-3">
      <div class="container d-flex justify-content-between text-body-secondary">
        <div>18/06/2025 Angel Antonio CS</div>
        <div>Contacto: Antonigel05@gmail.com</div>
      </div>
    </footer>

    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/darkmode.js') }}"></script>
    @yield('scripts')
  </body>
</html>