@extends('layouts.app')

@section('title', 'Procesar Devoluciones')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h2><i class="fas fa-undo"></i> Procesar Devoluciones</h2>
            <p class="text-muted">Gestionar devoluciones de películas rentadas</p>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('rentals.return.history') }}" class="btn btn-outline-info">
                <i class="fas fa-history"></i> Ver Historial
            </a>
            <a href="{{ route('rentals.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title">{{ number_format($stats['pending_returns']) }}</h3>
                            <p class="card-text">Pendientes de Devolución</p>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title">{{ number_format($stats['overdue_returns']) }}</h3>
                            <p class="card-text">Atrasadas</p>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title">{{ number_format($stats['returns_today']) }}</h3>
                            <p class="card-text">Devueltas Hoy</p>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h3 class="card-title">${{ number_format($stats['total_revenue_pending'], 2) }}</h3>
                            <p class="card-text">Ingresos Pendientes</p>
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
            <form method="GET" action="{{ route('rentals.return.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <label for="customer_search" class="form-label">Cliente</label>
                        <input type="text" 
                               class="form-control" 
                               id="customer_search" 
                               name="customer_search" 
                               value="{{ request('customer_search') }}"
                               placeholder="Buscar por nombre, apellido o email...">
                    </div>
                    <div class="col-md-4">
                        <label for="film_search" class="form-label">Película</label>
                        <input type="text" 
                               class="form-control" 
                               id="film_search" 
                               name="film_search" 
                               value="{{ request('film_search') }}"
                               placeholder="Buscar por título de película...">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Estado</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Todas</option>
                            <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Atrasadas</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="{{ route('rentals.return.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
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
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Película</th>
                                <th>Fecha Renta</th>
                                <th>Fecha Esperada</th>
                                <th>Estado</th>
                                <th>Empleado</th>
                                <th>Acciones</th>
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
                                            <strong>{{ $rental->customer ? $rental->customer->full_name : 'Cliente no encontrado' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $rental->customer ? $rental->customer->email : 'N/A' }}</small>
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
                                            {{ $rental->expected_return_date->format('d/m/Y') }}
                                            <br>
                                            <small class="text-muted">{{ $rental->expected_return_date->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($isOverdue)
                                            <span class="badge bg-danger">
                                                <i class="fas fa-exclamation-triangle"></i> 
                                                Atrasada {{ $daysOverdue }} días
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="fas fa-clock"></i> A tiempo
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            {{ $rental->staff->first_name ?? 'N/A' }} {{ $rental->staff->last_name ?? '' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('rentals.return.show', $rental->rental_id) }}" 
                                               class="btn btn-sm btn-primary"
                                               title="Procesar Devolución">
                                                <i class="fas fa-undo"></i> Devolver
                                            </a>
                                            <a href="{{ route('rentals.show', $rental->rental_id) }}" 
                                               class="btn btn-sm btn-outline-secondary"
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
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                Mostrando {{ $activeRentals->firstItem() }} a {{ $activeRentals->lastItem() }} 
                                de {{ $activeRentals->total() }} registros
                            </small>
                        </div>
                        <div>
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
    // Auto-submit del formulario cuando se cambia el estado
    document.getElementById('status').addEventListener('change', function() {
        document.querySelector('form').submit();
    });
</script>
@endpush
@endsection
                                </tr>
                            </thead>
                            <tbody id="rentals_tbody">
                                @if($activeRentals->count() > 0)
                                    @foreach($activeRentals as $rental)
                                        <tr class="rental-row" data-rental-id="{{ $rental->rental_id }}">
                                            <td><strong>{{ $rental->rental_id }}</strong></td>
                                            <td>{{ $rental->customer ? $rental->customer->formatted_name : 'Cliente no encontrado' }}</td>
                                            <td>{{ $rental->inventory && $rental->inventory->film ? $rental->inventory->film->title : 'Película no encontrada' }}</td>
                                            <td>{{ $rental->rental_date->format('d/m/Y H:i') }}</td>
                                            <td>{{ $rental->expected_return_date ? $rental->expected_return_date->format('d/m/Y') : 'N/A' }}</td>
                                            <td>
                                                @if($rental->isOverdue())
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                                        Atrasado {{ $rental->daysOverdue() }} días
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>A tiempo
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" 
                                                        class="btn btn-primary btn-sm return-btn" 
                                                        data-rental-id="{{ $rental->rental_id }}"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#returnModal">
                                                    <i class="fas fa-undo me-1"></i>Devolver
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            No hay rentas activas en el sistema
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    @if($activeRentals->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $activeRentals->links() }}
                        </div>
                    @endif
                </div>

                <div class="d-flex justify-content-start mt-4">
                    <a href="{{ route('rentals.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Devolución -->
<div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="returnForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="returnModalLabel">
                        <i class="fas fa-undo me-2"></i>Confirmar Devolución
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="return_details">
                        <!-- Los detalles se cargarán aquí via AJAX -->
                    </div>
                    
                    <div class="mb-3">
                        <label for="staff_id_return" class="form-label">Empleado que Procesa *</label>
                        <select class="form-select" id="staff_id_return" name="staff_id" required>
                            <option value="">Seleccionar empleado...</option>
                            @foreach($staff as $employee)
                                <option value="{{ $employee->staff_id }}">
                                    {{ $employee->full_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="damaged_return" name="damaged">
                        <label class="form-check-label" for="damaged_return">
                            La película fue devuelta con daños
                        </label>
                    </div>

                    <div class="mt-3" id="damage_fee_info" style="display: none;">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Se aplicará una tarifa adicional por daños.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-1"></i>Procesar Devolución
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rentalSearch = document.getElementById('rental_search');
    const clearSearch = document.getElementById('clear_search');
    const rentalsTable = document.getElementById('rentals_tbody');
    const returnModal = document.getElementById('returnModal');
    const returnForm = document.getElementById('returnForm');
    const returnDetails = document.getElementById('return_details');
    const damagedCheck = document.getElementById('damaged_return');
    const damageFeeInfo = document.getElementById('damage_fee_info');
    
    let searchTimeout;

    // Búsqueda de rentas
    rentalSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            loadAllRentals();
            return;
        }

        searchTimeout = setTimeout(() => {
            searchRentals(query);
        }, 300);
    });

    // Limpiar búsqueda
    clearSearch.addEventListener('click', function() {
        rentalSearch.value = '';
        loadAllRentals();
    });

    // Checkbox de daños
    damagedCheck.addEventListener('change', function() {
        damageFeeInfo.style.display = this.checked ? 'block' : 'none';
    });

    // Manejar clicks en botones de devolución
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('return-btn') || e.target.closest('.return-btn')) {
            const button = e.target.classList.contains('return-btn') ? e.target : e.target.closest('.return-btn');
            const rentalId = button.getAttribute('data-rental-id');
            loadReturnDetails(rentalId);
        }
    });

    function searchRentals(query) {
        fetch(`{{ route('rentals.return.index') }}?search=${encodeURIComponent(query)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTbody = doc.getElementById('rentals_tbody');
            if (newTbody) {
                rentalsTable.innerHTML = newTbody.innerHTML;
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function loadAllRentals() {
        fetch(`{{ route('rentals.return.index') }}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTbody = doc.getElementById('rentals_tbody');
            if (newTbody) {
                rentalsTable.innerHTML = newTbody.innerHTML;
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function loadReturnDetails(rentalId) {
        fetch(`{{ url('rentals') }}/${rentalId}/return-details`)
            .then(response => response.json())
            .then(data => {
                returnDetails.innerHTML = `
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">${data.film_title}</h6>
                            <div class="row">
                                <div class="col-6">
                                    <strong>Cliente:</strong><br>
                                    <span>${data.customer_name}</span>
                                </div>
                                <div class="col-6">
                                    <strong>Fecha de Renta:</strong><br>
                                    <span>${data.rental_date}</span>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <strong>Debe Devolver:</strong><br>
                                    <span class="${data.is_overdue ? 'text-danger' : 'text-success'}">
                                        ${data.expected_return_date}
                                        ${data.is_overdue ? '(Atrasado ' + data.days_overdue + ' días)' : ''}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <strong>Tienda:</strong><br>
                                    <span>${data.store_id}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                returnForm.action = `{{ url('rentals') }}/${rentalId}/return`;
            })
            .catch(error => {
                console.error('Error:', error);
                returnDetails.innerHTML = '<div class="alert alert-danger">Error al cargar los detalles de la renta</div>';
            });
    }
});
</script>
@endpush