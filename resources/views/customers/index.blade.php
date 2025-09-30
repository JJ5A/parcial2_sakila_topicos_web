@extends('layouts.app')

@section('title', 'Gestión de Clientes - Sistema Sakila')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-0">
                        <i class="fas fa-users text-primary me-2"></i>Gestión de Clientes
                    </h1>
                    <p class="text-muted">Administración completa de clientes</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('rentals.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                    </a>
                    <a href="{{ route('rentals.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nueva Renta
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de Clientes -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Clientes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_customers'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-success shadow">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Activos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_customers'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-success"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Inactivos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['inactive_customers'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-times fa-2x text-warning"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Con Rentas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['customers_with_rentals'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('customers.index') }}" class="row g-3">
                        <div class="col-md-6">
                            <label for="search" class="form-label">Buscar cliente</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="search" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Nombre, apellido o email...">
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Estado</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Todos los estados</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activos</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivos</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Clientes -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-list me-2"></i>Lista de Clientes
                        </h6>
                        <div class="text-muted">
                            Mostrando {{ $customers->firstItem() ?? 0 }} - {{ $customers->lastItem() ?? 0 }} 
                            de {{ $customers->total() }} resultados
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($customers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Cliente</th>
                                        <th>Email</th>
                                        <th>Dirección</th>
                                        <th>Total Rentas</th>
                                        <th>Activas</th>
                                        <th>Atrasadas</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customers as $customer)
                                        <tr>
                                            <td>
                                                <strong>#{{ $customer->customer_id }}</strong>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $customer->first_name }} {{ $customer->last_name }}</strong>
                                                    @if($customer->address)
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $customer->address->city->city ?? 'N/A' }}, 
                                                            {{ $customer->address->city->country->country ?? 'N/A' }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <a href="mailto:{{ $customer->email }}" class="text-decoration-none">
                                                    {{ $customer->email }}
                                                </a>
                                            </td>
                                            <td>
                                                @if($customer->address)
                                                    <small>
                                                        {{ $customer->address->address }}<br>
                                                        {{ $customer->address->district }}
                                                    </small>
                                                @else
                                                    <span class="text-muted">No registrada</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $customer->total_rentals }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($customer->active_rentals > 0)
                                                    <span class="badge bg-primary">{{ $customer->active_rentals }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($customer->overdue_rentals > 0)
                                                    <span class="badge bg-danger">{{ $customer->overdue_rentals }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($customer->active)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Activo
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-pause me-1"></i>Inactivo
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('customers.show', $customer) }}" 
                                                       class="btn btn-sm btn-outline-primary"
                                                       title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($customer->active_rentals > 0)
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-info"
                                                                title="Ver rentas activas">
                                                            <i class="fas fa-list"></i>
                                                        </button>
                                                    @endif
                                                    @if($customer->overdue_rentals > 0)
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger"
                                                                title="Contactar por atraso">
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
                            {{ $customers->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No se encontraron clientes</h4>
                            <p class="text-muted">
                                @if(request()->hasAny(['search', 'status']))
                                    No hay clientes que coincidan con los filtros aplicados.
                                @else
                                    No hay clientes registrados en el sistema.
                                @endif
                            </p>
                            @if(request()->hasAny(['search', 'status']))
                                <a href="{{ route('customers.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-times me-1"></i>Limpiar filtros
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
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
</style>
@endsection