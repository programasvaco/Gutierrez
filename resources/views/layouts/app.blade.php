<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Gestión')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .content-wrapper {
            flex: 1;
            padding: 2rem 0;
        }
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            border: none;
            border-radius: 0.5rem;
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #667eea;
            font-weight: 600;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
        .badge-activo {
            background-color: #28a745;
        }
        .badge-inactivo {
            background-color: #dc3545;
        }
        footer {
            background-color: #f8f9fa;
            padding: 1rem 0;
            margin-top: auto;
        }
        .navbar-nav .dropdown-menu {
            background-color: #5a67d8;
        }
        .navbar-nav .dropdown-item {
            color: #fff;
        }
        .navbar-nav .dropdown-item:hover {
            background-color: #6875db;
        }
        .dropdown-divider {
            border-color: rgba(255,255,255,0.2);
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                <i class="fas fa-box"></i> Sistema de Gestión
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Catálogos -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('productos.*') || request()->routeIs('almacenes.*') || request()->routeIs('proveedores.*') ? 'active' : '' }}" href="#" id="catalogosDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-book"></i> Catálogos
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('productos.index') }}"><i class="fas fa-box"></i> Productos</a></li>
                            <li><a class="dropdown-item" href="{{ route('almacenes.index') }}"><i class="fas fa-warehouse"></i> Almacenes</a></li>
                            <li><a class="dropdown-item" href="{{ route('proveedores.index') }}"><i class="fas fa-truck"></i> Proveedores</a></li>
                        </ul>
                    </li>

                    <!-- Operaciones -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('compras.*') || request()->routeIs('traspasos.*') ? 'active' : '' }}" href="#" id="operacionesDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cash-register"></i> Operaciones
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('compras.index') }}"><i class="fas fa-shopping-cart"></i> Compras</a></li>
                            <li><a class="dropdown-item" href="{{ route('traspasos.index') }}"><i class="fas fa-exchange-alt"></i> Traspasos</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('traspasos.por-recibir') }}"><i class="fas fa-truck-loading"></i> Traspasos Por Recibir</a></li>
                        </ul>
                    </li>

                    <!-- Consultas -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('inventarios.*') || request()->routeIs('cxpagar.*') ? 'active' : '' }}" href="#" id="consultasDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-search"></i> Consultas
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('inventarios.index') }}"><i class="fas fa-boxes"></i> Inventarios</a></li>
                            <li><a class="dropdown-item" href="{{ route('cxpagar.index') }}"><i class="fas fa-money-bill-wave"></i> Cuentas por Pagar</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('inventarios.stock-bajo') }}"><i class="fas fa-exclamation-triangle text-warning"></i> Stock Bajo</a></li>
                            <li><a class="dropdown-item" href="{{ route('cxpagar.vencidas') }}"><i class="fas fa-exclamation-circle text-danger"></i> Cuentas Vencidas</a></li>
                        </ul>
                    </li>

                    <!-- Reportes -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('kardex.*') ? 'active' : '' }}" href="#" id="reportesDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-chart-bar"></i> Reportes
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('kardex.index') }}"><i class="fas fa-clipboard-list"></i> Kardex</a></li>
                            <li><a class="dropdown-item" href="{{ route('kardex.reporte') }}"><i class="fas fa-chart-line"></i> Movimientos General</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="content-wrapper">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <p class="mb-0 text-muted">
                &copy; {{ date('Y') }} Sistema de Gestión. Todos los derechos reservados.
            </p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
