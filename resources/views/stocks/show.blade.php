@extends('layouts.app')

@section('content')
<div class="container">

    {{-- Alertes --}}
    @if(session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- En-tête --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="text-gold mb-0">
                <i class="bi bi-boxes me-2"></i>{{ $stockData['title'] }}
            </h4>
            <small class="text-muted">Film #{{ $stockData['filmId'] }}</small>
        </div>
    </div>

    {{-- Switcher de store --}}
    <div class="mb-4" style="max-width:360px;">
        <label class="form-label"><i class="bi bi-shop me-1"></i>Store</label>
        <select class="form-select" onchange="window.location.href=this.value">
            @foreach($stockData['stores'] as $store)
                @php
                    $dvdCount = count($store['dvds']);
                    $url = route('stocks.show', ['filmId' => $stockData['filmId'], 'store' => $store['storeId']]);
                @endphp
                <option value="{{ $url }}" {{ (string)$store['storeId'] === (string)$activeStore ? 'selected' : '' }}>
                    Store #{{ $store['storeId'] }}{{ $store['address'] ? ' — '.$store['address'].($store['district'] ? ', '.$store['district'] : '') : '' }} ({{ $dvdCount }} DVD{{ $dvdCount > 1 ? 's' : '' }})
                </option>
            @endforeach
        </select>
    </div>

    {{-- Store actif --}}
    @php
        $currentStore = collect($stockData['stores'])->firstWhere('storeId', (int)$activeStore);
    @endphp

    @if($currentStore)
        {{-- Résumé du store --}}
        @php
            $total     = count($currentStore['dvds']);
            $rented    = collect($currentStore['dvds'])->where('isRented', true)->count();
            $available = $total - $rented;
        @endphp
        <div class="row g-3 mb-4">
            <div class="col-sm-4">
                <div class="card text-center py-3">
                    <div style="font-size:1.8rem;color:#c9a84c;">{{ $total }}</div>
                    <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.6px;">Total DVDs</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card text-center py-3">
                    <div style="font-size:1.8rem;color:#2ecc71;">{{ $available }}</div>
                    <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.6px;">Disponibles</div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card text-center py-3">
                    <div style="font-size:1.8rem;color:#f39c12;">{{ $rented }}</div>
                    <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.6px;">Loués</div>
                </div>
            </div>
        </div>

        {{-- Ajouter un DVD --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="bi bi-plus-circle me-2"></i>Ajouter un DVD — Store #{{ $currentStore['storeId'] }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('stocks.addDvd', $stockData['filmId']) }}"
                      class="d-flex align-items-center gap-3">
                    @csrf
                    <input type="hidden" name="store_id" value="{{ $currentStore['storeId'] }}">
                    <span class="text-muted" style="font-size:.9rem;">
                        <i class="bi bi-shop me-1"></i>
                        Store #{{ $currentStore['storeId'] }}
                        @if($currentStore['address']) — {{ $currentStore['address'] }}{{ $currentStore['district'] ? ', '.$currentStore['district'] : '' }} @endif
                    </span>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>Ajouter un DVD
                    </button>
                </form>
            </div>
        </div>

        {{-- Liste des DVDs --}}
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-shop me-2"></i>Store #{{ $currentStore['storeId'] }}
                    @if($currentStore['address'])
                        <span class="text-muted fw-normal" style="font-size:.85rem;">
                            — {{ $currentStore['address'] }}{{ $currentStore['district'] ? ', '.$currentStore['district'] : '' }}
                        </span>
                    @endif
                </h5>
                <span class="badge bg-secondary">{{ $total }} DVD(s)</span>
            </div>

            @if($total === 0)
                <div class="card-body">
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>Aucun DVD dans ce store.
                    </div>
                </div>
            @else
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#Inventaire</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($currentStore['dvds'] as $dvd)
                                    <tr>
                                        <td class="text-muted">{{ $dvd['inventoryId'] }}</td>
                                        <td class="text-center">
                                            @if($dvd['isRented'])
                                                <span class="badge" style="background:rgba(243,156,18,.15);color:#f39c12;border:1px solid rgba(243,156,18,.3);">
                                                    <i class="bi bi-arrow-return-left me-1"></i>Loué
                                                </span>
                                            @else
                                                <span class="badge" style="background:rgba(46,204,113,.15);color:#2ecc71;border:1px solid rgba(46,204,113,.3);">
                                                    <i class="bi bi-check-circle me-1"></i>Disponible
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if(!$dvd['isRented'])
                                                <form method="POST"
                                                      action="{{ route('stocks.removeDvd', [$stockData['filmId'], $dvd['inventoryId']]) }}?store={{ $activeStore }}"
                                                      onsubmit="return confirm('Supprimer ce DVD de l\'inventaire ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm">
                                                        <i class="bi bi-trash me-1"></i>Supprimer
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-danger btn-sm" disabled title="DVD loué">
                                                    <i class="bi bi-trash me-1"></i>Supprimer
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
