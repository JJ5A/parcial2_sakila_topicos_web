@extends('layouts.app')

@section('title', 'Detalles del Cliente - Sakila')

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Información del Cliente -->
        <div class="card mb-4">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-user me-2"></i>{{ $customer->full_name }}
                        @if($customer->active)
                            <span class="badge bg-success ms-2">Activo</span>
                        @else
                            <span class="badge bg-danger ms-2">Inactivo</span>
                        @endif
                    </h4>
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Volver a Lista
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-id-card me-2"></i>Información Personal</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>ID Cliente:</strong></td>
                                <td>{{ $customer->customer_id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nombre:</strong></td>
                                <td>{{ $customer->first_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Apellido:</strong></td>
                                <td>{{ $customer->last_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td>
                                    <a href="mailto:{{ $customer->email }}">{{ $customer->email }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Estado:</strong></td>
                                <td>
                                    @if($customer->active)
                                        <span class="text-success"><i class="fas fa-check-circle"></i> Activo</span>
                                    @else
                                        <span class="text-danger"><i class="fas fa-times-circle"></i> Inactivo</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tienda:</strong></td>
                                <td>Tienda {{ $customer->store_id }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-map-marker-alt me-2"></i>Información de Contacto</h6>
                        @if($customer->address)
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Dirección:</strong></td>
                                    <td>{{ $customer->address->address }}</td>
                                </tr>
                                @if($customer->address->address2)
                                    <tr>
                                        <td><strong>Dirección 2:</strong></td>
                                        <td>{{ $customer->address->address2 }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td><strong>Distrito:</strong></td>
                                    <td>{{ $customer->address->district }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Ciudad:</strong></td>
                                    <td>{{ $customer->address->city->city ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>País:</strong></td>
                                    <td>{{ $customer->address->city->country->country ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Código Postal:</strong></td>
                                    <td>{{ $customer->address->postal_code ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Teléfono:</strong></td>
                                    <td>
                                        @if($customer->address->phone)
                                            <a href="tel:{{ $customer->address->phone }}">{{ $customer->address->phone }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        @else
                            <p class="text-muted">No hay información de dirección disponible.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas del Cliente -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-film fa-2x mb-2"></i>
                        <h3 class="mb-1">{{ $customerStats['total_rentals'] }}</h3>
                        <small>Total Rentas</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-play fa-2x mb-2"></i>
                        <h3 class="mb-1">{{ $customerStats['active_rentals'] }}</h3>
                        <small>Rentas Activas</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                        <h3 class="mb-1">{{ $customerStats['overdue_rentals'] }}</h3>
                        <small>Rentas Atrasadas</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                        <h3 class="mb-1">${{ number_format($customerStats['total_payments'], 2) }}</h3>
                        <small>Total Pagado</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Historial de Rentas -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>Historial de Rentas
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($rentals->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Película</th>
                                            <th>Fecha Renta</th>
                                            <th>Fecha Devolución</th>
                                            <th>Estado</th>
                                            <th>Empleado</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rentals as $rental)
                                            <tr>
                                                <td>{{ $rental->rental_id }}</td>
                                                <td>
                                                    @if($rental->inventory && $rental->inventory->film)
                                                        <strong>{{ $rental->inventory->film->title }}</strong><br>
                                                        <small class="text-muted">{{ $rental->inventory->film->rating ?? 'N/A' }}</small>
                                                    @else
                                                        <span class="text-muted">Película no disponible</span>
                                                    @endif
                                                </td>
                                                <td>{{ $rental->rental_date->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    @if($rental->return_date)
                                                        {{ $rental->return_date->format('d/m/Y H:i') }}
                                                    @else
                                                        <span class="text-muted">Sin devolver</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($rental->return_date)
                                                        <span class="badge bg-success">Devuelta</span>
                                                    @else
                                                        @if($rental->isOverdue())
                                                            <span class="badge bg-danger">Atrasada</span>
                                                        @else
                                                            <span class="badge bg-warning">Activa</span>
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>{{ $rental->staff->full_name ?? 'N/A' }}</td>
                                                <td>
                                                    @if($rental->payment)
                                                        ${{ number_format($rental->payment->amount, 2) }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginación -->
                            @if($rentals->hasPages())
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $rentals->links() }}
                                </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-film fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Sin Historial de Rentas</h5>
                                <p class="text-muted">Este cliente aún no ha realizado ninguna renta.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Películas Favoritas -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-star me-2"></i>Películas Más Rentadas
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($favoriteFilms->count() > 0)
                            @foreach($favoriteFilms as $film)
                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                    <div>
                                        <strong>{{ Str::limit($film->title, 20) }}</strong><br>
                                        <small class="text-muted">ID: {{ $film->film_id }}</small>
                                    </div>
                                    <span class="badge bg-primary">{{ $film->rental_count }}x</span>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">No hay películas rentadas aún.</p>
                        @endif
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>Información Adicional
                        </h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Última Renta:</strong></td>
                                <td>
                                    @if($customerStats['last_rental'])
                                        {{ $customerStats['last_rental']->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">Nunca</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Cliente Desde:</strong></td>
                                <td>
                                    @if($customer->create_date && is_object($customer->create_date) && method_exists($customer->create_date, 'format'))
                                        {{ $customer->create_date->format('d/m/Y') }}
                                    @else
                                        {{ $customer->create_date ?? 'N/A' }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Última Actualización:</strong></td>
                                <td>
                                    @if($customer->last_update && is_object($customer->last_update) && method_exists($customer->last_update, 'format'))
                                        {{ $customer->last_update->format('d/m/Y H:i') }}
                                    @else
                                        {{ $customer->last_update ?? 'N/A' }}
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.card {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border: none;
}

.badge {
    font-size: 0.75rem;
}

.table td {
    vertical-align: middle;
}

.border-rounded {
    border-radius: 0.375rem !important;
}

/* Estadísticas cards */
.card.bg-primary,
.card.bg-success,
.card.bg-danger,
.card.bg-info {
    border: none;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.card.bg-primary .card-body,
.card.bg-success .card-body,
.card.bg-danger .card-body,
.card.bg-info .card-body {
    transition: transform 0.2s ease;
}

.card.bg-primary:hover .card-body,
.card.bg-success:hover .card-body,
.card.bg-danger:hover .card-body,
.card.bg-info:hover .card-body {
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }
    
    .card-body h3 {
        font-size: 1.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tooltip para elementos truncados
    const titleElements = document.querySelectorAll('[title]');
    titleElements.forEach(el => {
        new bootstrap.Tooltip(el);
    });
});
</script>
@endpush

@endsection