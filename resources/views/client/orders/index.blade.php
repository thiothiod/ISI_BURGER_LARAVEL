<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Commandes - ISI BURGER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .navbar { background: #1d1d1d !important; }
        .navbar-brand { color: #e63946 !important; font-weight: 900; }
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
            <div class="d-flex align-items-center gap-3">
                <span class="text-white">{{ auth()->user()->name }}</span>
                <a href="{{ route('client.catalogue') }}" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-burger me-1"></i>Catalogue
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
                <h2 class="fw-bold mb-0">📦 Mes Commandes</h2>
                <p class="text-muted">{{ $orders->total() }} commande(s)</p>
            </div>
            <a href="{{ route('client.catalogue') }}" class="btn btn-danger">
                <i class="fas fa-plus me-2"></i>Nouvelle commande
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($orders->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Vous n'avez pas encore de commandes</h5>
                <a href="{{ route('client.catalogue') }}" class="btn btn-danger mt-2">
                    Voir le catalogue
                </a>
            </div>
        @else
            <div class="row g-3">
                @foreach($orders as $order)
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <p class="mb-1 text-muted small">Référence</p>
                                    <code class="text-danger fs-6">{{ $order->reference }}</code>
                                </div>
                                <div class="col-md-2">
                                    <p class="mb-1 text-muted small">Date</p>
                                    <strong>{{ $order->created_at->format('d/m/Y') }}</strong>
                                </div>
                                <div class="col-md-2">
                                    <p class="mb-1 text-muted small">Articles</p>
                                    <strong>{{ $order->items->count() }}</strong>
                                </div>
                                <div class="col-md-2">
                                    <p class="mb-1 text-muted small">Montant</p>
                                    <strong class="text-danger">
                                        {{ number_format($order->total_amount, 0, ',', ' ') }} F
                                    </strong>
                                </div>
                                <div class="col-md-2">
                                    <span class="badge badge-{{ $order->status }} rounded-pill px-3 py-2">
                                        {{ $order->status_label }}
                                    </span>
                                </div>
                                <div class="col-md-1">
                                    <a href="{{ route('client.orders.show', $order) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $orders->links() }}</div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>