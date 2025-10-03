@extends('layouts.app')

@section('title', 'Dashboard - Sistema de Rentas Sakila')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-0">
                        <i class="fas fa-tachometer-alt text-primary me-2"></i>Dashboard - Sistema de Rentas
                    </h1>
                    <p class="text-muted">Resumen completo del estado actual</p>
                </div>
                <div class="btn-group">
                                        <a href="{{ route('rentals.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nueva Renta
                    </a>
                    <a href="{{ route('films.available') }}" class="btn btn-info">
                        <i class="fas fa-film me-1"></i>Ver Disponibles
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Principales -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Rentas Activas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_rentals'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-play fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-danger shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Atrasadas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['overdue_rentals'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Disponibles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['available_inventory'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Clientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_customers'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Hoy</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_rentals_today'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card border-left-secondary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Ingresos Hoy</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($stats['revenue_today'] ?? 0, 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="row">
        <!-- Estado del Inventario -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-film me-2"></i>Estado del Inventario por Película
                    </h6>
                    <a href="{{ route('films.available') }}" class="btn btn-sm btn-primary">Ver Disponibles</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Película</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Rentadas</th>
                                    <th class="text-center">Disponibles</th>
                                    <th class="text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($inventory_status))
                                    @forelse($inventory_status as $item)
                                        <tr>
                                            <td>
                                                <strong>{{ $item->title }}</strong>
                                            </td>
                                            <td class="text-center">{{ $item->total_copies }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-danger">{{ $item->rented_copies }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success">{{ $item->available_copies }}</span>
                                            </td>
                                            <td class="text-center">
                                                <div class="progress" style="height: 20px;">
                                                    @php
                                                        $percentage = $item->total_copies > 0 ? ($item->available_copies / $item->total_copies) * 100 : 0;
                                                    @endphp
                                                    <div class="progress-bar bg-success" 
                                                         style="width: {{ $percentage }}%"
                                                         title="{{ $item->available_copies }}/{{ $item->total_copies }} disponibles">
                                                        {{ round($percentage) }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No hay inventario registrado</td>
                                        </tr>
                                    @endforelse
                                @else
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Datos no disponibles</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información Adicional -->
        <div class="col-xl-4 col-lg-5">
            <!-- Películas Populares -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-trophy me-2"></i>Películas Más Populares
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($popular_films))
                        @forelse($popular_films as $film)
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $film->title }}</h6>
                                    <small class="text-muted">{{ $film->rental_count }} rentas</small>
                                </div>
                                <span class="badge bg-primary fs-6">{{ $film->rental_count }}</span>
                            </div>
                            @if(!$loop->last)
                                <hr class="my-2">
                            @endif
                        @empty
                            <p class="text-muted text-center">No hay datos este mes</p>
                        @endforelse
                    @else
                        <p class="text-muted text-center">Datos no disponibles</p>
                    @endif
                </div>
            </div>

            <!-- Clientes Activos -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-star me-2"></i>Clientes Más Activos
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($top_customers))
                        @forelse($top_customers as $customer)
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $customer->first_name }} {{ $customer->last_name }}</h6>
                                    <small class="text-muted">{{ $customer->rental_count }} rentas</small>
                                </div>
                                <span class="badge bg-info fs-6">{{ $customer->rental_count }}</span>
                            </div>
                            @if(!$loop->last)
                                <hr class="my-2">
                            @endif
                        @empty
                            <p class="text-muted text-center">No hay datos este mes</p>
                        @endforelse
                    @else
                        <p class="text-muted text-center">Datos no disponibles</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Rentas Recientes -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-clock me-2"></i>Rentas Recientes
                    </h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Acciones
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('rentals.create') }}">Nueva Renta</a></li>
                            <li><a class="dropdown-item" href="{{ route('rentals.active') }}">Ver Todas</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('rentals.overdue') }}">Ver Atrasadas</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Película</th>
                                    <th>Fecha Renta</th>
                                    <th>Personal</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($recent_rentals))
                                    @forelse($recent_rentals as $rental)
                                        @if($rental->customer && $rental->inventory && $rental->inventory->film && $rental->staff)
                                        <tr>
                                            <td>
                                                <strong>#{{ $rental->rental_id }}</strong>
                                            </td>
                                            <td>{{ $rental->customer->first_name }} {{ $rental->customer->last_name }}</td>
                                            <td>
                                                <strong>{{ $rental->inventory->film->title }}</strong>
                                            </td>
                                            <td>{{ $rental->rental_date->format('d/m/Y H:i') }}</td>
                                            <td>{{ $rental->staff->first_name }} {{ $rental->staff->last_name }}</td>
                                            <td>
                                                @if($rental->isActive())
                                                    @if($rental->isOverdue())
                                                        <span class="badge bg-danger">Atrasada</span>
                                                    @else
                                                        <span class="badge bg-primary">Activa</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-success">Devuelta</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('rentals.show', $rental) }}" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($rental->isActive())
                                                        <span class="badge bg-warning">Activa</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">No hay rentas registradas</td>
                                        </tr>
                                    @endforelse
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Datos no disponibles</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 4px solid #4e73df !important;
}
.border-left-success {
    border-left: 4px solid #1cc88a !important;
}
.border-left-info {
    border-left: 4px solid #36b9cc !important;
}
.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}
.border-left-danger {
    border-left: 4px solid #e74a3b !important;
}
.border-left-secondary {
    border-left: 4px solid #858796 !important;
}
</style>

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function() {
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
});
</script>
@endsection
