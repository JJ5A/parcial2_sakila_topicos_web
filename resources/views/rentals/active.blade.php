@extends('layouts.app')

@section('title', 'Rentas Activas - Sistema Sakila')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-0">
                        <i class="fas fa-play-circle text-primary me-2"></i>Rentas Activas
                    </h1>
                    <p class="text-muted">Gestión de rentas en curso</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('rentals.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                    </a>
                    <a href="{{ route('rentals.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nueva Renta
                    </a>
                    <a href="{{ route('rentals.overdue') }}" class="btn btn-danger">
                        <i class="fas fa-exclamation-triangle me-1"></i>Ver Atrasadas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de Rentas Activas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Activas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_active'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-play fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-danger shadow">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Atrasadas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['overdue_count'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-warning shadow">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Vencen Hoy</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['due_today'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-info shadow">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Vencen Mañana</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['due_tomorrow'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-plus fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Rentas Activas -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list me-2"></i>Lista de Rentas Activas
                        </h6>
                        <div class="text-muted">
                            Mostrando {{ $rentals->firstItem() ?? 0 }} - {{ $rentals->lastItem() ?? 0 }} 
                            de {{ $rentals->total() }} resultados
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($rentals->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID Renta</th>
                                        <th>Cliente</th>
                                        <th>Película</th>
                                        <th>Fecha Renta</th>
                                        <th>Fecha Límite</th>
                                        <th>Días Restantes</th>
                                        <th>Personal</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rentals as $rental)
                                        @php
                                            $dueDate = $rental->rental_date->addDays($rental->inventory->film->rental_duration);
                                            $daysRemaining = now()->diffInDays($dueDate, false);
                                            $isOverdue = $daysRemaining < 0;
                                            $isDueToday = $daysRemaining == 0;
                                            $isDueTomorrow = $daysRemaining == 1;
                                        @endphp
                                        <tr class="{{ $isOverdue ? 'table-danger' : ($isDueToday ? 'table-warning' : '') }}">
                                            <td>
                                                <strong>#{{ $rental->rental_id }}</strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $rental->customer->first_name }} {{ $rental->customer->last_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">ID: {{ $rental->customer->customer_id }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $rental->inventory->film->title }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        Duración: {{ $rental->inventory->film->rental_duration }} días
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
                                                    {{ $dueDate->format('d/m/Y') }}
                                                    <br>
                                                    <small class="text-muted">{{ $dueDate->format('H:i') }}</small>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                @if($isOverdue)
                                                    <span class="badge bg-danger">
                                                        {{ abs(round($daysRemaining)) }} día(s) atrasado
                                                    </span>
                                                @elseif($isDueToday)
                                                    <span class="badge bg-warning">
                                                        Vence hoy
                                                    </span>
                                                @elseif($isDueTomorrow)
                                                    <span class="badge bg-info">
                                                        Vence mañana
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        {{ round($daysRemaining) }} día(s)
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $rental->staff->first_name }} {{ $rental->staff->last_name }}
                                            </td>
                                            <td>
                                                @if($isOverdue)
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>Atrasada
                                                    </span>
                                                @elseif($isDueToday)
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock me-1"></i>Vence Hoy
                                                    </span>
                                                @else
                                                    <span class="badge bg-primary">
                                                        <i class="fas fa-play me-1"></i>Activa
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('rentals.show', $rental) }}" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-success"
                                                            onclick="showReturnModal({{ $rental->rental_id }}, '{{ $rental->customer->first_name }} {{ $rental->customer->last_name }}', '{{ $rental->inventory->film->title }}')"
                                                            title="Procesar devolución">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                    @if($isOverdue)
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger"
                                                                title="Contactar cliente">
                                                            <i class="fas fa-phone"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $rentals->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No hay rentas activas</h4>
                            <p class="text-muted">Todas las películas han sido devueltas.</p>
                            <a href="{{ route('rentals.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Crear Nueva Renta
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Devolución -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-undo me-2"></i>Procesar Devolución
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro de procesar la devolución de esta renta?</p>
                <div class="alert alert-info">
                    <strong>Cliente:</strong> <span id="modalCustomer"></span><br>
                    <strong>Película:</strong> <span id="modalFilm"></span><br>
                    <strong>ID Renta:</strong> #<span id="modalRentalId"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="confirmReturn()">
                    <i class="fas fa-check me-1"></i>Confirmar Devolución
                </button>
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
</style>

<script>
let currentRentalId = null;

function showReturnModal(rentalId, customer, film) {
    currentRentalId = rentalId;
    document.getElementById('modalCustomer').textContent = customer;
    document.getElementById('modalFilm').textContent = film;
    document.getElementById('modalRentalId').textContent = rentalId;
    
    const modal = new bootstrap.Modal(document.getElementById('returnModal'));
    modal.show();
}

function confirmReturn() {
    if (currentRentalId) {
        // Crear formulario para enviar POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/rentals/${currentRentalId}/return`;
        
        // Token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken.getAttribute('content');
            form.appendChild(tokenInput);
        }
        
        // Agregar campo para indicar que es devolución directa
        const quickReturnInput = document.createElement('input');
        quickReturnInput.type = 'hidden';
        quickReturnInput.name = 'quick_return';
        quickReturnInput.value = '1';
        form.appendChild(quickReturnInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection