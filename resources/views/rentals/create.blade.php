@extends('layouts.app')

@section('title', 'Nueva Renta - Sistema Sakila')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Procesar Nueva Renta
                </h4>
            </div>
            <div class="card-body">
                <!-- Área de errores generales -->
                @if($errors->any())
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Se encontraron los siguientes errores:</h6>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atención:</strong> {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('rentals.store') }}" method="POST" id="rentalForm">
                    @csrf
                    
                    <div class="row">
                        <!-- Selección de Cliente -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="customer_method" class="form-label">Método de Selección de Cliente *</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="customer_method" id="method_select" value="select" checked>
                                    <label class="btn btn-outline-primary" for="method_select">Lista</label>
                                    
                                    <input type="radio" class="btn-check" name="customer_method" id="method_search" value="search">
                                    <label class="btn btn-outline-primary" for="method_search">Búsqueda</label>
                                </div>
                                
                                <!-- Selección tradicional -->
                                <div id="customer_select_method" class="mt-3">
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
                                                {{ $customer->first_name }} {{ $customer->last_name }} (ID: {{ $customer->customer_id }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <!-- Búsqueda AJAX -->
                                <div id="customer_search_method" class="mt-3" style="display: none;">
                                    <div class="position-relative">
                                        <input type="text" 
                                               class="form-control" 
                                               id="customer_search" 
                                               placeholder="Buscar cliente por nombre o email..."
                                               autocomplete="off">
                                        <input type="hidden" id="customer_id_search">
                                        <div id="customer_dropdown" class="dropdown-menu w-100" style="display: none;"></div>
                                    </div>
                                    <div id="selected_customer" class="mt-2" style="display: none;">
                                        <div class="alert alert-info">
                                            <i class="fas fa-user me-2"></i>
                                            <strong id="customer_name"></strong><br>
                                            <small>Email: <span id="customer_email"></span></small><br>
                                            <small>Estado: <span id="customer_status"></span></small>
                                        </div>
                                    </div>
                                    
                                    @error('customer_id')
                                        <div class="alert alert-danger mt-2">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <strong>Error:</strong> {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                
                                @error('customer_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    <div class="alert alert-danger mt-2">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        <strong>Error:</strong> {{ $message }}
                                    </div>
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
                                                {{ old('staff_id', auth()->user()->staff_id ?? '') == $employee->staff_id ? 'selected' : '' }}>
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

                    <!-- Panel de Selección de Películas -->
                    <div class="mb-3">
                        <label class="form-label">Seleccione una Película *</label>
                        
                        <!-- Métodos de selección de película -->
                        <div class="btn-group w-100 mb-3" role="group">
                            <input type="radio" class="btn-check" name="film_method" id="film_method_search" value="search" checked>
                            <label class="btn btn-outline-success" for="film_method_search">Búsqueda</label>
                            
                            <input type="radio" class="btn-check" name="film_method" id="film_method_list" value="list">
                            <label class="btn btn-outline-success" for="film_method_list">Lista Visual</label>
                        </div>

                        <!-- Búsqueda AJAX -->
                        <div id="film_search_method">
                            <div class="position-relative">
                                <input type="text" 
                                       class="form-control @error('inventory_id') is-invalid @enderror" 
                                       id="film_search" 
                                       placeholder="Buscar película por título..."
                                       autocomplete="off">
                                <div id="film_dropdown" class="dropdown-menu w-100" style="display: none;"></div>
                            </div>
                        </div>

                        <!-- Lista visual de películas -->
                        <div id="film_list_method" style="display: none;">
                            <div class="mb-3">
                                <input type="text" 
                                       class="form-control" 
                                       id="film_list_search" 
                                       placeholder="Filtrar películas..."
                                       autocomplete="off">
                            </div>
                            <div class="row" id="films_grid">
                                @foreach($availableFilms as $film)
                                    <div class="col-md-4 col-lg-3 mb-3 film-card" 
                                         data-title="{{ strtolower($film->title) }}"
                                         data-rating="{{ $film->rating }}"
                                         data-category="{{ $film->categories->first()->name ?? '' }}">
                                        <div class="card film-option h-100" 
                                             style="cursor: pointer; transition: all 0.3s;"
                                             data-film-id="{{ $film->film_id }}"
                                             data-title="{{ $film->title }}"
                                             data-rating="{{ $film->rating }}"
                                             data-rate="{{ $film->rental_rate }}"
                                             data-duration="{{ $film->rental_duration }}"
                                             data-copies="{{ json_encode($film->available_by_store->map(function($items, $store) { return $items->first()->inventory_id; })) }}"
                                             onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 4px 8px rgba(0,0,0,0.15)';"
                                             onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.12)';">
                                            <div class="card-body p-3">
                                                <h6 class="card-title mb-2" style="font-size: 0.9rem; line-height: 1.2;">
                                                    {{ Str::limit($film->title, 25) }}
                                                </h6>
                                                <div class="mb-2">
                                                    <span class="badge bg-primary">{{ $film->rating }}</span>
                                                    @if($film->categories->first())
                                                        <span class="badge bg-secondary">{{ $film->categories->first()->name }}</span>
                                                    @endif
                                                </div>
                                                <div class="small text-muted">
                                                    <div><strong>Precio:</strong> ${{ $film->rental_rate }}</div>
                                                    <div><strong>Duración:</strong> {{ $film->rental_duration }} días</div>
                                                    <div><strong>Disponibles:</strong> 
                                                        <span class="text-success fw-bold">{{ $film->available_copies }}</span>
                                                    </div>
                                                    <div><strong>Tiendas:</strong> 
                                                        @foreach($film->available_by_store as $store => $items)
                                                            <span class="badge bg-info">{{ $store }}</span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            @if($availableFilms->count() >= 24)
                                <div class="text-center mt-3">
                                    <button type="button" class="btn btn-outline-primary" id="load_more_films">
                                        <i class="fas fa-plus me-1"></i>Cargar Más Películas
                                    </button>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Campo oculto para inventory_id -->
                        <input type="hidden" name="inventory_id" id="inventory_id" value="{{ old('inventory_id') }}">
                        @error('inventory_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        
                        <!-- Información de la Película Seleccionada -->
                        <div id="film_info" class="card mt-3" style="display: none;">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-film me-2"></i>Película Seleccionada</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Título:</strong> <span id="selected_film_title">-</span><br>
                                        <strong>Rating:</strong> <span id="selected_film_rating">-</span><br>
                                        <strong>Duración de Renta:</strong> <span id="selected_film_duration">-</span> días
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Precio:</strong> $<span id="selected_film_rate">-</span><br>
                                        <strong>Copias Disponibles:</strong> <span id="selected_film_copies">-</span><br>
                                        <strong>Tienda:</strong> <span id="selected_store">-</span>
                                    </div>
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

@push('styles')
<style>
.dropdown-menu {
    max-height: 300px;
    overflow-y: auto;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    z-index: 1050;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
}

.dropdown-item {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #f8f9fa;
    transition: background-color 0.15s ease-in-out;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

.dropdown-item:last-child {
    border-bottom: none;
}

.position-relative {
    position: relative;
}

#customer_dropdown .dropdown-item,
#film_dropdown .dropdown-item {
    white-space: normal;
    word-wrap: break-word;
}

.form-control:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.btn-check:checked + .btn {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: white;
}

/* Estilos para las tarjetas de películas */
.film-option {
    border: 2px solid transparent;
    transition: all 0.3s ease;
    cursor: pointer;
}

.film-option:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.film-option.selected {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.film-card {
    transition: opacity 0.3s ease;
}

.film-card.hidden {
    display: none;
}

#films_grid {
    max-height: 600px;
    overflow-y: auto;
}

/* Scrollbar personalizada para el grid */
#films_grid::-webkit-scrollbar {
    width: 8px;
}

#films_grid::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

#films_grid::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

#films_grid::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const customerMethodRadios = document.querySelectorAll('input[name="customer_method"]');
    const customerSelectMethod = document.getElementById('customer_select_method');
    const customerSearchMethod = document.getElementById('customer_search_method');
    const customerSelect = document.getElementById('customer_id');
    const customerSearch = document.getElementById('customer_search');
    const customerIdSearch = document.getElementById('customer_id_search');
    const customerDropdown = document.getElementById('customer_dropdown');
    const selectedCustomer = document.getElementById('selected_customer');
    
    const filmMethodRadios = document.querySelectorAll('input[name="film_method"]');
    const filmSearchMethod = document.getElementById('film_search_method');
    const filmListMethod = document.getElementById('film_list_method');
    const filmSearch = document.getElementById('film_search');
    const filmListSearch = document.getElementById('film_list_search');
    const filmDropdown = document.getElementById('film_dropdown');
    const inventoryId = document.getElementById('inventory_id');
    const filmInfo = document.getElementById('film_info');
    const submitBtn = document.getElementById('submit_btn');
    
    const rentalDate = document.getElementById('rental_date');
    const estimatedReturn = document.getElementById('estimated_return');
    const discountAmount = document.getElementById('discount_amount');
    const totalAmount = document.getElementById('total_amount');
    const additionalFields = document.getElementById('additional_fields');
    const notesSection = document.getElementById('notes_section');
    
    let currentRentalRate = 0;
    let searchTimeout;
    let selectedFilmCard = null;

    // Manejar cambio de método de selección de cliente
    customerMethodRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'select') {
                customerSelectMethod.style.display = 'block';
                customerSearchMethod.style.display = 'none';
                customerSelect.required = true;
                customerIdSearch.removeAttribute('name');
            } else {
                customerSelectMethod.style.display = 'none';
                customerSearchMethod.style.display = 'block';
                customerSelect.required = false;
                customerIdSearch.setAttribute('name', 'customer_id');
            }
            validateForm();
        });
    });

    // Manejar cambio de método de selección de película
    filmMethodRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'search') {
                filmSearchMethod.style.display = 'block';
                filmListMethod.style.display = 'none';
            } else {
                filmSearchMethod.style.display = 'none';
                filmListMethod.style.display = 'block';
            }
            // Limpiar selección cuando cambia el método
            resetFilmSelection();
        });
    });

    // Búsqueda de clientes
    customerSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(() => {
                searchCustomers(query);
            }, 300);
        } else {
            customerDropdown.style.display = 'none';
            selectedCustomer.style.display = 'none';
            customerIdSearch.value = '';
            validateForm();
        }
    });

    // Búsqueda de películas AJAX
    filmSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length >= 2) {
            searchTimeout = setTimeout(() => {
                searchFilms(query);
            }, 300);
        } else {
            filmDropdown.style.display = 'none';
            resetFilmSelection();
        }
    });

    // Filtro de lista de películas
    filmListSearch.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        const filmCards = document.querySelectorAll('.film-card');
        
        filmCards.forEach(card => {
            const title = card.dataset.title;
            const rating = card.dataset.rating.toLowerCase();
            const category = card.dataset.category.toLowerCase();
            
            const matches = title.includes(query) || 
                          rating.includes(query) || 
                          category.includes(query);
            
            if (matches || query === '') {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Manejar clics en las tarjetas de películas
    document.querySelectorAll('.film-option').forEach(card => {
        card.addEventListener('click', function() {
            selectFromFilmCard(this);
        });
    });

    // Manejar selección de cliente tradicional
    customerSelect.addEventListener('change', function() {
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

    // Función para buscar clientes
    function searchCustomers(query) {
        fetch(`/customers/search?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.customers && data.customers.length > 0) {
                    let html = '';
                    data.customers.forEach(customer => {
                        html += `
                            <div class="dropdown-item" style="cursor: pointer;" 
                                 onclick="selectCustomer(${customer.customer_id}, '${customer.first_name} ${customer.last_name}', '${customer.email}', ${customer.active})">
                                <strong>${customer.first_name} ${customer.last_name}</strong><br>
                                <small class="text-muted">ID: ${customer.customer_id} | Email: ${customer.email} | Estado: ${customer.active ? 'Activo' : 'Inactivo'}</small>
                            </div>
                        `;
                    });
                    customerDropdown.innerHTML = html;
                    customerDropdown.style.display = 'block';
                } else {
                    customerDropdown.innerHTML = '<div class="dropdown-item text-muted">No se encontraron clientes</div>';
                    customerDropdown.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error al buscar clientes:', error);
                customerDropdown.style.display = 'none';
            });
    }

    // Función para buscar películas
    function searchFilms(query) {
        fetch(`/films/search-available?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.films && data.films.length > 0) {
                    let html = '';
                    data.films.forEach(film => {
                        html += `
                            <div class="dropdown-item" style="cursor: pointer;" 
                                 onclick="selectFilm(${film.inventory_id}, '${film.title}', '${film.rating}', ${film.rental_duration}, ${film.rental_rate}, ${film.available_copies}, ${film.store_id})">
                                <strong>${film.title}</strong> (${film.rating})<br>
                                <small class="text-muted">
                                    Tienda: ${film.store_id} | Precio: $${film.rental_rate} | 
                                    Duración: ${film.rental_duration} días | 
                                    Disponibles: ${film.available_copies}
                                </small>
                            </div>
                        `;
                    });
                    filmDropdown.innerHTML = html;
                    filmDropdown.style.display = 'block';
                } else {
                    filmDropdown.innerHTML = '<div class="dropdown-item text-muted">No se encontraron películas disponibles</div>';
                    filmDropdown.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error al buscar películas:', error);
                filmDropdown.style.display = 'none';
            });
    }

    // Función para seleccionar película desde tarjeta
    function selectFromFilmCard(cardElement) {
        const filmId = cardElement.dataset.filmId;
        const title = cardElement.dataset.title;
        const rating = cardElement.dataset.rating;
        const rate = cardElement.dataset.rate;
        const duration = cardElement.dataset.duration;
        const copies = JSON.parse(cardElement.dataset.copies);
        
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
            selectFilmCommon(selectedInventoryId, title, rating, duration, rate, 
                           Object.keys(copies).length, selectedStoreId);
            
            // Resaltar tarjeta seleccionada
            if (selectedFilmCard) {
                selectedFilmCard.classList.remove('selected');
            }
            cardElement.classList.add('selected');
            selectedFilmCard = cardElement;
        }
    }

    // Función global para seleccionar cliente
    window.selectCustomer = function(id, name, email, active) {
        customerIdSearch.value = id;
        customerSearch.value = name;
        document.getElementById('customer_name').textContent = name;
        document.getElementById('customer_email').textContent = email;
        document.getElementById('customer_status').textContent = active ? 'Activo' : 'Inactivo';
        selectedCustomer.style.display = 'block';
        customerDropdown.style.display = 'none';
        validateForm();
    };

    // Función global para seleccionar película desde búsqueda
    window.selectFilm = function(inventoryIdValue, title, rating, duration, rate, copies, storeId) {
        selectFilmCommon(inventoryIdValue, title, rating, duration, rate, copies, storeId);
        filmSearch.value = title;
        filmDropdown.style.display = 'none';
    };

    // Función común para seleccionar película
    function selectFilmCommon(inventoryIdValue, title, rating, duration, rate, copies, storeId) {
        inventoryId.value = inventoryIdValue;
        
        document.getElementById('selected_film_title').textContent = title;
        document.getElementById('selected_film_rating').textContent = rating;
        document.getElementById('selected_film_duration').textContent = duration;
        document.getElementById('selected_film_rate').textContent = rate;
        document.getElementById('selected_film_copies').textContent = copies;
        document.getElementById('selected_store').textContent = `Tienda ${storeId}`;
        
        currentRentalRate = parseFloat(rate);
        filmInfo.style.display = 'block';
        
        showAdditionalFields();
        calculateEstimatedReturn();
        calculateTotal();
        validateForm();
    }

    function resetFilmSelection() {
        inventoryId.value = '';
        filmInfo.style.display = 'none';
        additionalFields.style.display = 'none';
        notesSection.style.display = 'none';
        currentRentalRate = 0;
        
        if (selectedFilmCard) {
            selectedFilmCard.classList.remove('selected');
            selectedFilmCard = null;
        }
        
        validateForm();
    }

    function showAdditionalFields() {
        additionalFields.style.display = 'block';
        notesSection.style.display = 'block';
    }

    function calculateEstimatedReturn() {
        const rental = new Date(rentalDate.value);
        const duration = parseInt(document.getElementById('selected_film_duration').textContent) || 3;
        
        if (rental && duration) {
            const returnDate = new Date(rental);
            returnDate.setDate(returnDate.getDate() + duration);
            estimatedReturn.value = returnDate.toISOString().split('T')[0];
        }
    }

    function calculateTotal() {
        const discount = parseFloat(discountAmount.value) || 0;
        const total = Math.max(0, currentRentalRate - discount);
        totalAmount.value = total.toFixed(2);
    }

    function validateForm() {
        const customerMethod = document.querySelector('input[name="customer_method"]:checked').value;
        let customerValid = false;
        
        if (customerMethod === 'select') {
            customerValid = customerSelect.value !== '';
        } else {
            customerValid = customerIdSearch.value !== '';
        }
        
        const filmValid = inventoryId.value !== '';
        const staffValid = document.getElementById('staff_id').value !== '';
        
        submitBtn.disabled = !(customerValid && filmValid && staffValid);
    }

    // Ocultar dropdowns al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#customer_search_method')) {
            customerDropdown.style.display = 'none';
        }
        if (!e.target.closest('#film_search_method')) {
            filmDropdown.style.display = 'none';
        }
    });

    // Validación inicial
    validateForm();
    
    // Mantener estado del formulario en caso de errores
    @if(old('customer_id') && !old('customer_method'))
        // Si hay un customer_id en old pero no hay método definido, probablemente vino del método de búsqueda
        document.getElementById('method_search').checked = true;
        customerSelectMethod.style.display = 'none';
        customerSearchMethod.style.display = 'block';
        customerSelect.required = false;
        customerIdSearch.setAttribute('name', 'customer_id');
        customerIdSearch.value = '{{ old('customer_id') }}';
        
        // Si hay customer_id, intentar mostrar información del cliente
        @if(old('customer_id'))
            fetch(`/customers/search?q={{ old('customer_id') }}`)
                .then(response => response.json())
                .then(data => {
                    if (data.customers && data.customers.length > 0) {
                        const customer = data.customers.find(c => c.customer_id == {{ old('customer_id') }});
                        if (customer) {
                            selectCustomer(customer.customer_id, customer.full_name, customer.email, customer.active);
                        }
                    }
                })
                .catch(error => console.error('Error al cargar cliente:', error));
        @endif
    @endif
});
</script>
@endpush



@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let selectedCustomer = null;
    let selectedFilm = null;
    let customerSearchTimer = null;
    let filmSearchTimer = null;
    let currentCustomerMethod = 'select';

    // Elementos del DOM
    const methodSelect = document.getElementById('method_select');
    const methodSearch = document.getElementById('method_search');
    const customerSelectMethod = document.getElementById('customer_select_method');
    const customerSearchMethod = document.getElementById('customer_search_method');
    const customerSelect = document.getElementById('customer_id');
    const customerSearch = document.getElementById('customer_search');
    const customerDropdown = document.getElementById('customer_dropdown');
    const selectedCustomerDiv = document.getElementById('selected_customer');
    const customerIdSearch = document.getElementById('customer_id_search');
    
    const filmSearch = document.getElementById('film_search');
    const filmDropdown = document.getElementById('film_dropdown');
    const filmInfo = document.getElementById('film_info');
    const inventoryIdInput = document.getElementById('inventory_id');
    
    const rentalDate = document.getElementById('rental_date');
    const estimatedReturn = document.getElementById('estimated_return');
    const discountAmount = document.getElementById('discount_amount');
    const totalAmount = document.getElementById('total_amount');

    // Alternar métodos de selección de cliente
    methodSelect.addEventListener('change', function() {
        if (this.checked) {
            currentCustomerMethod = 'select';
            customerSelectMethod.style.display = 'block';
            customerSearchMethod.style.display = 'none';
            selectedCustomerDiv.style.display = 'none';
            selectedCustomer = null;
            if (customerIdSearch) customerIdSearch.value = '';
            if (customerSearch) customerSearch.value = '';
        }
    });

    methodSearch.addEventListener('change', function() {
        if (this.checked) {
            currentCustomerMethod = 'search';
            customerSelectMethod.style.display = 'none';
            customerSearchMethod.style.display = 'block';
            if (customerSelect) customerSelect.value = '';
        }
    });

    // Búsqueda de clientes (solo para método search)
    if (customerSearch) {
        customerSearch.addEventListener('input', function() {
            const search = this.value;
            
            clearTimeout(customerSearchTimer);
            
            if (search.length < 2) {
                hideCustomerDropdown();
                return;
            }
            
            customerSearchTimer = setTimeout(() => {
                searchCustomers(search);
            }, 300);
        });
    }

    function searchCustomers(search) {
        fetch(`{{ route('api.search-customers') }}?search=${encodeURIComponent(search)}`)
            .then(response => response.json())
            .then(customers => {
                showCustomerDropdown(customers);
            })
            .catch(error => {
                console.error('Error searching customers:', error);
            });
    }

    function showCustomerDropdown(customers) {
        if (!customerDropdown) return;
        
        customerDropdown.innerHTML = '';
        
        if (customers.length === 0) {
            customerDropdown.innerHTML = '<div class="dropdown-item text-muted">No se encontraron clientes</div>';
        } else {
            customers.forEach(customer => {
                const item = document.createElement('div');
                item.className = `dropdown-item ${customer.has_pending_rentals ? 'text-warning' : ''}`;
                item.style.cursor = 'pointer';
                item.innerHTML = `
                    <div>
                        <strong>${customer.name}</strong>
                        <br><small>${customer.email}</small>
                        <br><small class="${customer.has_pending_rentals ? 'text-danger' : 'text-success'}">${customer.status_message}</small>
                    </div>
                `;
                
                item.addEventListener('click', () => selectCustomerFromSearch(customer));
                customerDropdown.appendChild(item);
            });
        }
        
        customerDropdown.style.display = 'block';
    }

    function hideCustomerDropdown() {
        if (customerDropdown) customerDropdown.style.display = 'none';
    }

    function selectCustomerFromSearch(customer) {
        selectedCustomer = customer;
        if (customerSearch) customerSearch.value = customer.name;
        if (customerIdSearch) customerIdSearch.value = customer.id;
        
        // Sincronizar con el select tradicional
        if (customerSelect) customerSelect.value = customer.id;
        
        if (document.getElementById('customer_name')) document.getElementById('customer_name').textContent = customer.name;
        if (document.getElementById('customer_email')) document.getElementById('customer_email').textContent = customer.email;
        if (document.getElementById('customer_status')) {
            document.getElementById('customer_status').textContent = customer.status_message;
            document.getElementById('customer_status').className = customer.has_pending_rentals ? 'text-danger' : 'text-success';
        }
        
        if (selectedCustomerDiv) selectedCustomerDiv.style.display = 'block';
        hideCustomerDropdown();
        validateForm();
    }

    // Búsqueda de películas
    if (filmSearch) {
        filmSearch.addEventListener('input', function() {
            const search = this.value;
            
            clearTimeout(filmSearchTimer);
            
            if (search.length < 2) {
                hideFilmDropdown();
                return;
            }
            
            filmSearchTimer = setTimeout(() => {
                searchFilms(search);
            }, 300);
        });
    }

    function searchFilms(search) {
        fetch(`{{ route('api.search-films') }}?search=${encodeURIComponent(search)}`)
            .then(response => response.json())
            .then(films => {
                showFilmDropdown(films);
            })
            .catch(error => {
                console.error('Error searching films:', error);
            });
    }

    function showFilmDropdown(films) {
        if (!filmDropdown) return;
        
        filmDropdown.innerHTML = '';
        
        if (films.length === 0) {
            filmDropdown.innerHTML = '<div class="dropdown-item text-muted">No se encontraron películas disponibles</div>';
        } else {
            films.forEach(film => {
                const item = document.createElement('div');
                item.className = 'dropdown-item';
                item.style.cursor = 'pointer';
                item.innerHTML = `
                    <div>
                        <strong>${film.title}</strong> <span class="badge bg-secondary">${film.rating}</span>
                        <br><small>$${film.rental_rate} | ${film.rental_duration} días | ${film.total_available} disponibles</small>
                    </div>
                `;
                
                item.addEventListener('click', () => selectFilm(film));
                filmDropdown.appendChild(item);
            });
        }
        
        filmDropdown.style.display = 'block';
    }

    function hideFilmDropdown() {
        if (filmDropdown) filmDropdown.style.display = 'none';
    }

    function selectFilm(film) {
        selectedFilm = film;
        if (filmSearch) filmSearch.value = film.title;
        
        // Seleccionar el primer inventario disponible
        const firstStore = Object.keys(film.stores)[0];
        const firstInventoryId = film.stores[firstStore].inventory_ids[0];
        if (inventoryIdInput) inventoryIdInput.value = firstInventoryId;
        
        // Mostrar información de la película
        if (document.getElementById('selected_film_title')) document.getElementById('selected_film_title').textContent = film.title;
        if (document.getElementById('selected_film_rating')) document.getElementById('selected_film_rating').textContent = film.rating;
        if (document.getElementById('selected_film_duration')) document.getElementById('selected_film_duration').textContent = film.rental_duration;
        if (document.getElementById('selected_film_rate')) document.getElementById('selected_film_rate').textContent = film.rental_rate;
        if (document.getElementById('selected_film_copies')) document.getElementById('selected_film_copies').textContent = film.total_available;
        if (document.getElementById('selected_store')) document.getElementById('selected_store').textContent = `Tienda #${firstStore}`;
        
        if (filmInfo) filmInfo.style.display = 'block';
        hideFilmDropdown();
        calculateEstimatedReturn();
        calculateTotal();
        validateForm();
    }

    // Cálculos automáticos
    function calculateEstimatedReturn() {
        if (!selectedFilm || !rentalDate || !rentalDate.value) return;
        
        const rentalDateTime = new Date(rentalDate.value);
        rentalDateTime.setDate(rentalDateTime.getDate() + parseInt(selectedFilm.rental_duration));
        
        const year = rentalDateTime.getFullYear();
        const month = String(rentalDateTime.getMonth() + 1).padStart(2, '0');
        const day = String(rentalDateTime.getDate()).padStart(2, '0');
        
        if (estimatedReturn) estimatedReturn.value = `${year}-${month}-${day}`;
    }

    function calculateTotal() {
        if (!selectedFilm) return;
        
        const discount = parseFloat(discountAmount?.value) || 0;
        const total = Math.max(0, selectedFilm.rental_rate - discount);
        if (totalAmount) totalAmount.value = total.toFixed(2);
    }

    function validateForm() {
        let hasCustomer = false;
        
        if (currentCustomerMethod === 'select') {
            hasCustomer = customerSelect && customerSelect.value !== '';
        } else {
            hasCustomer = selectedCustomer !== null;
        }
        
        const hasStaff = document.getElementById('staff_id') && document.getElementById('staff_id').value !== '';
        const hasFilm = selectedFilm !== null;
        
        const submitBtn = document.getElementById('submit_btn');
        if (submitBtn) submitBtn.disabled = !(hasCustomer && hasStaff && hasFilm);
    }

    // Event listeners
    if (rentalDate) rentalDate.addEventListener('change', calculateEstimatedReturn);
    if (discountAmount) discountAmount.addEventListener('input', calculateTotal);
    if (customerSelect) customerSelect.addEventListener('change', validateForm);

    // Cerrar dropdowns al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (customerSearch && customerDropdown && 
            !customerSearch.contains(e.target) && !customerDropdown.contains(e.target)) {
            hideCustomerDropdown();
        }
        if (filmSearch && filmDropdown && 
            !filmSearch.contains(e.target) && !filmDropdown.contains(e.target)) {
            hideFilmDropdown();
        }
    });

    validateForm();
});
</script>
@endpush
@endsection