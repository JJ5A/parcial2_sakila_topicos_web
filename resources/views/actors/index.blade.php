@extends('layouts.app')

@section('title', 'Lista de Actores - Sakila')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-users me-2"></i>Lista de Actores
                </h4>
                <a href="{{ route('actors.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Nuevo Actor
                </a>
            </div>
            <div class="card-body">
                @if($actors->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Apellido</th>
                                    <th>Última Actualización</th>
                                    <th width="200px">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($actors as $actor)
                                    <tr>
                                        <td>{{ $actor->actor_id }}</td>
                                        <td>{{ $actor->first_name }}</td>
                                        <td>{{ $actor->last_name }}</td>
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
                                Mostrando {{ $actors->firstItem() }} - {{ $actors->lastItem() }} actores
                            @else
                                No hay actores para mostrar
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
</style>
@endpush

@endsection