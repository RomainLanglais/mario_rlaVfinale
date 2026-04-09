@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="bi bi-pencil me-2"></i>Modifier le film</h5>
                    <a href="{{ route('films.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Retour
                    </a>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('films.update', $film['filmId']) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Titre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title', $film['title'] ?? '') }}">
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description', $film['description'] ?? '') }}</textarea>
                            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="releaseYear" class="form-label">Année de sortie</label>
                                <input type="number" class="form-control @error('releaseYear') is-invalid @enderror"
                                       id="releaseYear" name="releaseYear"
                                       value="{{ old('releaseYear', $film['releaseYear'] ?? '') }}"
                                       min="1888" max="2100">
                                @error('releaseYear') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="originalLanguageId" class="form-label">ID Langue <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('originalLanguageId') is-invalid @enderror"
                                       id="originalLanguageId" name="originalLanguageId"
                                       value="{{ old('originalLanguageId', $film['originalLanguageId'] ?? 1) }}" min="1">
                                @error('originalLanguageId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="rentalDuration" class="form-label">Durée de location (jours) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('rentalDuration') is-invalid @enderror"
                                       id="rentalDuration" name="rentalDuration"
                                       value="{{ old('rentalDuration', $film['rentalDuration'] ?? 3) }}" min="1">
                                @error('rentalDuration') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="length" class="form-label">Durée (minutes)</label>
                                <input type="number" class="form-control @error('length') is-invalid @enderror"
                                       id="length" name="length"
                                       value="{{ old('length', $film['length'] ?? '') }}" min="1">
                                @error('length') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="rating" class="form-label">Classification</label>
                            <select class="form-select @error('rating') is-invalid @enderror" id="rating" name="rating">
                                <option value="">— Sélectionner —</option>
                                @foreach(['G', 'PG', 'PG-13', 'R', 'NC-17'] as $r)
                                    <option value="{{ $r }}" {{ old('rating', $film['rating'] ?? '') === $r ? 'selected' : '' }}>{{ $r }}</option>
                                @endforeach
                            </select>
                            @error('rating') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-1"></i>Enregistrer les modifications
                            </button>
                            <a href="{{ route('films.index') }}" class="btn btn-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
