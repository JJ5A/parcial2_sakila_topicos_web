@extends('layouts.app')

@section('title', 'Crear Película - Sakila')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Crear Nueva Película
                </h4>
            </div>
            <div class="card-body">
                <form action="{{ route('films.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <!-- Información Básica -->
                        <div class="col-md-6">
                            <h5 class="text-muted mb-3">Información Básica</h5>
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Título *</label>
                                <input type="text" 
                                       class="form-control @error('title') is-invalid @enderror" 
                                       id="title" 
                                       name="title" 
                                       value="{{ old('title') }}"
                                       maxlength="128"
                                       required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Descripción</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="4">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="release_year" class="form-label">Año de Estreno</label>
                                        <input type="number" 
                                               class="form-control @error('release_year') is-invalid @enderror" 
                                               id="release_year" 
                                               name="release_year" 
                                               value="{{ old('release_year') }}"
                                               min="1888"
                                               max="{{ date('Y') + 5 }}">
                                        @error('release_year')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="length" class="form-label">Duración (minutos)</label>
                                        <input type="number" 
                                               class="form-control @error('length') is-invalid @enderror" 
                                               id="length" 
                                               name="length" 
                                               value="{{ old('length') }}"
                                               min="1"
                                               placeholder="ej. 120">
                                        @error('length')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="rating" class="form-label">Clasificación</label>
                                <select class="form-select @error('rating') is-invalid @enderror" id="rating" name="rating">
                                    <option value="">Seleccionar clasificación</option>
                                    @foreach($ratings as $value => $label)
                                        <option value="{{ $value }}" {{ old('rating') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Información de Alquiler y Idiomas -->
                        <div class="col-md-6">
                            <h5 class="text-muted mb-3">Información de Alquiler e Idiomas</h5>
                            
                            <div class="mb-3">
                                <label for="language_id" class="form-label">Idioma Principal *</label>
                                <select class="form-select @error('language_id') is-invalid @enderror" 
                                        id="language_id" 
                                        name="language_id" 
                                        required>
                                    <option value="">Seleccionar idioma</option>
                                    @foreach($languages as $language)
                                        <option value="{{ $language->language_id }}" 
                                                {{ old('language_id') == $language->language_id ? 'selected' : '' }}>
                                            {{ $language->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('language_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="original_language_id" class="form-label">Idioma Original</label>
                                <select class="form-select @error('original_language_id') is-invalid @enderror" 
                                        id="original_language_id" 
                                        name="original_language_id">
                                    <option value="">Sin idioma original específico</option>
                                    @foreach($languages as $language)
                                        <option value="{{ $language->language_id }}" 
                                                {{ old('original_language_id') == $language->language_id ? 'selected' : '' }}>
                                            {{ $language->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('original_language_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="rental_duration" class="form-label">Días de Alquiler *</label>
                                        <input type="number" 
                                               class="form-control @error('rental_duration') is-invalid @enderror" 
                                               id="rental_duration" 
                                               name="rental_duration" 
                                               value="{{ old('rental_duration', 3) }}"
                                               min="1"
                                               max="255"
                                               required>
                                        @error('rental_duration')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="rental_rate" class="form-label">Precio de Alquiler ($) *</label>
                                        <input type="number" 
                                               class="form-control @error('rental_rate') is-invalid @enderror" 
                                               id="rental_rate" 
                                               name="rental_rate" 
                                               value="{{ old('rental_rate', '4.99') }}"
                                               step="0.01"
                                               min="0"
                                               max="99.99"
                                               required>
                                        @error('rental_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="replacement_cost" class="form-label">Costo de Reemplazo ($) *</label>
                                <input type="number" 
                                       class="form-control @error('replacement_cost') is-invalid @enderror" 
                                       id="replacement_cost" 
                                       name="replacement_cost" 
                                       value="{{ old('replacement_cost', '19.99') }}"
                                       step="0.01"
                                       min="0"
                                       max="999.99"
                                       required>
                                @error('replacement_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Características Especiales</label>
                                <div class="row">
                                    @foreach($specialFeatures as $value => $label)
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       name="special_features[]" 
                                                       value="{{ $value }}" 
                                                       id="feature_{{ $loop->index }}"
                                                       {{ in_array($value, old('special_features', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="feature_{{ $loop->index }}">
                                                    {{ $label }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('special_features')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('films.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Guardar Película
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection