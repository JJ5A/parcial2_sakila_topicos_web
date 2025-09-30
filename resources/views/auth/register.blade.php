@extends('layouts.app')

@section('title', 'Registro de Empleado - Sistema Sakila')

@section('content')
<div class="container">
    <div class="row justify-content-center py-5">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-success text-white text-center py-4">
                    <h3 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>Registro de Empleado
                    </h3>
                    <p class="mb-0 mt-2">Sistema Sakila</p>
                </div>

                <div class="card-body p-5">
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Información:</strong> Una vez registrado, recibirás tu contraseña temporal por correo electrónico.
                    </div>

                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Información Personal -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">
                                    <i class="fas fa-user me-1"></i>Nombre <span class="text-danger">*</span>
                                </label>
                                <input id="first_name" type="text" 
                                       class="form-control @error('first_name') is-invalid @enderror" 
                                       name="first_name" 
                                       value="{{ old('first_name') }}" 
                                       required 
                                       maxlength="45"
                                       placeholder="Tu nombre">

                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="last_name" class="form-label">
                                    <i class="fas fa-user me-1"></i>Apellido <span class="text-danger">*</span>
                                </label>
                                <input id="last_name" type="text" 
                                       class="form-control @error('last_name') is-invalid @enderror" 
                                       name="last_name" 
                                       value="{{ old('last_name') }}" 
                                       required 
                                       maxlength="45"
                                       placeholder="Tu apellido">

                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Credenciales -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="username" class="form-label">
                                    <i class="fas fa-at me-1"></i>Nombre de Usuario <span class="text-danger">*</span>
                                </label>
                                <input id="username" type="text" 
                                       class="form-control @error('username') is-invalid @enderror" 
                                       name="username" 
                                       value="{{ old('username') }}" 
                                       required 
                                       maxlength="16"
                                       placeholder="usuario_ejemplo">
                                <small class="form-text text-muted">Máximo 16 caracteres</small>

                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Correo Electrónico <span class="text-danger">*</span>
                                </label>
                                <input id="email" type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       maxlength="50"
                                       placeholder="ejemplo@correo.com">

                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Dirección y Tienda -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="address_id" class="form-label">
                                    <i class="fas fa-map-marker-alt me-1"></i>Dirección <span class="text-danger">*</span>
                                </label>
                                <select id="address_id" 
                                        class="form-select @error('address_id') is-invalid @enderror" 
                                        name="address_id" 
                                        required>
                                    <option value="">Seleccionar dirección...</option>
                                    @foreach($addresses as $address)
                                        <option value="{{ $address->address_id }}" 
                                                {{ old('address_id') == $address->address_id ? 'selected' : '' }}>
                                            {{ $address->address }}, {{ $address->city->city }} - {{ $address->city->country->country }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('address_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="store_id" class="form-label">
                                    <i class="fas fa-store me-1"></i>Tienda Asignada <span class="text-danger">*</span>
                                </label>
                                <select id="store_id" 
                                        class="form-select @error('store_id') is-invalid @enderror" 
                                        name="store_id" 
                                        required>
                                    <option value="">Seleccionar tienda...</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->store_id }}" 
                                                {{ old('store_id') == $store->store_id ? 'selected' : '' }}>
                                            Tienda #{{ $store->store_id }} - {{ $store->address->city->city }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('store_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Foto de Perfil -->
                        <div class="mb-4">
                            <label for="picture" class="form-label">
                                <i class="fas fa-camera me-1"></i>Foto de Perfil (Opcional)
                            </label>
                            <input id="picture" type="file" 
                                   class="form-control @error('picture') is-invalid @enderror" 
                                   name="picture" 
                                   accept="image/*">
                            <small class="form-text text-muted">Máximo 2MB. Formatos: JPG, PNG, GIF</small>

                            @error('picture')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Términos y Condiciones -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    Acepto los términos y condiciones del sistema <span class="text-danger">*</span>
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-user-check me-2"></i>Registrarse como Empleado
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="text-muted mb-3">¿Ya tienes una cuenta?</p>
                        <a href="{{ route('login') }}" class="btn btn-outline-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted">
                    <i class="fas fa-envelope me-1"></i>Recibirás tu contraseña por correo electrónico
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        min-height: 100vh;
    }
    
    .card {
        border-radius: 15px;
        backdrop-filter: blur(10px);
    }
    
    .card-header {
        border-radius: 15px 15px 0 0 !important;
    }
    
    .form-control:focus,
    .form-select:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
    }
    
    .btn-success:hover {
        background: linear-gradient(135deg, #218838 0%, #1aa179 100%);
        transform: translateY(-1px);
    }
    
    .text-danger {
        font-weight: bold;
    }
</style>
@endsection
