@extends('layouts.app')

@section('title', 'Películas Disponibles para Rentar')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-0">🎬 Películas Disponibles</h1>
                    <p class="text-muted">Inventario disponible para renta</p>
                </div>
                <div>
                    <a href="{{ route('rentals.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Renta
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('films.available') }}" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label">Buscar película</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Título de la película...">
                        </div>
                        <div class="col-md-3">
                            <label for="rating" class="form-label">Clasificación</label>
                            <select class="form-select" id="rating" name="rating">
                                <option value="">Todas las clasificaciones</option>
                                @foreach($ratings as $value => $label)
                                    <option value="{{ $value }}" {{ request('rating') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="category" class="form-label">Categoría</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Todas las categorías</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->category_id }}" {{ request('category') == $category->category_id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('films.available') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Resultados -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-film text-primary"></i> 
                    {{ $films->count() }} película(s) disponible(s)
                </h5>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary btn-sm active" id="grid-view">
                        <i class="fas fa-th"></i> Grid
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="list-view">
                        <i class="fas fa-list"></i> Lista
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Vista Grid -->
    <div id="grid-container">
        <div class="row">
            @forelse($films as $film)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 film-card">
                        <div class="card-img-top bg-gradient-primary d-flex align-items-center justify-content-center" style="height: 200px;">
                            <div class="text-center text-white">
                                <i class="fas fa-film fa-3x mb-2"></i>
                                <h6 class="fw-bold">{{ Str::limit($film->title, 20) }}</h6>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">{{ $film->title }}</h6>
                            <p class="card-text text-muted small flex-grow-1">
                                {{ Str::limit($film->description, 80) }}
                            </p>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <small class="text-muted">Año:</small><br>
                                    <strong>{{ $film->release_year ?: 'N/A' }}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Duración:</small><br>
                                    <strong>{{ $film->length ? $film->length . ' min' : 'N/A' }}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Clasificación:</small><br>
                                    <span class="badge bg-secondary">{{ $film->rating ?: 'NR' }}</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Idioma:</small><br>
                                    <strong>{{ $film->language->name ?? 'N/A' }}</strong>
                                </div>
                            </div>

                            <div class="border-top pt-3">
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">Precio renta:</small><br>
                                        <strong class="text-success">${{ number_format($film->rental_rate, 2) }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Duración renta:</small><br>
                                        <strong>{{ $film->rental_duration }} días</strong>
                                    </div>
                                </div>

                                <div class="availability-info mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="small text-muted">Disponibilidad:</span>
                                        <span class="badge bg-success">
                                            {{ $film->available_copies }}/{{ $film->total_copies }} disponibles
                                        </span>
                                    </div>
                                    <div class="progress mt-1" style="height: 4px;">
                                        <div class="progress-bar bg-success" 
                                             style="width: {{ ($film->available_copies / $film->total_copies) * 100 }}%">
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="{{ route('rentals.create', ['film' => $film->film_id]) }}" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-shopping-cart"></i> Rentar Ahora
                                    </a>
                                    <a href="{{ route('films.show', $film) }}" 
                                       class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-info-circle"></i> Ver Detalles
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-film fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No hay películas disponibles</h4>
                        <p class="text-muted">
                            @if(request()->hasAny(['search', 'rating', 'category']))
                                No se encontraron películas que coincidan con los filtros aplicados.
                                <br>
                                <a href="{{ route('films.available') }}" class="btn btn-outline-primary mt-2">
                                    <i class="fas fa-times"></i> Limpiar filtros
                                </a>
                            @else
                                Actualmente no hay películas disponibles para rentar.
                            @endif
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Vista Lista -->
    <div id="list-container" style="display: none;">
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Título</th>
                            <th>Año</th>
                            <th>Clasificación</th>
                            <th>Duración</th>
                            <th>Precio</th>
                            <th>Días renta</th>
                            <th>Disponibles</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($films as $film)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $film->title }}</strong>
                                        @if($film->language)
                                            <br><small class="text-muted">{{ $film->language->name }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $film->release_year ?: 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $film->rating ?: 'NR' }}</span>
                                </td>
                                <td>{{ $film->length ? $film->length . ' min' : 'N/A' }}</td>
                                <td class="text-success fw-bold">${{ number_format($film->rental_rate, 2) }}</td>
                                <td>{{ $film->rental_duration }} días</td>
                                <td>
                                    <span class="badge bg-success">
                                        {{ $film->available_copies }}/{{ $film->total_copies }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('rentals.create', ['film' => $film->film_id]) }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-shopping-cart"></i> Rentar
                                        </a>
                                        <a href="{{ route('films.show', $film) }}" 
                                           class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.film-card {
    transition: transform 0.2s;
}

.film-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.bg-gradient-primary {
    background: linear-gradient(45deg, #007bff, #6610f2);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const gridView = document.getElementById('grid-view');
    const listView = document.getElementById('list-view');
    const gridContainer = document.getElementById('grid-container');
    const listContainer = document.getElementById('list-container');

    gridView.addEventListener('click', function() {
        gridView.classList.add('active');
        listView.classList.remove('active');
        gridContainer.style.display = 'block';
        listContainer.style.display = 'none';
    });

    listView.addEventListener('click', function() {
        listView.classList.add('active');
        gridView.classList.remove('active');
        gridContainer.style.display = 'none';
        listContainer.style.display = 'block';
    });
});
</script>
@endsection