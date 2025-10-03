@extends('layouts.app')

@section('title', 'Procesar Devolución - Renta #' . $rental->rental_id)

@section('content')
<div class="container">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-undo"></i> Procesar Devolución</h2>
            <p class="text-muted">Renta #{{ $rental->rental_id }} - {{ $rental->customer->full_name }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('rentals.return.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Devoluciones
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información de la Renta -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información de la Renta</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID de Renta:</strong></td>
                                    <td>#{{ $rental->rental_id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Cliente:</strong></td>
                                    <td>
                                        {{ $rental->customer->full_name }}
                                        <br>
                                        <small class="text-muted">{{ $rental->customer->email }}</small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Película:</strong></td>
                                    <td>
                                        {{ $rental->inventory->film->title ?? 'N/A' }}
                                        <br>
                                        <small class="text-muted">
                                            Duración de renta: {{ $rental->inventory->film->rental_duration ?? 'N/A' }} días
                                        </small>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Empleado que rentó:</strong></td>
                                    <td>{{ $rental->staff->first_name ?? 'N/A' }} {{ $rental->staff->last_name ?? '' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Fecha de Renta:</strong></td>
                                    <td>{{ $rental->rental_date->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha Esperada:</strong></td>
                                    <td>{{ $rental->expected_return_date->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Estado:</strong></td>
                                    <td>
                                        @if($rental->isOverdue())
                                            <span class="badge bg-danger">
                                                <i class="fas fa-exclamation-triangle"></i> 
                                                Atrasada {{ $rental->daysOverdue() }} días
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="fas fa-clock"></i> A tiempo
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Costo de Renta:</strong></td>
                                    <td>${{ number_format($rental->inventory->film->rental_rate ?? 0, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de Devolución -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-edit"></i> Procesar Devolución</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('rentals.return.process', $rental->rental_id) }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="return_date" class="form-label">Fecha de Devolución <span class="text-danger">*</span></label>
                                    <input type="datetime-local" 
                                           class="form-control @error('return_date') is-invalid @enderror" 
                                           id="return_date" 
                                           name="return_date" 
                                           value="{{ old('return_date', now()->format('Y-m-d\TH:i')) }}"
                                           required>
                                    @error('return_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="late_fee" class="form-label">Multa por Atraso</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               class="form-control @error('late_fee') is-invalid @enderror" 
                                               id="late_fee" 
                                               name="late_fee" 
                                               value="{{ old('late_fee', $lateFee) }}"
                                               min="0" 
                                               step="0.01"
                                               {{ $rental->isOverdue() ? '' : 'readonly' }}>
                                        @error('late_fee')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @if($rental->isOverdue())
                                        <div class="form-text text-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Multa calculada: ${{ number_format($lateFee, 2) }} 
                                            ({{ $rental->daysOverdue() }} días x $1.50)
                                        </div>
                                    @else
                                        <div class="form-text text-success">
                                            <i class="fas fa-check"></i>
                                            No hay multa - devolución a tiempo
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notas Adicionales</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3" 
                                      placeholder="Comentarios sobre la condición de la película, incidencias, etc...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('rentals.return.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check"></i> Procesar Devolución
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel de Información Adicional -->
        <div class="col-md-4">
            <!-- Resumen de Costos -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calculator"></i> Resumen de Costos</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td>Costo de Renta:</td>
                            <td class="text-end">${{ number_format($rental->inventory->film->rental_rate ?? 0, 2) }}</td>
                        </tr>
                        @if($lateFee > 0)
                        <tr class="text-warning">
                            <td>Multa por Atraso:</td>
                            <td class="text-end">${{ number_format($lateFee, 2) }}</td>
                        </tr>
                        <tr class="table-warning fw-bold">
                            <td>Total a Cobrar:</td>
                            <td class="text-end">${{ number_format(($rental->inventory->film->rental_rate ?? 0) + $lateFee, 2) }}</td>
                        </tr>
                        @else
                        <tr class="table-success fw-bold">
                            <td>Total a Cobrar:</td>
                            <td class="text-end">${{ number_format($rental->inventory->film->rental_rate ?? 0, 2) }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Historial del Cliente -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Información del Cliente</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><strong>Nombre:</strong></td>
                            <td>{{ $rental->customer->full_name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $rental->customer->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Estado:</strong></td>
                            <td>
                                @if($rental->customer->active)
                                    <span class="badge bg-success">Activo</span>
                                @else
                                    <span class="badge bg-danger">Inactivo</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Rentas Activas:</strong></td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $rental->customer->activeRentals()->count() }}
                                </span>
                            </td>
                        </tr>
                    </table>
                    
                    <div class="mt-3">
                        <a href="{{ route('customers.show', $rental->customer->customer_id) }}" 
                           class="btn btn-sm btn-outline-primary w-100">
                            <i class="fas fa-user"></i> Ver Perfil Completo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Calcular multa automáticamente cuando cambia la fecha
    document.getElementById('return_date').addEventListener('change', function() {
        const returnDate = new Date(this.value);
        const expectedDate = new Date('{{ $rental->expected_return_date->format('Y-m-d\TH:i') }}');
        
        if (returnDate > expectedDate) {
            const diffTime = Math.abs(returnDate - expectedDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            const lateFee = diffDays * 1.50;
            
            document.getElementById('late_fee').value = lateFee.toFixed(2);
            document.getElementById('late_fee').readOnly = false;
        } else {
            document.getElementById('late_fee').value = '0.00';
            document.getElementById('late_fee').readOnly = true;
        }
    });
</script>
@endpush
@endsection