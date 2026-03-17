<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Burgers - ISI BURGER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .navbar { background: #1d1d1d !important; }
        .navbar-brand { color: #e63946 !important; font-weight: 900; font-size: 1.5rem; }
        .btn-danger { background: #e63946; border-color: #e63946; }
        .card { border: none; box-shadow: 0 2px 12px rgba(0,0,0,.08); border-radius: 12px; }
        .product-img { height: 160px; object-fit: cover; }
    </style>
</head>
<body>
    {{-- Navbar --}}
    <nav class="navbar navbar-dark mb-4">
        <div class="container-fluid">
            <span class="navbar-brand">🍔 ISI BURGER</span>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white">{{ auth()->user()->name }}</span>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light btn-sm">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm">Déconnexion</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">🍔 Gestion des Burgers</h2>
                <p class="text-muted">{{ $products->total() }} produit(s)</p>
            </div>
            <a href="{{ route('admin.products.create') }}" class="btn btn-danger">
                <i class="fas fa-plus me-2"></i>Nouveau Burger
            </a>
        </div>

        {{-- Alertes --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Filtres --}}
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control"
                               placeholder="Rechercher un burger..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="category_id" class="form-select">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="disponible" {{ request('status') === 'disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="rupture" {{ request('status') === 'rupture' ? 'selected' : '' }}>Rupture</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-search me-1"></i>Filtrer
                        </button>
                    </div>
                    <div class="col-md-1">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Grille des produits --}}
        <div class="row g-3">
            @forelse($products as $product)
            <div class="col-md-3">
                <div class="card h-100">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}"
                             class="card-img-top product-img" alt="{{ $product->name }}">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center product-img">
                            <i class="fas fa-burger fa-3x text-muted"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="card-title fw-bold mb-1">{{ $product->name }}</h6>
                            @if($product->status === 'rupture')
                                <span class="badge bg-warning text-dark">Rupture</span>
                            @else
                                <span class="badge bg-success">Dispo</span>
                            @endif
                        </div>
                        <p class="text-muted small mb-1">{{ $product->category->name }}</p>
                        <p class="fw-bold text-danger mb-1">{{ number_format($product->price, 0, ',', ' ') }} F</p>
                        <small class="text-muted">Stock : {{ $product->stock }}</small>
                    </div>
                    <div class="card-footer bg-white border-0 d-flex gap-1">
                        <a href="{{ route('admin.products.edit', $product) }}"
                           class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST"
                              action="{{ route('admin.products.archive', $product) }}"
                              class="flex-fill">
                            @csrf @method('PATCH')
                            <button class="btn btn-sm btn-outline-warning w-100" title="Archiver">
                                <i class="fas fa-archive"></i>
                            </button>
                        </form>
                        <form method="POST"
                              action="{{ route('admin.products.destroy', $product) }}"
                              class="flex-fill"
                              onsubmit="return confirm('Supprimer ce burger ?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger w-100">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-burger fa-3x text-muted mb-3"></i>
                <p class="text-muted">Aucun burger trouvé.</p>
                <a href="{{ route('admin.products.create') }}" class="btn btn-danger">
                    Ajouter le premier burger
                </a>
            </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $products->withQueryString()->links() }}</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>