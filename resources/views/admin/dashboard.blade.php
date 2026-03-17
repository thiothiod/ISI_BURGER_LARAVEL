<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ISI BURGER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .navbar { background: #1d1d1d !important; }
        .navbar-brand { color: #e63946 !important; font-weight: 900; font-size: 1.5rem; }
        .card { border: none; box-shadow: 0 2px 12px rgba(0,0,0,.08); border-radius: 12px; }
        .badge-en_attente { background: #ffc107; color: #000; }
        .badge-en_preparation { background: #0d6efd; }
        .badge-prete { background: #198754; }
        .badge-payee { background: #6f42c1; }
        .badge-annulee { background: #dc3545; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark mb-4">
        <div class="container-fluid">
            <span class="navbar-brand">🍔 ISI BURGER</span>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-burger me-1"></i>Produits
                </a>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-clipboard-list me-1"></i>Commandes
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm">Déconnexion</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Tableau de Bord</h2>
                <p class="text-muted">{{ now()->format('d/m/Y') }}</p>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card h-100 border-start border-warning border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small text-uppercase fw-semibold">Commandes en cours</p>
                                <h2 class="fw-bold mb-0 text-warning">{{ $ordersEnCours }}</h2>
                                <small class="text-muted">Aujourd'hui</small>
                            </div>
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 border-start border-success border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small text-uppercase fw-semibold">Commandes validées</p>
                                <h2 class="fw-bold mb-0 text-success">{{ $ordersValidees }}</h2>
                                <small class="text-muted">Aujourd'hui</small>
                            </div>
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 border-start border-primary border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small text-uppercase fw-semibold">Recettes du jour</p>
                                <h2 class="fw-bold mb-0 text-primary">{{ number_format($recettesJournalieres, 0, ',', ' ') }} F</h2>
                                <small class="text-muted">Paiements reçus</small>
                            </div>
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-money-bill-wave fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card h-100 border-start border-danger border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small text-uppercase fw-semibold">Total Produits</p>
                                <h2 class="fw-bold mb-0 text-danger">{{ $totalProducts }}</h2>
                                <small class="text-muted">En catalogue</small>
                            </div>
                            <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-burger fa-2x text-danger"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="row g-4 mb-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-chart-bar text-primary me-2"></i>
                            Commandes par Mois
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="ordersChart" height="100"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="fw-bold mb-0">
                            <i class="fas fa-chart-pie text-danger me-2"></i>
                            Produits par Catégorie
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="categoryChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dernières commandes --}}
        <div class="card">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center pt-3">
                <h5 class="fw-bold mb-0">
                    <i class="fas fa-list text-muted me-2"></i>Dernières Commandes
                </h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-danger">
                    Voir tout
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Référence</th>
                                <th>Client</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td><code class="text-danger">{{ $order->reference }}</code></td>
                                <td>{{ $order->user->name }}</td>
                                <td><strong>{{ number_format($order->total_amount, 0, ',', ' ') }} F</strong></td>
                                <td>
                                    <span class="badge badge-{{ $order->status }} rounded-pill">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('d/m H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Aucune commande
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Graphique commandes par mois
        const ordersData = @json($ordersParMois);
        new Chart(document.getElementById('ordersChart'), {
            type: 'bar',
            data: {
                labels: ordersData.map(d => d.label),
                datasets: [{
                    label: 'Commandes',
                    data: ordersData.map(d => d.total),
                    backgroundColor: 'rgba(230, 57, 70, 0.8)',
                    borderColor: '#e63946',
                    borderWidth: 2,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });

        // Graphique produits par catégorie
        const catData = @json($produitsParCategorie);
        const colors  = ['#e63946', '#f4a261', '#2a9d8f', '#457b9d', '#6d6875', '#e9c46a'];
        new Chart(document.getElementById('categoryChart'), {
            type: 'doughnut',
            data: {
                labels: catData.map(d => d.label),
                datasets: [{
                    data: catData.map(d => d.count),
                    backgroundColor: colors.slice(0, catData.length),
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true } }
                }
            }
        });
    </script>
</body>
</html>