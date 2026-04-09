@extends('layouts.app')

@section('content')
<div class="container">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5><i class="bi bi-boxes me-2"></i>Gestion de l'inventaire</h5>

            <form method="GET" action="{{ route('stocks.index') }}" class="d-flex gap-2">
                <div class="input-group input-group-sm" style="min-width:240px;">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control"
                           placeholder="Rechercher par titre..."
                           value="{{ $search ?? '' }}">
                    <button class="btn btn-primary" type="submit">Rechercher</button>
                    @if($search)
                        <a href="{{ route('stocks.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="card-body p-0">
            @if(empty($stocks))
                <div class="p-4">
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Aucun stock disponible.
                    </div>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Titre</th>
                                <th class="text-center">Store</th>
                                <th class="text-center">Disponibles</th>
                                <th class="text-center">Total</th>
                                <th class="text-center">Loués</th>
                                <th class="d-none d-md-table-cell">Adresse</th>
                                <th class="d-none d-md-table-cell">District</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stocks as $stock)
                                <tr>
                                    <td>
                                        @if(!empty($stock['filmId']))
                                            <a href="{{ route('stocks.show', $stock['filmId']) }}"
                                               class="text-gold fw-bold text-decoration-none">
                                                <i class="bi bi-box-arrow-in-right me-1 opacity-50"></i>{{ $stock['title'] }}
                                            </a>
                                        @else
                                            <strong>{{ $stock['title'] ?? '—' }}</strong>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-shop me-1"></i>{{ $stock['storeId'] ?? $stock['store_id'] ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge"
                                              style="background:rgba(46,204,113,.15);color:#2ecc71;border:1px solid rgba(46,204,113,.3);font-size:.8rem;">
                                            <i class="bi bi-check-circle me-1"></i>{{ $stock['disponibles'] ?? $stock['availableCount'] ?? $stock['available'] ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="text-center text-muted">
                                        {{ $stock['total'] ?? $stock['totalStock'] ?? '—' }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge"
                                              style="background:rgba(243,156,18,.15);color:#f39c12;border:1px solid rgba(243,156,18,.3);font-size:.8rem;">
                                            <i class="bi bi-arrow-return-left me-1"></i>{{ $stock['loues'] ?? $stock['rentedCount'] ?? $stock['rented'] ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="text-muted d-none d-md-table-cell">{{ $stock['address'] ?? '—' }}</td>
                                    <td class="text-muted d-none d-md-table-cell">{{ $stock['district'] ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 d-flex justify-content-between align-items-center"
                     style="border-top:1px solid rgba(201,168,76,.1)">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong class="text-gold">{{ $totalItems }}</strong> résultat(s)
                    </small>

                    @if($totalPages > 1)
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item {{ $currentPage === 0 ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ route('stocks.index', ['page' => $currentPage - 1, 'search' => $search]) }}">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>

                                @php $start = max(0, $currentPage - 4); $end = min($totalPages - 1, $currentPage + 4); @endphp

                                @for($i = $start; $i <= $end; $i++)
                                    <li class="page-item {{ $i === $currentPage ? 'active' : '' }}">
                                        <a class="page-link" href="{{ route('stocks.index', ['page' => $i, 'search' => $search]) }}">{{ $i + 1 }}</a>
                                    </li>
                                @endfor

                                <li class="page-item {{ $currentPage >= $totalPages - 1 ? 'disabled' : '' }}">
                                    <a class="page-link" href="{{ route('stocks.index', ['page' => $currentPage + 1, 'search' => $search]) }}">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    @endif
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
