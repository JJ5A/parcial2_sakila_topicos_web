@extends('layouts.app')

@section('title', 'Lista de Actores - Sakila')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">
                        <i class="fas fa-users me-2"></i>Lista de Actores
                        @if(isset($filteredCount))
                            <small class="text-muted">({{ $filteredCount }} de {{ $totalActors }})</small>
                        @endif
                    </h4>
                    <a href="{{ route('actors.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nuevo Actor
                    </a>
                </div>
                
                <!-- Formulario de búsqueda -->
                <form method="GET" action="{{ route('actors.index') }}" class="mb-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Buscar por nombre o apellido..."
                                       value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                                @if(request('search') || request('letter'))
                                    <a href="{{ route('actors.index') }}" class="btn btn-outline-danger">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="sort" class="form-select" onchange="this.form.submit()">
                                <option value="last_name" {{ request('sort') == 'last_name' ? 'selected' : '' }}>Ordenar por Apellido</option>
                                <option value="first_name" {{ request('sort') == 'first_name' ? 'selected' : '' }}>Ordenar por Nombre</option>
                                <option value="actor_id" {{ request('sort') == 'actor_id' ? 'selected' : '' }}>Ordenar por ID</option>
                                <option value="last_update" {{ request('sort') == 'last_update' ? 'selected' : '' }}>Última Actualización</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="direction" class="form-select" onchange="this.form.submit()">
                                <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                                <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Descendente</option>
                            </select>
                        </div>
                        <!-- Campos ocultos para mantener otros filtros -->
                        @if(request('letter'))
                            <input type="hidden" name="letter" value="{{ request('letter') }}">
                        @endif
                    </div>
                </form>
                
                <!-- Filtro alfabético -->
                @if(isset($letters) && count($letters) > 0)
                    <div class="mb-3">
                        <small class="text-muted">Filtrar por letra:</small>
                        <div class="btn-group flex-wrap mt-1" role="group">
                            <a href="{{ route('actors.index') }}" 
                               class="btn btn-sm {{ !request('letter') ? 'btn-primary' : 'btn-outline-primary' }}">
                                Todos
                            </a>
                            @foreach($letters as $letter)
                                <a href="{{ route('actors.index', array_merge(request()->all(), ['letter' => $letter])) }}" 
                                   class="btn btn-sm {{ request('letter') == $letter ? 'btn-primary' : 'btn-outline-primary' }}">
                                    {{ $letter }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            <div class="card-body">
                @if($actors->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>
                                        <a href="{{ route('actors.index', array_merge(request()->all(), ['sort' => 'actor_id', 'direction' => request('sort') == 'actor_id' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                                           class="text-white text-decoration-none">
                                            ID
                                            @if(request('sort') == 'actor_id')
                                                <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @else
                                                <i class="fas fa-sort ms-1 opacity-50"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('actors.index', array_merge(request()->all(), ['sort' => 'first_name', 'direction' => request('sort') == 'first_name' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                                           class="text-white text-decoration-none">
                                            Nombre
                                            @if(request('sort') == 'first_name')
                                                <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @else
                                                <i class="fas fa-sort ms-1 opacity-50"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('actors.index', array_merge(request()->all(), ['sort' => 'last_name', 'direction' => request('sort') == 'last_name' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                                           class="text-white text-decoration-none">
                                            Apellido
                                            @if(request('sort') == 'last_name' || !request('sort'))
                                                <i class="fas fa-sort-{{ request('direction', 'asc') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @else
                                                <i class="fas fa-sort ms-1 opacity-50"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>
                                        <a href="{{ route('actors.index', array_merge(request()->all(), ['sort' => 'last_update', 'direction' => request('sort') == 'last_update' && request('direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                                           class="text-white text-decoration-none">
                                            Última Actualización
                                            @if(request('sort') == 'last_update')
                                                <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @else
                                                <i class="fas fa-sort ms-1 opacity-50"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th width="200px">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($actors as $actor)
                                    <tr>
                                        <td>{{ $actor->actor_id }}</td>
                                        <td>
                                            @if(request('search'))
                                                {!! preg_replace('/(' . preg_quote(request('search'), '/') . ')/i', '<span class="search-highlight">$1</span>', e($actor->first_name)) !!}
                                            @else
                                                {{ $actor->first_name }}
                                            @endif
                                        </td>
                                        <td>
                                            @if(request('search'))
                                                {!! preg_replace('/(' . preg_quote(request('search'), '/') . ')/i', '<span class="search-highlight">$1</span>', e($actor->last_name)) !!}
                                            @else
                                                {{ $actor->last_name }}
                                            @endif
                                        </td>
                                        <td>{{ $actor->last_update ? $actor->last_update->format('d/m/Y H:i') : '-' }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('actors.show', $actor->actor_id) }}" 
                                                   class="btn btn-info btn-sm" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('actors.edit', $actor->actor_id) }}" 
                                                   class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('actors.destroy', $actor->actor_id) }}" 
                                                      method="POST" style="display: inline;"
                                                      onsubmit="return confirm('¿Estás seguro de que quieres eliminar este actor?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div class="text-muted small">
                            @if($actors->firstItem())
                                Mostrando {{ $actors->firstItem() }} - {{ $actors->lastItem() }} de {{ $actors->total() }} actores
                                @if(request('search'))
                                    <span class="text-primary">(filtrado por: "{{ request('search') }}")</span>
                                @endif
                                @if(request('letter'))
                                    <span class="text-primary">(letra: {{ request('letter') }})</span>
                                @endif
                            @else
                                @if(request('search') || request('letter'))
                                    No se encontraron actores con los filtros aplicados
                                @else
                                    No hay actores para mostrar
                                @endif
                            @endif
                        </div>
                        <div class="pagination-wrapper">
                            {{ $actors->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay actores registrados</h5>
                        <p class="text-muted">Comienza agregando tu primer actor.</p>
                        <a href="{{ route('actors.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Crear Primer Actor
                        </a>
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
    
    .pagination-wrapper .page-item:first-child .page-link,
    .pagination-wrapper .page-item:last-child .page-link {
        border-radius: 0.375rem;
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
    
    .pagination-wrapper .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #f8f9fa;
        border-color: #dee2e6;
        cursor: not-allowed;
        opacity: 0.6;
    }
    
    /* Estilo para paginación simple */
    .pagination-wrapper .pagination .page-item {
        display: inline-block;
    }
    
    .pagination-wrapper .page-link {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    /* Estilos para filtros alfabéticos */
    .btn-group .btn {
        min-width: 40px;
        margin: 2px;
    }
    
    .btn-group.flex-wrap {
        flex-wrap: wrap !important;
    }
    
    /* Estilos para encabezados ordenables */
    .table thead th a {
        transition: all 0.2s ease;
    }
    
    .table thead th a:hover {
        background-color: rgba(255, 255, 255, 0.1);
        padding: 4px 8px;
        border-radius: 4px;
    }
    
    .table thead th a i.opacity-50 {
        opacity: 0.5 !important;
        transition: opacity 0.2s ease;
    }
    
    .table thead th a:hover i.opacity-50 {
        opacity: 0.8 !important;
    }
    
    /* Estilo para resaltar resultados de búsqueda */
    .search-highlight {
        background-color: #fff3cd;
        padding: 1px 3px;
        border-radius: 3px;
        font-weight: 500;
    }
    
    /* Responsive para filtros alfabéticos */
    @media (max-width: 768px) {
        .btn-group .btn {
            min-width: 35px;
            font-size: 0.8rem;
            margin: 1px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    const searchForm = document.querySelector('form');
    let searchTimer;
    
    // Búsqueda manual - no automática para evitar problemas de sesión
    if (searchInput) {
        // Enviar form solo cuando se presiona Enter o el botón de búsqueda
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                clearTimeout(searchTimer);
                searchForm.submit();
            }
        });
    }
    
    // Mejorar UX de los filtros alfabéticos
    document.querySelectorAll('.btn-group a').forEach(btn => {
        btn.addEventListener('click', function(e) {
            // Agregar efecto visual de carga
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        });
    });
    
    // Auto-focus en el campo de búsqueda si no hay resultados
    @if(request('search') && $actors->count() === 0)
        if (searchInput) {
            searchInput.focus();
            searchInput.select();
        }
    @endif
});
</script>
@endpush

@endsection