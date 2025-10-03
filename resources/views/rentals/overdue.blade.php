@extends('layouts.app')

@section('title', 'Rentas Atrasadas - Sakila')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>Rentas Atrasadas
                    </h4>
                    <div>
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#filterModal">
                            <i class="fas fa-filter me-1"></i>Filtros
                        </button>
                        <a href="{{ route('rentals.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Dashboard
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Estadísticas de Rentas Atrasadas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                                <h3 class="mb-1">{{ $overdueRentals->count() }}</h3>
                                <small>Total Atrasadas</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body text-center">
                                <i class="fas fa-clock fa-2x mb-2"></i>
                                <h3 class="mb-1">{{ $overdueRentals->where('days_overdue', '<=', 7)->count() }}</h3>
                                <small>≤ 7 días</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-dark text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                <h3 class="mb-1">{{ $overdueRentals->where('days_overdue', '>', 7)->where('days_overdue', '<=', 30)->count() }}</h3>
                                <small>8-30 días</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <i class="fas fa-ban fa-2x mb-2"></i>
                                <h3 class="mb-1">{{ $overdueRentals->where('days_overdue', '>', 30)->count() }}</h3>
                                <small>> 30 días</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Rentas Atrasadas -->
                @if($overdueRentals->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'rental_id', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                           class="text-white text-decoration-none">
                                            ID Renta
                                            @if(request('sort') === 'rental_id')
                                                <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Cliente</th>
                                    <th>Película</th>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'rental_date', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                           class="text-white text-decoration-none">
                                            Fecha Renta
                                            @if(request('sort') === 'rental_date')
                                                <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Debía Devolver</th>
                                    <th>
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'days_overdue', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc']) }}" 
                                           class="text-white text-decoration-none">
                                            Días Atraso
                                            @if(request('sort') === 'days_overdue')
                                                <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th>Empleado</th>
                                    <th>Tienda</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($overdueRentals as $rental)
                                    <tr class="{{ $rental->days_overdue > 30 ? 'table-danger' : ($rental->days_overdue > 7 ? 'table-warning' : '') }}">
                                        <td><strong>{{ $rental->rental_id }}</strong></td>
                                        <td>
                                            <div>{{ $rental->customer ? $rental->customer->formatted_name : 'Cliente no encontrado' }}</div>
                                            <small class="text-muted">{{ $rental->customer ? $rental->customer->email : 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $rental->inventory->film->title }}</div>
                                            <small class="text-muted">{{ $rental->inventory->film->categories->first()->name ?? 'N/A' }}</small>
                                        </td>
                                        <td>{{ $rental->rental_date->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <span class="text-danger">
                                                {{ $rental->expected_return_date->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $rental->days_overdue > 30 ? 'bg-danger' : ($rental->days_overdue > 7 ? 'bg-warning text-dark' : 'bg-secondary') }}">
                                                {{ $rental->days_overdue }} día(s)
                                            </span>
                                        </td>
                                        <td>{{ $rental->staff->full_name }}</td>
                                        <td>{{ $rental->inventory->store_id }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" 
                                                        class="btn btn-outline-info contact-btn" 
                                                        data-customer-id="{{ $rental->customer->customer_id }}"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#contactModal"
                                                        title="Contactar Cliente">
                                                    <i class="fas fa-phone"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($overdueRentals->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $overdueRentals->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h4 class="text-muted">¡Excelente!</h4>
                        <p class="text-muted">No hay rentas atrasadas en este momento.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de Filtros -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="GET" action="{{ route('rentals.overdue') }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">
                        <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="days_min" class="form-label">Días mínimos de atraso</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="days_min" 
                                       name="days_min" 
                                       value="{{ request('days_min') }}"
                                       min="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="days_max" class="form-label">Días máximos de atraso</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="days_max" 
                                       name="days_max" 
                                       value="{{ request('days_max') }}"
                                       min="1">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="customer_search" class="form-label">Buscar Cliente</label>
                        <input type="text" 
                               class="form-control" 
                               id="customer_search" 
                               name="customer" 
                               value="{{ request('customer') }}"
                               placeholder="Nombre o email del cliente">
                    </div>

                    <div class="mb-3">
                        <label for="film_search_filter" class="form-label">Buscar Película</label>
                        <input type="text" 
                               class="form-control" 
                               id="film_search_filter" 
                               name="film" 
                               value="{{ request('film') }}"
                               placeholder="Título de la película">
                    </div>

                    <div class="mb-3">
                        <label for="store_filter" class="form-label">Tienda</label>
                        <select class="form-select" id="store_filter" name="store_id">
                            <option value="">Todas las tiendas</option>
                            <option value="1" {{ request('store_id') == '1' ? 'selected' : '' }}>Tienda 1</option>
                            <option value="2" {{ request('store_id') == '2' ? 'selected' : '' }}>Tienda 2</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('rentals.overdue') }}" class="btn btn-secondary">Limpiar Filtros</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>Aplicar Filtros
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Contacto -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactModalLabel">
                    <i class="fas fa-phone me-2"></i>Información de Contacto
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="contact_details">
                <!-- Los detalles se cargarán aquí via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

    </div>
</div>

@push('scripts'>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar clicks en botones de contacto
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('contact-btn') || e.target.closest('.contact-btn')) {
            const button = e.target.classList.contains('contact-btn') ? e.target : e.target.closest('.contact-btn');
            const customerId = button.getAttribute('data-customer-id');
            loadContactDetails(customerId);
        }
    });

    function loadContactDetails(customerId) {
        const contactDetails = document.getElementById('contact_details');
        
        // Aquí iría una llamada AJAX para obtener los detalles del cliente
        contactDetails.innerHTML = `
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>
        `;
        
        // Simular datos del cliente (en una implementación real, esto vendría del servidor)
        setTimeout(() => {
            contactDetails.innerHTML = `
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Información del Cliente</h6>
                        <p><strong>ID Cliente:</strong> ${customerId}</p>
                        <p><strong>Teléfono:</strong> <a href="tel:+1234567890">+1 (234) 567-890</a></p>
                        <p><strong>Email:</strong> <a href="mailto:cliente@email.com">cliente@email.com</a></p>
                        <p><strong>Dirección:</strong> Calle Principal #123, Ciudad</p>
                    </div>
                </div>
            `;
        }, 500);
    }
});
</script>
@endpush
@endsection