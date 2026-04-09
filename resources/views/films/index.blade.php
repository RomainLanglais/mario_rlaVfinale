@extends('layouts.app')

@section('content')
<div class="container">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="bi bi-film me-2"></i>Catalogue de films</h5>
            <a href="{{ route('films.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i>Ajouter un film
            </a>
        </div>

        <div class="card-body p-0">
            @if (empty($films))
                <div class="p-4">
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Aucun film disponible.
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:60px">ID</th>
                                <th>Titre</th>
                                <th class="d-none d-md-table-cell">Description</th>
                                <th style="width:80px">Année</th>
                                <th style="width:90px">Durée</th>
                                <th style="width:80px" class="text-center">Note</th>
                                <th style="width:110px" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($films as $film)
                                @php $rating = $film['rating'] ?? null; @endphp
                                <tr>
                                    <td class="text-muted">{{ $film['filmId'] ?? $film['id'] ?? '—' }}</td>
                                    <td><strong>{{ $film['title'] ?? 'Sans titre' }}</strong></td>
                                    <td class="text-muted d-none d-md-table-cell">
                                        {{ Str::limit($film['description'] ?? '—', 75) }}
                                    </td>
                                    <td class="text-muted">{{ $film['releaseYear'] ?? '—' }}</td>
                                    <td class="text-muted">{{ $film['length'] ?? '—' }} min</td>
                                    <td class="text-center">
                                        @if($rating)
                                            <span class="rating-badge rating-{{ $rating }}">{{ $rating }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('films.show', $film['filmId'] ?? $film['id']) }}"
                                               class="btn btn-info" title="Voir">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('films.edit', $film['filmId'] ?? $film['id']) }}"
                                               class="btn btn-warning" title="Modifier">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('films.destroy', $film['filmId'] ?? $film['id']) }}"
                                                  method="POST" style="display:inline;"
                                                  onsubmit="return confirm('Supprimer ce film ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Supprimer">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-top border-opacity-10" style="border-color: rgba(201,168,76,.15) !important;">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong class="text-gold">{{ count($films) }}</strong> film(s) au catalogue
                    </small>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
