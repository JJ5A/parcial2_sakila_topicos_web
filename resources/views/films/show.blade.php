@extends('layouts.app')

@section('title', 'Detalles de la Película - Sakila')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-film me-2"></i>{{ $film->title }}
                </h4>
                <div class="btn-group">
                    <a href="{{ route('films.edit', $film->film_id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Editar
                    </a>
                    <form action="{{ route('films.destroy', $film->film_id) }}" 
                          method="POST" 
                          style="display: inline;"
                          onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta película?')">
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
                    <!-- Información Principal -->
                    <div class="col-md-8">
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">Información General</h5>
                            
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong class="text-muted">ID:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $film->film_id }}
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong class="text-muted">Título:</strong>
                                </div>
                                <div class="col-sm-9">
                                    <h5 class="mb-0">{{ $film->title }}</h5>
                                </div>
                            </div>
                            
                            @if($film->description)
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong class="text-muted">Descripción:</strong>
                                </div>
                                <div class="col-sm-9">
                                    <p class="mb-0">{{ $film->description }}</p>
                                </div>
                            </div>
                            @endif
                            
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong class="text-muted">Año:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $film->release_year ?? 'No especificado' }}
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong class="text-muted">Duración:</strong>
                                </div>
                                <div class="col-sm-9">
                                    {{ $film->formatted_length }}
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong class="text-muted">Clasificación:</strong>
                                </div>
                                <div class="col-sm-9">
                                    @if($film->rating)
                                        <span class="badge bg-secondary fs-6">{{ $film->rating }}</span>
                                        <small class="text-muted ms-2">
                                            {{ \App\Models\Film::getRatings()[$film->rating] ?? $film->rating }}
                                        </small>
                                    @else
                                        <span class="text-muted">No clasificada</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Idiomas -->
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">Idiomas</h5>
                            
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong class="text-muted">Idioma Principal:</strong>
                                </div>
                                <div class="col-sm-9">
                                    <i class="fas fa-language me-1"></i>
                                    {{ $film->language->name ?? 'No especificado' }}
                                </div>
                            </div>
                            
                            @if($film->original_language_id && $film->originalLanguage)
                            <div class="row mb-3">
                                <div class="col-sm-3">
                                    <strong class="text-muted">Idioma Original:</strong>
                                </div>
                                <div class="col-sm-9">
                                    <i class="fas fa-globe me-1"></i>
                                    {{ $film->originalLanguage->name }}
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Características Especiales -->
                        @if($film->special_features)
                        <div class="mb-4">
                            <h5 class="text-primary mb-3">Características Especiales</h5>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(explode(',', $film->special_features) as $feature)
                                    <span class="badge bg-info">{{ trim($feature) }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Panel Lateral -->
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-dollar-sign me-1"></i>Información de Alquiler
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong class="text-muted d-block">Precio de Alquiler</strong>
                                    <h4 class="text-success mb-0">{{ $film->formatted_rental_rate }}</h4>
                                    <small class="text-muted">por {{ $film->rental_duration }} día(s)</small>
                                </div>
                                
                                <div class="mb-3">
                                    <strong class="text-muted d-block">Costo de Reemplazo</strong>
                                    <h5 class="text-danger mb-0">{{ $film->formatted_replacement_cost }}</h5>
                                </div>
                                
                                <hr>
                                
                                <div class="mb-2">
                                    <strong class="text-muted d-block">Duración de Alquiler</strong>
                                    <span class="badge bg-primary">{{ $film->rental_duration }} día(s)</span>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Sistema -->
                        <div class="card mt-3 bg-light">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-info-circle me-1"></i>Información del Sistema
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-2">
                                    <strong class="text-muted d-block">Última Actualización</strong>
                                    @if($film->last_update)
                                        <small>{{ $film->last_update->format('d/m/Y H:i:s') }}</small>
                                        <br>
                                        <small class="text-muted">
                                            {{ $film->last_update->diffForHumans() }}
                                        </small>
                                    @else
                                        <small class="text-muted">No disponible</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <a href="{{ route('films.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Volver a la Lista
                    </a>
                    <div>
                        <a href="{{ route('films.edit', $film->film_id) }}" class="btn btn-primary me-2">
                            <i class="fas fa-edit me-1"></i>Editar Película
                        </a>
                        <form action="{{ route('films.destroy', $film->film_id) }}" 
                              method="POST" 
                              style="display: inline;"
                              onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta película?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="fas fa-trash me-1"></i>Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection