@extends('layouts.app')

@section('title', 'Editar Actor - Sakila')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-user-edit me-2"></i>Editar Actor
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ route('actors.update', $actor->actor_id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">ID del Actor</label>
                            <div class="form-control-plaintext fw-bold">{{ $actor->actor_id }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">Última Actualización</label>
                            <div class="form-control-plaintext">
                                @if($actor->last_update)
                                    {{ $actor->last_update->format('d/m/Y H:i') }}
                                @else
                                    No disponible
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="first_name" class="form-label">Nombre *</label>
                        <input type="text" 
                               class="form-control @error('first_name') is-invalid @enderror" 
                               id="first_name" 
                               name="first_name" 
                               value="{{ old('first_name', $actor->first_name) }}"
                               maxlength="45"
                               required>
                        @error('first_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="last_name" class="form-label">Apellido *</label>
                        <input type="text" 
                               class="form-control @error('last_name') is-invalid @enderror" 
                               id="last_name" 
                               name="last_name" 
                               value="{{ old('last_name', $actor->last_name) }}"
                               maxlength="45"
                               required>
                        @error('last_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('actors.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Cancelar
                            </a>
                            <a href="{{ route('actors.show', $actor->actor_id) }}" class="btn btn-info">
                                <i class="fas fa-eye me-1"></i>Ver Detalles
                            </a>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Actualizar Actor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection