@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="bi bi-film me-2"></i>Détails du film</h5>
                    <a href="{{ route('films.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Retour
                    </a>
                </div>

                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h3 class="text-gold mb-1">{{ $film['title'] ?? 'Sans titre' }}</h3>
                            <p class="text-muted mb-0">{{ $film['description'] ?? 'Aucune description disponible.' }}</p>
                        </div>
                        @if(isset($film['rating']))
                            <span class="rating-badge rating-{{ $film['rating'] }} ms-3" style="white-space:nowrap;font-size:1rem;padding:0.4em 0.9em;">
                                {{ $film['rating'] }}
                            </span>
                        @endif
                    </div>

                    <hr class="section-divider">

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="p-3 rounded" style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06)">
                                <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.5px">ID</div>
                                <div class="fw-600">{{ $film['filmId'] ?? $film['id'] ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded" style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06)">
                                <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.5px">Année de sortie</div>
                                <div class="fw-600">{{ $film['releaseYear'] ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded" style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06)">
                                <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.5px">Durée</div>
                                <div class="fw-600">{{ $film['length'] ?? '—' }} minutes</div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 rounded" style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06)">
                                <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.5px">Durée de location</div>
                                <div class="fw-600">{{ $film['rentalDuration'] ?? '—' }} jours</div>
                            </div>
                        </div>
                    </div>

                    <hr class="section-divider">

                    <div class="d-flex gap-2">
                        <a href="{{ route('films.edit', $film['filmId'] ?? $film['id']) }}" class="btn btn-warning">
                            <i class="bi bi-pencil me-1"></i>Modifier
                        </a>
                        <form action="{{ route('films.destroy', $film['filmId'] ?? $film['id']) }}"
                              method="POST"
                              onsubmit="return confirm('Supprimer ce film ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash me-1"></i>Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
