@extends('layouts.app')

@section('title', 'Detalles del Actor - Sakila')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-user me-2"></i>Detalles del Actor
                </h4>
                <div class="btn-group">
                    <a href="{{ route('actors.edit', $actor->actor_id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Editar
                    </a>
                    <form action="{{ route('actors.destroy', $actor->actor_id) }}" 
                          method="POST" 
                          style="display: inline;"
                          onsubmit="return confirm('¿Estás seguro de que quieres eliminar este actor?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash me-1"></i>Eliminar
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">ID del Actor</label>
                            <div class="form-control-plaintext fw-bold">{{ $actor->actor_id }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Nombre Completo</label>
                            <div class="form-control-plaintext fw-bold fs-5">
                                {{ $actor->first_name }} {{ $actor->last_name }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Nombre</label>
                            <div class="form-control-plaintext">{{ $actor->first_name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label text-muted">Apellido</label>
                            <div class="form-control-plaintext">{{ $actor->last_name }}</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label class="form-label text-muted">Última Actualización</label>
                            <div class="form-control-plaintext">
                                @if($actor->last_update)
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $actor->last_update->format('d/m/Y H:i:s') }}
                                    <small class="text-muted">
                                        ({{ $actor->last_update->diffForHumans() }})
                                    </small>
                                @else
                                    <span class="text-muted">No disponible</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('actors.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Volver a la Lista
                    </a>
                    <a href="{{ route('actors.edit', $actor->actor_id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i>Editar Actor
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection