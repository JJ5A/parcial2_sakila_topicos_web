@extends('layouts.app')

@section('title', 'Lista de Películas - Sakila')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">
                        <i class="fas fa-film me-2"></i>Lista de Películas
                        @if(isset($films))
                            <small class="text-muted">({{ $films->total() }} películas)</small>
                        @endif
                    </h4>
                    <a href="{{ route('films.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nueva Película
                    </a>
                </div>
                
                <!-- Formulario de búsqueda y filtros -->
                <form method="GET" action="{{ route('films.index') }}" class="mb-3">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Buscar por título..."
                                       value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request()->hasAny(['search', 'rating', 'year']))
                                    <a href="{{ route('films.index') }}" class="btn btn-outline-danger">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="rating" class="form-select" onchange="this.form.submit()">
                                <option value="">Todos los ratings</option>
                                @if(isset($ratings))
                                    @foreach($ratings as $value => $label)
                                        <option value="{{ $value }}" {{ request('rating') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="year" class="form-select" onchange="this.form.submit()">
                                <option value="">Todos los años</option>
                                @if(isset($years))
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="sort" class="form-select" onchange="this.form.submit()">
                                <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Por Título</option>
                                <option value="release_year" {{ request('sort') == 'release_year' ? 'selected' : '' }}>Por Año</option>
                                <option value="rental_rate" {{ request('sort') == 'rental_rate' ? 'selected' : '' }}>Por Precio</option>
                                <option value="length" {{ request('sort') == 'length' ? 'selected' : '' }}>Por Duración</option>
                            </select>
                        </div>
                        <!-- Campos ocultos para mantener filtros -->
                        @foreach(['search', 'rating', 'year'] as $field)
                            @if(request($field) && $field != 'sort')
                                <input type="hidden" name="{{ $field }}" value="{{ request($field) }}">
                            @endif
                        @endforeach
                    </div>
                </form>
            </div>
            
            <div class="card-body">
                @if(isset($films) && $films->count() > 0)
                    <div class="row">
                        @foreach($films as $film)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 shadow-sm film-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title text-truncate" title="{{ $film->title }}">
                                                @if(request('search'))
                                                    {!! preg_replace('/(' . preg_quote(request('search'), '/') . ')/i', '<span class="search-highlight">$1</span>', e($film->title)) !!}
                                                @else
                                                    {{ $film->title }}
                                                @endif
                                            </h5>
                                            <span class="badge bg-secondary">{{ $film->rating ?? 'N/A' }}</span>
                                        </div>
                                        
                                        <div class="film-details mb-2">
                                            <small class="text-muted d-block">
                                                <i class="fas fa-calendar me-1"></i>
                                                <strong>Año:</strong> {{ $film->release_year ?? 'N/A' }}
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-clock me-1"></i>
                                                <strong>Duración:</strong> {{ $film->length ? $film->length . ' min' : 'N/A' }}
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-dollar-sign me-1"></i>
                                                <strong>Alquiler:</strong> ${{ number_format($film->rental_rate, 2) }}
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-language me-1"></i>
                                                <strong>Idioma:</strong> {{ $film->language->name ?? 'N/A' }}
                                            </small>
                                        </div>
                                        
                                        <p class="card-text small">
                                            {{ Str::limit($film->description, 100, '...') }}
                                        </p>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="btn-group w-100" role="group">
                                            <a href="{{ route('films.show', $film->film_id) }}" 
                                               class="btn btn-outline-info btn-sm" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('films.edit', $film->film_id) }}" 
                                               class="btn btn-outline-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('films.destroy', $film->film_id) }}" 
                                                  method="POST" 
                                                  style="display: inline;"
                                                  onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta película?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Paginación -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted small">
                            @if($films->firstItem())
                                Mostrando {{ $films->firstItem() }} - {{ $films->lastItem() }} de {{ $films->total() }} películas
                                @if(request('search'))
                                    <span class="text-primary">(filtrado por: "{{ request('search') }}")</span>
                                @endif
                                @if(request('rating'))
                                    <span class="text-primary">(rating: {{ $ratings[request('rating')] ?? request('rating') }})</span>
                                @endif
                                @if(request('year'))
                                    <span class="text-primary">(año: {{ request('year') }})</span>
                                @endif
                            @else
                                @if(request()->hasAny(['search', 'rating', 'year']))
                                    No se encontraron películas con los filtros aplicados
                                @else
                                    No hay películas para mostrar
                                @endif
                            @endif
                        </div>
                        <div class="pagination-wrapper">
                            {{ $films->withQueryString()->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-film fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay películas registradas</h5>
                        <p class="text-muted">
                            @if(request()->hasAny(['search', 'rating', 'year']))
                                No se encontraron películas con los filtros seleccionados.
                            @else
                                Comienza agregando tu primera película.
                            @endif
                        </p>
                        <div>
                            @if(request()->hasAny(['search', 'rating', 'year']))
                                <a href="{{ route('films.index') }}" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-times me-1"></i>Limpiar Filtros
                                </a>
                            @endif
                            <a href="{{ route('films.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Crear Primera Película
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
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
    
    .film-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    
    .film-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }
    
    .card-title {
        color: #2c3e50;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .film-details {
        line-height: 1.6;
    }
    
    .film-details small {
        margin-bottom: 0.2rem;
    }
    
    /* Estilo para resaltar resultados de búsqueda */
    .search-highlight {
        background-color: #fff3cd;
        padding: 1px 3px;
        border-radius: 3px;
        font-weight: 500;
    }
    
    .badge {
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .col-lg-4 {
            margin-bottom: 1rem;
        }
        
        .film-details small {
            font-size: 0.75rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    const searchForm = document.querySelector('form');
    
    // Búsqueda manual - enviar solo al presionar Enter
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchForm.submit();
            }
        });
    }
    
    // Auto-focus en el campo de búsqueda si no hay resultados
    @if(request('search') && isset($films) && $films->count() === 0)
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    @endif
});
</script>
@endpush

@endsection