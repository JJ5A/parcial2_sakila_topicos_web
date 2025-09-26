@extends('layouts.app')

@section('title', 'Lista de Películas - Sakila')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-film me-2"></i>Lista de Películas
                </h4>
                <a href="{{ route('films.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Nueva Película
                </a>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('films.index') }}" class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Buscar por título</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="search" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="Título de la película...">
                            </div>
                            <div class="col-md-3">
                                <label for="rating" class="form-label">Rating</label>
                                <select class="form-select" id="rating" name="rating">
                                    <option value="">Todos los ratings</option>
                                    @foreach($ratings as $value => $label)
                                        <option value="{{ $value }}" {{ request('rating') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="year" class="form-label">Año</label>
                                <select class="form-select" id="year" name="year">
                                    <option value="">Todos los años</option>
                                    @foreach($years as $year)
                                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-search me-1"></i>Buscar
                                </button>
                                <a href="{{ route('films.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Limpiar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                @if($films->count() > 0)
                    <div class="row">
                        @foreach($films as $film)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title text-truncate" title="{{ $film->title }}">
                                                {{ $film->title }}
                                            </h5>
                                            <span class="badge bg-secondary">{{ $film->rating ?? 'N/A' }}</span>
                                        </div>
                                        
                                        <p class="card-text small text-muted mb-2">
                                            <strong>Año:</strong> {{ $film->release_year ?? 'N/A' }} |
                                            <strong>Duración:</strong> {{ $film->formatted_length }} |
                                            <strong>Alquiler:</strong> {{ $film->formatted_rental_rate }}
                                        </p>
                                        
                                        <p class="card-text">
                                            {{ Str::limit($film->description, 100) }}
                                        </p>
                                        
                                        <div class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-language me-1"></i>
                                                {{ $film->language->name ?? 'N/A' }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="btn-group w-100" role="group">
                                            <a href="{{ route('films.show', $film->film_id) }}" 
                                               class="btn btn-outline-info btn-sm" title="Ver">
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
                                Mostrando {{ $films->firstItem() }} - {{ $films->lastItem() }} películas
                            @else
                                No hay películas para mostrar
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
    
    .card:hover {
        transform: translateY(-2px);
        transition: transform 0.2s ease-in-out;
    }
    
    .card-title {
        color: #2c3e50;
        font-weight: 600;
    }
</style>
@endpush
@endsection