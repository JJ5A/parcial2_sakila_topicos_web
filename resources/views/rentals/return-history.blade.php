@extends('layouts.app')

@section('title', 'Historial de Devoluciones')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-history"></i> Historial de Devoluciones</h2>
            <p class="text-muted">Registro completo de todas las devoluciones realizadas</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('rentals.return.index') }}" class="btn btn-primary">
                <i class="fas fa-undo"></i> Procesar Devoluciones
            </a>
            <a href="{{ route('rentals.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Rentas
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title">{{ number_format($stats['total_returns']) }}</h3>
                            <p class="card-text">Total Devoluciones</p>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title">{{ number_format($stats['returns_this_month']) }}</h3>
                            <p class="card-text">Este Mes</p>
                        </div>
                        <i class="fas fa-calendar-month fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title">{{ number_format($stats['late_returns']) }}</h3>
                            <p class="card-text">Devoluciones Tardías</p>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title">${{ number_format($stats['revenue_late_fees'], 2) }}</h3>
                            <p class="card-text">Multas Recaudadas</p>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros de Búsqueda</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('rentals.return.history') }}">
                <div class="row">
                    <div class="col-md-4">
                        <label for="customer_search" class="form-label">Cliente</label>
                        <input type="text" 
                               class="form-control" 
                               id="customer_search" 
                               name="customer_search" 
                               value="{{ request('customer_search') }}"
                               placeholder="Buscar por nombre o apellido...">
                    </div>
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Fecha Desde</label>
                        <input type="date" 
                               class="form-control" 
                               id="date_from" 
                               name="date_from" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Fecha Hasta</label>
                        <input type="date" 
                               class="form-control" 
                               id="date_to" 
                               name="date_to" 
                               value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('rentals.return.history') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de Devoluciones -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list"></i> Devoluciones Realizadas</h5>
            <span class="badge bg-secondary">{{ $returns->total() }} registros</span>
        </div>
        <div class="card-body p-0">
            @if($returns->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Película</th>
                                <th>Fecha Renta</th>
                                <th>Fecha Devolución</th>
                                <th>Días de Atraso</th>
                                <th>Empleado</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($returns as $rental)
                                @php
                                    $wasLate = $rental->return_date > $rental->expected_return_date;
                                    $daysLate = $wasLate ? $rental->return_date->diffInDays($rental->expected_return_date) : 0;
                                @endphp
                                <tr>
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
                                            <strong>{{ $rental->inventory->film->title ?? 'N/A' }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                Duración: {{ $rental->inventory->film->rental_duration ?? 'N/A' }} días
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
                                            {{ $rental->return_date->format('d/m/Y') }}
                                            <br>
                                            <small class="text-muted">{{ $rental->return_date->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($wasLate)
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock"></i> {{ $daysLate }} días
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> A tiempo
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            {{ $rental->staff->first_name ?? 'N/A' }} {{ $rental->staff->last_name ?? '' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle"></i> Devuelto
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('rentals.show', $rental->rental_id) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="Ver Detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($rental->payment)
                                                <span class="btn btn-sm btn-outline-success" title="Pago Registrado">
                                                    <i class="fas fa-dollar-sign"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                Mostrando {{ $returns->firstItem() }} a {{ $returns->lastItem() }} 
                                de {{ $returns->total() }} registros
                            </small>
                        </div>
                        <div>
                            {{ $returns->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No se encontraron devoluciones</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['customer_search', 'date_from', 'date_to']))
                            No hay devoluciones que coincidan con los filtros aplicados.
                        @else
                            Aún no se han procesado devoluciones en el sistema.
                        @endif
                    </p>
                    @if(request()->hasAny(['customer_search', 'date_from', 'date_to']))
                        <a href="{{ route('rentals.return.history') }}" class="btn btn-outline-primary">
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
    // Auto-submit del formulario cuando se cambian las fechas
    document.getElementById('date_from').addEventListener('change', function() {
        if(document.getElementById('date_to').value) {
            document.querySelector('form').submit();
        }
    });
    
    document.getElementById('date_to').addEventListener('change', function() {
        if(document.getElementById('date_from').value) {
            document.querySelector('form').submit();
        }
    });
</script>
@endpush
@endsection