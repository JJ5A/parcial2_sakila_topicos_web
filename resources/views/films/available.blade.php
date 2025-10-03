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
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
                        @if(isset($totalAvailable))
                            <small class="text-muted">({{ $totalAvailable }} de {{ $totalFilms }} películas disponibles)</small>
                        @endif
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('films.available') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Buscar película</label>
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}" 
                                       placeholder="Título de la película...">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="rating" class="form-label">Clasificación</label>
                            <select class="form-select" id="rating" name="rating" onchange="this.form.submit()">
                                <option value="">Todas</option>
                                @foreach($ratings as $value => $label)
                                    <option value="{{ $value }}" {{ request('rating') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="category" class="form-label">Categoría</label>
                            <select class="form-select" id="category" name="category" onchange="this.form.submit()">
                                <option value="">Todas</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->category_id }}" {{ request('category') == $category->category_id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="sort" class="form-label">Ordenar por</label>
                            <select class="form-select" id="sort" name="sort" onchange="this.form.submit()">
                                <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Título</option>
                                <option value="release_year" {{ request('sort') == 'release_year' ? 'selected' : '' }}>Año</option>
                                <option value="rental_rate" {{ request('sort') == 'rental_rate' ? 'selected' : '' }}>Precio</option>
                                <option value="length" {{ request('sort') == 'length' ? 'selected' : '' }}>Duración</option>
                                <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Rating</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="direction" class="form-label">Orden</label>
                            <select class="form-select" id="direction" name="direction" onchange="this.form.submit()">
                                <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                                <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descendente</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            @if(request()->hasAny(['search', 'rating', 'category', 'sort', 'direction']))
                                <a href="{{ route('films.available') }}" class="btn btn-outline-danger" title="Limpiar filtros">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                        <!-- Campos ocultos para mantener parámetros -->
                        @foreach(['search', 'rating', 'category'] as $field)
                            @if(request($field))
                                <input type="hidden" name="{{ $field }}" value="{{ request($field) }}">
                            @endif
                        @endforeach
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
                    {{ $films->total() }} película(s) disponible(s)
                    @if(request()->hasAny(['search', 'rating', 'category']))
                        <small class="text-muted">
                            (filtrado
                            @if(request('search')) por "{{ request('search') }}" @endif
                            @if(request('rating')) - rating: {{ $ratings[request('rating')] ?? request('rating') }} @endif
                            @if(request('category')) - categoría @endif
                            )
                        </small>
                    @endif
                    @if($films->hasPages())
                        <small class="text-muted">| Página {{ $films->currentPage() }} de {{ $films->lastPage() }}</small>
                    @endif
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
                            <h6 class="card-title">
                                @if(request('search'))
                                    {!! preg_replace('/(' . preg_quote(request('search'), '/') . ')/i', '<span class="search-highlight">$1</span>', e($film->title)) !!}
                                @else
                                    {{ $film->title }}
                                @endif
                            </h6>
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
        
        <!-- Navegación de Películas -->
        @if($films->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Mostrando {{ $films->firstItem() ?? 0 }} - {{ $films->lastItem() ?? 0 }} de {{ $films->total() }} películas disponibles
                    @if(request('search'))
                        <br><small class="text-primary">Filtrado por: "{{ request('search') }}"</small>
                    @endif
                    @if(request('rating'))
                        <br><small class="text-primary">Rating: {{ $ratings[request('rating')] ?? request('rating') }}</small>
                    @endif
                    @if(request('category'))
                        @php
                            $categoryName = $categories->where('category_id', request('category'))->first()->name ?? 'Desconocida';
                        @endphp
                        <br><small class="text-primary">Categoría: {{ $categoryName }}</small>
                    @endif
                    <br><small class="text-muted">Página {{ $films->currentPage() }} de {{ $films->lastPage() }}</small>
                </div>
                <div class="navigation-controls">
                    <div class="d-flex align-items-center gap-3">
                        <!-- Botón Anterior -->
                        @if($films->onFirstPage())
                            <button class="btn btn-outline-secondary" disabled>
                                <i class="fas fa-chevron-left me-1"></i>Anterior
                            </button>
                        @else
                            <a href="{{ $films->previousPageUrl() }}" class="btn btn-outline-primary">
                                <i class="fas fa-chevron-left me-1"></i>Anterior
                            </a>
                        @endif
                        
                        <!-- Información de página -->
                        <div class="page-info bg-light px-3 py-1 rounded">
                            <small class="text-muted">{{ $films->currentPage() }} / {{ $films->lastPage() }}</small>
                        </div>
                        
                        <!-- Botón Siguiente -->
                        @if($films->hasMorePages())
                            <a href="{{ $films->nextPageUrl() }}" class="btn btn-primary">
                                Siguiente<i class="fas fa-chevron-right ms-1"></i>
                            </a>
                        @else
                            <button class="btn btn-outline-secondary" disabled>
                                Siguiente<i class="fas fa-chevron-right ms-1"></i>
                            </button>
                        @endif
                    </div>
                    
                    <!-- Paginación completa más pequeña -->
                    <div class="mt-2">
                        {{ $films->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        @endif
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
                                        <strong>
                                            @if(request('search'))
                                                {!! preg_replace('/(' . preg_quote(request('search'), '/') . ')/i', '<span class="search-highlight">$1</span>', e($film->title)) !!}
                                            @else
                                                {{ $film->title }}
                                            @endif
                                        </strong>
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
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.film-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.bg-gradient-primary {
    background: linear-gradient(45deg, #007bff, #6610f2);
}

/* Estilo para resaltar resultados de búsqueda */
.search-highlight {
    background-color: #fff3cd;
    padding: 1px 3px;
    border-radius: 3px;
    font-weight: 500;
}

/* Estilos de paginación */
.pagination-wrapper .pagination {
    margin: 0;
    font-size: 0.875rem;
}

.pagination-wrapper .page-link {
    padding: 0.375rem 0.75rem;
    font-size: 0.85rem;
    border-radius: 0.375rem;
    text-align: center;
    line-height: 1.5;
    font-weight: 500;
    border: 1px solid #dee2e6;
    margin: 0 0.125rem;
}

.pagination-wrapper .page-link:hover {
    background-color: #e3f2fd;
    border-color: #90caf9;
    color: #1976d2;
    text-decoration: none;
}

.pagination-wrapper .page-item.active .page-link {
    background-color: #1976d2;
    border-color: #1976d2;
    color: white;
    font-weight: 600;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .col-xl-3 {
        margin-bottom: 1rem;
    }
    
    .navigation-controls {
        flex-direction: column;
        gap: 1rem;
    }
    
    .navigation-controls .d-flex {
        justify-content: center;
    }
}

/* Mejoras visuales para filtros */
.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.btn-group .btn.active {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const gridView = document.getElementById('grid-view');
    const listView = document.getElementById('list-view');
    const gridContainer = document.getElementById('grid-container');
    const listContainer = document.getElementById('list-container');
    const searchInput = document.querySelector('input[name="search"]');
    const searchForm = document.querySelector('form');

    // Toggle between grid and list view
    gridView.addEventListener('click', function() {
        gridView.classList.add('active');
        listView.classList.remove('active');
        gridContainer.style.display = 'block';
        listContainer.style.display = 'none';
        localStorage.setItem('filmViewMode', 'grid');
    });

    listView.addEventListener('click', function() {
        listView.classList.add('active');
        gridView.classList.remove('active');
        gridContainer.style.display = 'none';
        listContainer.style.display = 'block';
        localStorage.setItem('filmViewMode', 'list');
    });

    // Restore view mode from localStorage
    const savedViewMode = localStorage.getItem('filmViewMode');
    if (savedViewMode === 'list') {
        listView.click();
    }

    // Manual search - submit only on Enter
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchForm.submit();
            }
        });
    }

    // Auto-focus on search field if no results
    @if(request('search') && $films->count() === 0)
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    @endif

    // Smooth scroll to top when changing pages
    const pageLinks = document.querySelectorAll('.pagination a, .btn[href*="page="]');
    pageLinks.forEach(link => {
        link.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
});
</script>
@endsection