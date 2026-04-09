@extends('layouts.app')

@section('content')
<div class="container">

    <div class="card">
        <div class="card-header">
            <h5><i class="bi bi-receipt me-2"></i>Locations par client</h5>
        </div>

        <div class="card-body">
            <div class="mb-4">
                <label for="customerSelect" class="form-label">
                    <i class="bi bi-person-fill me-1"></i>Sélectionner un client
                </label>
                <select id="customerSelect" class="form-select" style="width:100%;">
                    <option value="">— Rechercher un client —</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer['customerId'] }}">
                            {{ $customer['firstName'] }} {{ $customer['lastName'] }} — {{ $customer['email'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Loader -->
            <div id="loader" class="text-center py-4" style="display:none;">
                <div class="spinner-border text-warning" role="status" style="width:2rem;height:2rem;">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <div class="text-muted mt-2" style="font-size:.85rem;">Chargement des locations...</div>
            </div>

            <!-- Message aucune location -->
            <div id="noRentalsMessage" class="alert alert-info" style="display:none;">
                <i class="bi bi-info-circle me-2"></i>Aucune location pour ce client.
            </div>

            <!-- Tableau des locations -->
            <div id="rentalsContainer" style="display:none;">
                <hr class="section-divider">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 id="customerName" class="text-gold mb-0"></h6>
                    <small class="text-muted">
                        <strong id="rentalsCount" class="text-gold">0</strong> location(s)
                    </small>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:80px">ID</th>
                                <th>Film</th>
                                <th>Date de location</th>
                                <th>Date de retour</th>
                                <th style="width:120px" class="text-center">Statut</th>
                            </tr>
                        </thead>
                        <tbody id="rentalsTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet"/>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
/* Select2 dark theme overrides */
.select2-container--bootstrap-5 .select2-selection {
    background-color: #16213e !important;
    border-color: #2a2a4a !important;
    color: #e8e8f0 !important;
}
.select2-container--bootstrap-5 .select2-selection__rendered { color: #e8e8f0 !important; }
.select2-container--bootstrap-5 .select2-selection__placeholder { color: #55557a !important; }
.select2-dropdown {
    background-color: #1a1a2e !important;
    border-color: rgba(201,168,76,.2) !important;
}
.select2-container--bootstrap-5 .select2-results__option { color: #e8e8f0 !important; }
.select2-container--bootstrap-5 .select2-results__option--highlighted {
    background-color: rgba(201,168,76,.12) !important;
    color: #c9a84c !important;
}
.select2-container--bootstrap-5 .select2-search__field {
    background-color: #16213e !important;
    border-color: #2a2a4a !important;
    color: #e8e8f0 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#customerSelect').select2({
        theme: 'bootstrap-5',
        placeholder: '— Rechercher un client —',
        allowClear: true
    });

    $('#customerSelect').on('change', function () {
        const customerId  = $(this).val();
        const customerText = $(this).find('option:selected').text();

        if (!customerId) {
            document.getElementById('rentalsContainer').style.display = 'none';
            document.getElementById('noRentalsMessage').style.display = 'none';
            return;
        }

        document.getElementById('loader').style.display = 'block';
        document.getElementById('rentalsContainer').style.display = 'none';
        document.getElementById('noRentalsMessage').style.display = 'none';

        fetch('/rentals/customer/' + customerId)
            .then(r => r.json())
            .then(data => {
                document.getElementById('loader').style.display = 'none';

                if (data.length === 0) {
                    document.getElementById('noRentalsMessage').style.display = 'block';
                    return;
                }

                document.getElementById('customerName').textContent = 'Locations de : ' + customerText;

                const tbody = document.getElementById('rentalsTableBody');
                tbody.innerHTML = '';

                data.forEach(function (rental) {
                    const tr = document.createElement('tr');

                    let statusBadge = '<span class="badge" style="background:rgba(108,117,125,.2);color:#aaa;border:1px solid rgba(108,117,125,.3)">N/A</span>';
                    if (rental.status) {
                        const label = rental.status.statusLabel || 'Inconnu';
                        if (rental.statusId === 1) {
                            statusBadge = '<span class="badge" style="background:rgba(46,204,113,.15);color:#2ecc71;border:1px solid rgba(46,204,113,.3)">' + label + '</span>';
                        } else if (rental.statusId === 2) {
                            statusBadge = '<span class="badge" style="background:rgba(243,156,18,.15);color:#f39c12;border:1px solid rgba(243,156,18,.3)">' + label + '</span>';
                        } else if (rental.statusId === 3) {
                            statusBadge = '<span class="badge" style="background:rgba(52,152,219,.15);color:#3498db;border:1px solid rgba(52,152,219,.3)">' + label + '</span>';
                        }
                    }

                    let filmTitle = 'N/A';
                    if (rental.inventory && rental.inventory.film) {
                        filmTitle = rental.inventory.film.title;
                    }

                    const rentalDate = rental.rentalDate ? new Date(rental.rentalDate).toLocaleDateString('fr-FR') : '—';
                    const returnDate = rental.returnDate ? new Date(rental.returnDate).toLocaleDateString('fr-FR') : '—';

                    tr.innerHTML =
                        '<td class="text-muted">' + rental.rentalId + '</td>' +
                        '<td><strong>' + filmTitle + '</strong></td>' +
                        '<td>' + rentalDate + '</td>' +
                        '<td>' + returnDate + '</td>' +
                        '<td class="text-center">' + statusBadge + '</td>';

                    tbody.appendChild(tr);
                });

                document.getElementById('rentalsCount').textContent = data.length;
                document.getElementById('rentalsContainer').style.display = 'block';
            })
            .catch(err => {
                console.error(err);
                document.getElementById('loader').style.display = 'none';
                alert('Erreur lors de la récupération des locations.');
            });
    });
});
</script>
@endsection
