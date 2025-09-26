@extends('layouts.app')

@section('title', 'Nueva Renta - Sakila')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Procesar Nueva Renta
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ route('rentals.store') }}" method="POST" id="rentalForm">
                    @csrf
                    
                    <div class="row">
                        <!-- Selección de Cliente -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_id" class="form-label">Cliente *</label>
                                <select class="form-select @error('customer_id') is-invalid @enderror" 
                                        id="customer_id" 
                                        name="customer_id" 
                                        required>
                                    <option value="">Seleccionar cliente...</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->customer_id }}" 
                                                data-email="{{ $customer->email }}"
                                                data-active="{{ $customer->active ? 'Activo' : 'Inactivo' }}"
                                                {{ old('customer_id') == $customer->customer_id ? 'selected' : '' }}>
                                            {{ $customer->full_name }} (ID: {{ $customer->customer_id }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Selección de Empleado -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="staff_id" class="form-label">Empleado que procesa *</label>
                                <select class="form-select @error('staff_id') is-invalid @enderror" 
                                        id="staff_id" 
                                        name="staff_id" 
                                        required>
                                    <option value="">Seleccionar empleado...</option>
                                    @foreach($staff as $employee)
                                        <option value="{{ $employee->staff_id }}" 
                                                data-username="{{ $employee->username }}"
                                                data-email="{{ $employee->email }}"
                                                {{ old('staff_id') == $employee->staff_id ? 'selected' : '' }}>
                                            {{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->username }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('staff_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Información del Cliente Seleccionado -->
                    <div id="customer_info" class="alert alert-info" style="display: none;">
                        <h6><i class="fas fa-user me-2"></i>Información del Cliente</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Email:</strong> <span id="customer_email">-</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Estado:</strong> <span id="customer_status">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Fecha de Renta -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="rental_date" class="form-label">Fecha de Renta</label>
                                <input type="datetime-local" 
                                       class="form-control @error('rental_date') is-invalid @enderror" 
                                       id="rental_date" 
                                       name="rental_date" 
                                       value="{{ old('rental_date', now()->format('Y-m-d\TH:i')) }}">
                                <div class="form-text">Por defecto: fecha actual</div>
                                @error('rental_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Fecha Estimada de Devolución -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="estimated_return" class="form-label">Fecha Estimada de Devolución</label>
                                <input type="date" 
                                       class="form-control" 
                                       id="estimated_return" 
                                       readonly>
                                <div class="form-text">Se calcula automáticamente según la película</div>
                            </div>
                        </div>

                        <!-- Método de Pago -->
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Método de Pago</label>
                                <select class="form-select" id="payment_method" name="payment_method">
                                    <option value="cash" {{ old('payment_method', 'cash') == 'cash' ? 'selected' : '' }}>Efectivo</option>
                                    <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Tarjeta</option>
                                    <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>Transferencia</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Panel de Películas Disponibles -->
                    <div class="mb-3">
                        <label class="form-label">Seleccione una Película *</label>
                        <div class="card">
                            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                @if($available_films->count() > 0)
                                    <div class="row">
                                        @foreach($available_films as $film)
                                            <div class="col-md-6 mb-2">
                                                <div class="card film-option" 
                                                     data-film-id="{{ $film['film_id'] }}"
                                                     data-title="{{ $film['title'] }}"
                                                     data-rate="{{ $film['rental_rate'] }}"
                                                     data-duration="{{ $film['rental_duration'] }}"
                                                     data-copies="{{ json_encode($film['inventory_items']) }}"
                                                     style="cursor: pointer; border: 2px solid transparent;">
                                                    <div class="card-body p-2">
                                                        <h6 class="card-title mb-1">{{ $film['title'] }}</h6>
                                                        <small class="text-muted">
                                                            {{ $film['rental_rate'] }} | {{ $film['rental_duration'] }} días
                                                            <br>Copias disponibles: {{ $film['available_copies'] }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center text-muted">
                                        <i class="fas fa-film fa-3x mb-3"></i>
                                        <p>No hay películas disponibles en este momento.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Campo oculto para inventory_id -->
                    <input type="hidden" name="inventory_id" id="inventory_id" value="{{ old('inventory_id') }}">
                    @error('inventory_id')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <!-- Información de la Película Seleccionada -->
                    <div id="film_info" class="card mb-3" style="display: none;">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-film me-2"></i>Película Seleccionada</h6>
                        </div>
                        <div class="card-body">
                            <h5 id="selected_film_title" class="card-title mb-3"></h5>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Tienda:</strong>
                                    <span id="selected_store_id" class="badge bg-info"></span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Precio de Renta:</strong>
                                    <span id="selected_rental_rate" class="text-success fw-bold fs-5"></span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Duración Máxima:</strong>
                                    <span id="selected_rental_duration"></span> día(s)
                                </div>
                                <div class="col-md-3">
                                    <strong>ID Inventario:</strong>
                                    <span id="selected_inventory_id" class="text-muted"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campos Adicionales -->
                    <div class="row" id="additional_fields" style="display: none;">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="discount_amount" class="form-label">Descuento</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" 
                                           class="form-control" 
                                           id="discount_amount" 
                                           name="discount_amount" 
                                           value="{{ old('discount_amount', '0.00') }}" 
                                           step="0.01" 
                                           min="0">
                                </div>
                                <div class="form-text">Descuento aplicado a esta renta</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="total_amount" class="form-label">Total a Pagar</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" 
                                           class="form-control fw-bold" 
                                           id="total_amount" 
                                           readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="notes_section" style="display: none;">
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notas Adicionales</label>
                            <textarea class="form-control" 
                                      id="notes" 
                                      name="notes" 
                                      rows="2" 
                                      placeholder="Comentarios adicionales sobre la renta...">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('rentals.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary" id="submit_btn" disabled>
                            <i class="fas fa-save me-1"></i>Procesar Renta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const inventoryId = document.getElementById('inventory_id');
    const filmInfo = document.getElementById('film_info');
    const submitBtn = document.getElementById('submit_btn');
    const filmOptions = document.querySelectorAll('.film-option');
    const customerSelect = document.getElementById('customer_id');
    const customerInfo = document.getElementById('customer_info');
    const rentalDate = document.getElementById('rental_date');
    const estimatedReturn = document.getElementById('estimated_return');
    const discountAmount = document.getElementById('discount_amount');
    const totalAmount = document.getElementById('total_amount');
    const additionalFields = document.getElementById('additional_fields');
    const notesSection = document.getElementById('notes_section');
    
    let selectedFilmCard = null;
    let currentRentalRate = 0;

    // Manejar selección de cliente
    customerSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (this.value) {
            const email = selectedOption.dataset.email || 'No especificado';
            const status = selectedOption.dataset.active;
            
            document.getElementById('customer_email').textContent = email;
            document.getElementById('customer_status').textContent = status;
            customerInfo.style.display = 'block';
        } else {
            customerInfo.style.display = 'none';
        }
        validateForm();
    });

    // Manejar cambios en fecha de renta
    rentalDate.addEventListener('change', function() {
        calculateEstimatedReturn();
    });

    // Manejar cambios en descuento
    discountAmount.addEventListener('input', function() {
        calculateTotal();
    });

    // Manejar selección de películas de la lista
    filmOptions.forEach(card => {
        card.addEventListener('click', function() {
            const filmId = this.dataset.filmId;
            const title = this.dataset.title;
            const rate = this.dataset.rate;
            const duration = this.dataset.duration;
            const copies = JSON.parse(this.dataset.copies);
            
            // Seleccionar la primera copia disponible (tienda 1 preferida)
            let selectedInventoryId = null;
            let selectedStoreId = null;
            
            if (copies['1']) {
                selectedInventoryId = copies['1'];
                selectedStoreId = 1;
            } else if (copies['2']) {
                selectedInventoryId = copies['2'];
                selectedStoreId = 2;
            } else {
                // Tomar el primer disponible
                const storeIds = Object.keys(copies);
                if (storeIds.length > 0) {
                    selectedStoreId = storeIds[0];
                    selectedInventoryId = copies[selectedStoreId];
                }
            }
            
            if (selectedInventoryId) {
                selectFromList({
                    inventory_id: selectedInventoryId,
                    title: title,
                    store_id: selectedStoreId,
                    rental_rate: rate,
                    rental_duration: duration
                }, this);
            }
        });
    });

    function selectFromList(item, cardElement) {
        inventoryId.value = item.inventory_id;
        
        document.getElementById('selected_film_title').textContent = item.title;
        document.getElementById('selected_store_id').textContent = item.store_id;
        document.getElementById('selected_rental_rate').textContent = item.rental_rate;
        document.getElementById('selected_rental_duration').textContent = item.rental_duration;
        document.getElementById('selected_inventory_id').textContent = item.inventory_id;
        
        // Extraer valor numérico del precio
        currentRentalRate = parseFloat(item.rental_rate.replace(/[^0-9.]/g, ''));
        
        filmInfo.style.display = 'block';
        
        showAdditionalFields();
        calculateEstimatedReturn();
        calculateTotal();
        validateForm();
        
        // Resaltar tarjeta seleccionada
        if (selectedFilmCard) {
            selectedFilmCard.style.border = '2px solid transparent';
        }
        cardElement.style.border = '2px solid #0d6efd';
        selectedFilmCard = cardElement;
    }



    function calculateEstimatedReturn() {
        const rentalDateValue = rentalDate.value;
        const duration = document.getElementById('selected_rental_duration').textContent;
        
        if (rentalDateValue && duration) {
            const rentalDateTime = new Date(rentalDateValue);
            rentalDateTime.setDate(rentalDateTime.getDate() + parseInt(duration));
            
            const year = rentalDateTime.getFullYear();
            const month = String(rentalDateTime.getMonth() + 1).padStart(2, '0');
            const day = String(rentalDateTime.getDate()).padStart(2, '0');
            
            estimatedReturn.value = `${year}-${month}-${day}`;
        }
    }

    function calculateTotal() {
        const discount = parseFloat(discountAmount.value) || 0;
        const total = Math.max(0, currentRentalRate - discount);
        totalAmount.value = total.toFixed(2);
    }

    function validateForm() {
        const hasCustomer = customerSelect.value !== '';
        const hasStaff = document.getElementById('staff_id').value !== '';
        const hasInventory = inventoryId.value !== '';
        
        submitBtn.disabled = !(hasCustomer && hasStaff && hasInventory);
    }

    function showAdditionalFields() {
        additionalFields.style.display = 'block';
        notesSection.style.display = 'block';
    }
});
</script>
@endpush
@endsection