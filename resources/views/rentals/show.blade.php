@extends('layouts.app')

@section('title', 'Renta Procesada - Sakila')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">
                    <i class="fas fa-check-circle me-2"></i>Renta Procesada Exitosamente
                </h4>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Detalles de la Renta
                        </h5>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ID de Renta:</strong></td>
                                <td>{{ $rental->rental_id }}</td>
                            </tr>
                            <tr>
                                <td><strong>Fecha de Renta:</strong></td>
                                <td>{{ $rental->rental_date->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Cliente:</strong></td>
                                <td>{{ $rental->customer->full_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Empleado:</strong></td>
                                <td>{{ $rental->staff->full_name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Película:</strong></td>
                                <td>{{ $rental->inventory->film->title }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tienda:</strong></td>
                                <td>{{ $rental->inventory->store_id }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-success mb-3">
                            <i class="fas fa-dollar-sign me-2"></i>Información de Pago
                        </h5>
                        @if($rental->payment)
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Monto:</strong></td>
                                    <td class="text-success fw-bold">${{ number_format($rental->payment->amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha de Pago:</strong></td>
                                    <td>{{ $rental->payment->payment_date->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>ID de Pago:</strong></td>
                                    <td>{{ $rental->payment->payment_id }}</td>
                                </tr>
                            </table>
                        @endif
                        
                        <div class="alert alert-info mt-3">
                            <h6><i class="fas fa-calendar-alt me-2"></i>Fecha de Devolución</h6>
                            <p class="mb-0">
                                <strong>{{ $rental->due_date->format('d/m/Y') }}</strong>
                                <br><small class="text-muted">
                                    ({{ $rental->inventory->film->rental_duration }} días después de la renta)
                                </small>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Información Importante</h6>
                            <ul class="mb-0">
                                <li>La película debe devolverse antes de la fecha indicada</li>
                                <li>Se aplicarán cargos por demora después de la fecha de vencimiento</li>
                                <li>Conserve este comprobante como referencia</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('rentals.index') }}" class="btn btn-secondary">
                        <i class="fas fa-list me-1"></i>Ver Todas las Rentas
                    </a>
                    <a href="{{ route('rentals.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nueva Renta
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection