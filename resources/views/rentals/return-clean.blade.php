@extends('layouts.app')

@section('title', 'Procesar Devoluciones')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-undo"></i> Procesar Devoluciones</h2>
            <p class="text-muted">Gestionar devoluciones de películas rentadas</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('rentals.return.history') }}" class="btn btn-outline-info">
                <i class="fas fa-history"></i> Ver Historial
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ $stats['pending_returns'] }}</h3>
                            <p class="mb-0">Pendientes de Devolución</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ $stats['overdue_returns'] }}</h3>
                            <p class="mb-0">Atrasadas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">{{ $stats['returns_today'] }}</h3>
                            <p class="mb-0">Devueltas Hoy</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="mb-0">${{ number_format($stats['total_revenue_pending'], 2) }}</h3>
                            <p class="mb-0">Ingresos Pendientes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-filter"></i> Filtros de Búsqueda</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('rentals.return.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <label for="customer_search" class="form-label">Cliente</label>
                        <input type="text" class="form-control" id="customer_search" name="customer_search" 
                               placeholder="Buscar por nombre, apellido o email..." 
                               value="{{ request('customer_search') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="film_search" class="form-label">Película</label>
                        <input type="text" class="form-control" id="film_search" name="film_search" 
                               placeholder="Buscar por título de película..." 
                               value="{{ request('film_search') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Estado</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Todas</option>
                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Atrasadas</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                        </div>
                    </div>
                </div>
                @if(request()->hasAny(['customer_search', 'film_search', 'status']))
                    <div class="row mt-2">
                        <div class="col-12">
                            <a href="{{ route('rentals.return.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-times"></i> Limpiar Filtros
                            </a>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Tabla de Rentas Pendientes -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list"></i> Rentas Pendientes de Devolución</h5>
            <span class="badge bg-secondary">{{ $activeRentals->total() }} registros</span>
        </div>
        <div class="card-body p-0">
            @if($activeRentals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th width="8%">ID</th>
                                <th width="25%">Cliente</th>
                                <th width="25%">Película</th>
                                <th width="12%">Fecha Renta</th>
                                <th width="12%">Fecha Esperada</th>
                                <th width="10%">Estado</th>
                                <th width="8%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeRentals as $rental)
                                @php
                                    $isOverdue = $rental->isOverdue();
                                    $daysOverdue = $isOverdue ? $rental->daysOverdue() : 0;
                                @endphp
                                <tr class="{{ $isOverdue ? 'table-warning' : '' }}">
                                    <td>
                                        <strong>#{{ $rental->rental_id }}</strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $rental->customer->full_name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $rental->customer->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $rental->inventory->film->title }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                Duración: {{ $rental->inventory->film->rental_duration }} días | 
                                                Costo: ${{ $rental->inventory->film->rental_rate }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $rental->rental_date->format('d/m/Y') }}
                                            <br>
                                            <small class="text-muted">{{ $rental->rental_date->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $rental->expected_return_date->format('d/m/Y') }}
                                            <br>
                                            <small class="text-muted">{{ $rental->expected_return_date->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($isOverdue)
                                            <span class="badge bg-danger">
                                                <i class="fas fa-exclamation-triangle"></i> 
                                                {{ $daysOverdue }} días
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="fas fa-clock"></i> A tiempo
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('rentals.return.show', $rental->rental_id) }}" 
                                               class="btn btn-sm btn-primary"
                                               title="Procesar Devolución">
                                                <i class="fas fa-undo"></i>
                                            </a>
                                            <a href="{{ route('rentals.show', $rental->rental_id) }}" 
                                               class="btn btn-sm btn-outline-info"
                                               title="Ver Detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="card-footer">
                    <div class="row align-items-center">
                        <div class="col-sm-12 col-md-6">
                            <div class="text-muted small">
                                Mostrando {{ $activeRentals->firstItem() }} a {{ $activeRentals->lastItem() }} 
                                de {{ $activeRentals->total() }} registros
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-6">
                            {{ $activeRentals->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5 class="text-muted">No hay rentas pendientes de devolución</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['customer_search', 'film_search', 'status']))
                            No hay rentas que coincidan con los filtros aplicados.
                        @else
                            ¡Excelente! Todas las rentas han sido devueltas.
                        @endif
                    </p>
                    @if(request()->hasAny(['customer_search', 'film_search', 'status']))
                        <a href="{{ route('rentals.return.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-times"></i> Limpiar Filtros
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit del formulario cuando se cambia el estado
    const statusSelect = document.getElementById('status');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            this.closest('form').submit();
        });
    }
});
</script>
@endpush
@endsection