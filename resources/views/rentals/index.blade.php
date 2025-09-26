@extends('layouts.app')

@section('title', 'Sistema de Rentas - Sakila')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-video me-2"></i>Sistema de Rentas
            </h2>
            <div class="btn-group">
                <a href="{{ route('rentals.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Nueva Renta
                </a>
                <a href="{{ route('rentals.return') }}" class="btn btn-success">
                    <i class="fas fa-undo me-1"></i>Procesar Devolución
                </a>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>{{ $stats['active_rentals'] }}</h4>
                                <p class="mb-0">Rentas Activas</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-play fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('rentals.index') }}?status=active" class="text-white text-decoration-none">
                            Ver todas <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>{{ $stats['overdue_rentals'] }}</h4>
                                <p class="mb-0">Rentas Atrasadas</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('rentals.overdue') }}" class="text-white text-decoration-none">
                            Ver todas <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>{{ $stats['total_rentals_today'] }}</h4>
                                <p class="mb-0">Rentas Hoy</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-calendar-day fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4>{{ $stats['available_inventory'] }}</h4>
                                <p class="mb-0">Ítems Disponibles</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rentas Recientes -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Rentas Recientes
                </h5>
                <a href="{{ route('rentals.overdue') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-chart-bar me-1"></i>Ver Reportes
                </a>
            </div>
            <div class="card-body">
                @if($recent_rentals->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>ID Renta</th>
                                    <th>Cliente</th>
                                    <th>Película</th>
                                    <th>Fecha Renta</th>
                                    <th>Empleado</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_rentals as $rental)
                                    <tr>
                                        <td>
                                            <strong>#{{ $rental->rental_id }}</strong>
                                        </td>
                                        <td>{{ $rental->customer->full_name }}</td>
                                        <td>
                                            <strong>{{ $rental->inventory->film->title }}</strong>
                                        </td>
                                        <td>{{ $rental->rental_date->format('d/m/Y H:i') }}</td>
                                        <td>{{ $rental->staff->full_name }}</td>
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
                                            <a href="{{ route('rentals.show', $rental->rental_id) }}" 
                                               class="btn btn-outline-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-video fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay rentas registradas</h5>
                        <p class="text-muted">Comienza procesando tu primera renta.</p>
                        <a href="{{ route('rentals.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Nueva Renta
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection