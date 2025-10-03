<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sakila - Sistema de Rentas')</title>
    
    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        /* Asegurar que la navbar esté en la parte superior */
        .navbar {
            z-index: 1030;
            position: fixed;
            top: 0;
            width: 100%;
        }
        
        /* Evitar que el contenido se sobreponga con la navbar */
        body {
            padding-top: 76px; /* Altura aproximada de la navbar */
        }
        
        /* Dropdown styles for search */
        .search-dropdown {
            max-height: 300px;
            overflow-y: auto;
            z-index: 1050;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
        }
    </style>
    @stack('styles')
</head>
<body>
    @auth
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('rentals.index') }}">
                <i class="fas fa-film me-2"></i>Sakila Sistema
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('rentals.*') ? 'active' : '' }}" href="{{ route('rentals.index') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('rentals.*') ? 'active' : '' }}" href="#" id="rentalsDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-handshake me-1"></i>Rentas
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('rentals.create') }}"><i class="fas fa-plus me-2"></i>Nueva Renta</a></li>
                            <li><a class="dropdown-item" href="{{ route('rentals.active') }}"><i class="fas fa-play me-2"></i>Rentas Activas</a></li>
                            <li><a class="dropdown-item" href="{{ route('rentals.overdue') }}"><i class="fas fa-exclamation-triangle me-2"></i>Rentas Atrasadas</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Devoluciones</h6></li>
                            <li><a class="dropdown-item" href="{{ route('rentals.return.index') }}"><i class="fas fa-undo me-2"></i>Procesar Devoluciones</a></li>
                            <li><a class="dropdown-item" href="{{ route('rentals.return.history') }}"><i class="fas fa-history me-2"></i>Historial de Devoluciones</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('films.*') ? 'active' : '' }}" href="#" id="filmsDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-film me-1"></i>Películas
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('films.index') }}"><i class="fas fa-list me-2"></i>Ver Todas</a></li>
                            <li><a class="dropdown-item" href="{{ route('films.available') }}"><i class="fas fa-check-circle me-2"></i>Disponibles</a></li>
                            <li><a class="dropdown-item" href="{{ route('films.create') }}"><i class="fas fa-plus me-2"></i>Agregar Película</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                            <i class="fas fa-users me-1"></i>Clientes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('actors.*') ? 'active' : '' }}" href="{{ route('actors.index') }}">
                            <i class="fas fa-user-tie me-1"></i>Actores
                        </a>
                    </li>
                </ul>
                
                <!-- Información del usuario -->
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <div class="user-avatar me-2">
                                @if(auth()->user()->picture)
                                    <img src="data:image/jpeg;base64,{{ base64_encode(auth()->user()->picture) }}" 
                                         alt="Avatar" class="rounded-circle" width="32" height="32">
                                @else
                                    <div class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 32px; height: 32px; background: #6c757d; color: white; font-size: 14px;">
                                        {{ substr(auth()->user()->first_name, 0, 1) }}{{ substr(auth()->user()->last_name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="user-info">
                                <div class="fw-bold">{{ auth()->user()->full_name }}</div>
                                <small class="text-muted">Tienda #{{ auth()->user()->store_id }}</small>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <div class="dropdown-header">
                                    <strong>{{ auth()->user()->full_name }}</strong><br>
                                    <small class="text-muted">{{ auth()->user()->email }}</small>
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Configuración</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    @endauth

    <main class="@guest container-fluid @else pt-4 @endguest">
        @if(session('success'))
            <div class="container">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
