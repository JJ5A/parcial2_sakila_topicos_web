<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sakila - Base de Datos de Películas')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('rentals.index') }}">
                <i class="fas fa-film me-2"></i>Sakila DB
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="rentalsDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-handshake me-1"></i>Rentas
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('rentals.index') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('rentals.create') }}">
                                <i class="fas fa-plus me-1"></i>Nueva Renta
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('rentals.return') }}">
                                <i class="fas fa-undo me-1"></i>Procesar Devolución
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('rentals.overdue') }}">
                                <i class="fas fa-exclamation-triangle me-1 text-danger"></i>Rentas Atrasadas
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="filmsDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-film me-1"></i>Películas
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('films.index') }}">
                                <i class="fas fa-list me-1"></i>Lista de Películas
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('films.create') }}">
                                <i class="fas fa-plus me-1"></i>Nueva Película
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="actorsDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-users me-1"></i>Actores
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('actors.index') }}">
                                <i class="fas fa-list me-1"></i>Lista de Actores
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('actors.create') }}">
                                <i class="fas fa-plus me-1"></i>Nuevo Actor
                            </a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>