@extends('layouts.app')

@section('title', 'Procesar Devolución - Sakila')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-undo me-2"></i>Procesar Devolución
                </h4>
            </div>
            <div class="card-body">
                <!-- Búsqueda de Renta Activa -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <label for="rental_search" class="form-label">Buscar Renta Activa</label>
                        <input type="text" 
                               class="form-control" 
                               id="rental_search" 
                               placeholder="ID de renta, nombre del cliente o título de la película..."
                               autocomplete="off">
                        <div class="form-text">Escriba al menos 2 caracteres para buscar rentas activas.</div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-secondary w-100" id="clear_search">
                            <i class="fas fa-eraser me-1"></i>Limpiar Búsqueda
                        </button>
                    </div>
                </div>

                <!-- Resultados de Búsqueda -->
                <div id="search_results">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Película</th>
                                    <th>Fecha Renta</th>
                                    <th>Debe Devolver</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody id="rentals_tbody">
                                @if($activeRentals->count() > 0)
                                    @foreach($activeRentals as $rental)
                                        <tr class="rental-row" data-rental-id="{{ $rental->rental_id }}">
                                            <td><strong>{{ $rental->rental_id }}</strong></td>
                                            <td>{{ $rental->customer->formatted_name }}</td>
                                            <td>{{ $rental->inventory->film->title }}</td>
                                            <td>{{ $rental->rental_date->format('d/m/Y H:i') }}</td>
                                            <td>{{ $rental->expected_return_date->format('d/m/Y') }}</td>
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
        fetch(`{{ route('rentals.return') }}?search=${encodeURIComponent(query)}`, {
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
        fetch(`{{ route('rentals.return') }}`, {
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
@endsection