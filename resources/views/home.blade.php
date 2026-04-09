@extends('layouts.app')

@section('content')
<div class="container">

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="mb-4">
        <h4 class="text-gold mb-1">
            <i class="bi bi-speedometer2 me-2"></i>Tableau de bord
        </h4>
        <p class="text-muted mb-0">Bienvenue, <strong class="text-gold">{{ Auth::user()->name }}</strong></p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('films.index') }}" class="stat-card">
                <div class="stat-icon"><i class="bi bi-film"></i></div>
                <div class="stat-label">Films</div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('stocks.index') }}" class="stat-card">
                <div class="stat-icon"><i class="bi bi-boxes"></i></div>
                <div class="stat-label">Inventaire</div>
            </a>
        </div>

        <div class="col-sm-6 col-lg-3">
            <a href="{{ route('films.create') }}" class="stat-card">
                <div class="stat-icon"><i class="bi bi-plus-circle"></i></div>
                <div class="stat-label">Ajouter un film</div>
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-info-circle me-2"></i>Accès rapide</h5>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('films.index') }}" class="btn btn-primary">
                    <i class="bi bi-film me-1"></i>Gérer les films
                </a>
                <a href="{{ route('films.create') }}" class="btn btn-secondary">
                    <i class="bi bi-plus-circle me-1"></i>Nouveau film
                </a>
                <a href="{{ route('stocks.index') }}" class="btn btn-secondary">
                    <i class="bi bi-boxes me-1"></i>Voir l'inventaire
                </a>

            </div>
        </div>
    </div>

</div>
@endsection
