@extends('layouts.app')

@section('title', 'Iniciar Sesión - Sistema Sakila')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0">
                        <i class="fas fa-film me-2"></i>Sistema Sakila
                    </h3>
                    <p class="mb-0 mt-2">Iniciar Sesión</p>
                </div>

                <div class="card-body p-5">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="username" class="form-label">
                                <i class="fas fa-user me-1"></i>Nombre de Usuario
                            </label>
                            <input id="username" type="text" 
                                   class="form-control form-control-lg @error('username') is-invalid @enderror" 
                                   name="username" 
                                   value="{{ old('username') }}" 
                                   required 
                                   autocomplete="username" 
                                   autofocus
                                   placeholder="Ingresa tu nombre de usuario">

                            @error('username')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i>Contraseña
                            </label>
                            <div class="input-group">
                                <input id="password" type="password" 
                                       class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password"
                                       placeholder="Ingresa tu contraseña">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>

                            @error('password')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" 
                                       {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    Mantener sesión iniciada
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="text-muted mb-3">¿No tienes una cuenta?</p>
                        <a href="{{ route('register') }}" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus me-2"></i>Registrarse como Empleado
                        </a>
                    </div>

                    @if (Route::has('password.request'))
                        <div class="text-center mt-3">
                            <a class="text-decoration-none text-muted" href="{{ route('password.request') }}">
                                <i class="fas fa-question-circle me-1"></i>¿Olvidaste tu contraseña?
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted">
                    <i class="fas fa-shield-alt me-1"></i>Acceso solo para empleados autorizados
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }
    
    .card {
        border-radius: 15px;
        backdrop-filter: blur(10px);
    }
    
    .card-header {
        border-radius: 15px 15px 0 0 !important;
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        transform: translateY(-1px);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = this.querySelector('i');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    }
});
</script>
@endsection
