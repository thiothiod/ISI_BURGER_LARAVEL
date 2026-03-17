<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue - ISI BURGER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .navbar { background: #1d1d1d !important; }
        .navbar-brand { color: #e63946 !important; font-weight: 900; }
        .card { border: none; box-shadow: 0 2px 12px rgba(0,0,0,.08); border-radius: 12px; }
        .product-card { transition: transform .2s; }
        .product-card:hover { transform: translateY(-4px); }
        .product-img { height: 200px; object-fit: cover; }
        .btn-danger { background: #e63946; border-color: #e63946; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark mb-4">
        <div class="container-fluid">
            <span class="navbar-brand">🍔 ISI BURGER</span>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white">{{ auth()->user()->name }}</span>
                <a href="{{ route('client.orders.index') }}" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-shopping-bag me-1"></i>Mes Commandes
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
                <h2 class="fw-bold mb-0">🍔 Nos Burgers</h2>
                <p class="text-muted">{{ $products->total() }} burger(s) disponibles</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Filtres --}}
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control"
                               placeholder="Rechercher..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="category_id" class="form-select">
                            <option value="">Catégories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="min_price" class="form-control"
                               placeholder="Prix min"
                               value="{{ request('min_price') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="max_price" class="form-control"
                               placeholder="Prix max"
                               value="{{ request('max_price') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="sort" class="form-select">
                            <option value="">Trier par</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Prix ↑</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Prix ↓</option>
                            <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Nom</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Panier --}}
        <div id="cartSummary" class="alert alert-danger d-none mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-shopping-cart me-2"></i>
                    <strong id="cartCount">0</strong> article(s) -
                    <strong id="cartTotal">0</strong> F
                </div>
                <button type="button" class="btn btn-white btn-sm border" onclick="submitOrder()">
                    <i class="fas fa-check me-1"></i>Commander
                </button>
            </div>
        </div>

        <form id="orderForm" method="POST" action="{{ route('client.orders.store') }}">
            @csrf
            <div id="orderItemsContainer"></div>
            <input type="hidden" name="notes" id="orderNotes">
        </form>

        {{-- Produits --}}
        <div class="row g-4">
            @forelse($products as $product)
            <div class="col-md-4">
                <div class="card product-card h-100">
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}"
                             class="card-img-top product-img" alt="{{ $product->name }}">
                    @else
                        <div class="card-img-top product-img bg-light d-flex align-items-center justify-content-center">
                            <i class="fas fa-burger fa-4x text-muted"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title fw-bold mb-1">{{ $product->name }}</h5>
                            <span class="badge bg-secondary">{{ $product->category->name }}</span>
                        </div>
                        <p class="text-muted small mb-2">{{ substr($product->description, 0, 80) }}</p>
                        <span class="fs-5 fw-bold text-danger">
                            {{ number_format($product->price, 0, ',', ' ') }} F
                        </span>
                        <div id="qty-{{ $product->id }}" class="mt-2 d-none">
                            <small class="text-success fw-semibold">
                                <i class="fas fa-shopping-cart me-1"></i>
                                Quantité : <span id="qty-display-{{ $product->id }}">0</span>
                            </small>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 d-flex gap-2">
                        <button type="button"
                                class="btn btn-danger flex-fill"
                                onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})">
                            <i class="fas fa-plus me-1"></i>Ajouter
                        </button>
                        <button type="button"
                                class="btn btn-outline-secondary"
                                onclick="removeFromCart({{ $product->id }})">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="fas fa-burger fa-3x text-muted mb-3"></i>
                <p class="text-muted">Aucun burger disponible.</p>
            </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $products->withQueryString()->links() }}</div>
    </div>

    {{-- Modal confirmation --}}
    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">🛒 Confirmer la commande</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="modalCartItems"></div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold fs-5">
                        <span>Total</span>
                        <span class="text-danger" id="modalTotal"></span>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Notes (optionnel)</label>
                        <textarea class="form-control" id="notesInput" rows="2"
                                  placeholder="Instructions spéciales..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">Continuer</button>
                    <button type="button" class="btn btn-danger px-4" onclick="confirmOrder()">
                        <i class="fas fa-check me-2"></i>Confirmer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let cart = {};

        function addToCart(id, name, price) {
            if (!cart[id]) {
                cart[id] = { name: name, price: price, qty: 0 };
            }
            cart[id].qty++;
            updateCartUI();
            updateQtyDisplay(id);
        }

        function removeFromCart(id) {
            if (cart[id] && cart[id].qty > 0) {
                cart[id].qty--;
                if (cart[id].qty === 0) delete cart[id];
            }
            updateCartUI();
            updateQtyDisplay(id);
        }

                function updateQtyDisplay(id) {
    const qtyDiv     = document.getElementById('qty-' + id);
    const qtyDisplay = document.getElementById('qty-display-' + id);
    if (!qtyDiv || !qtyDisplay) return;
    if (cart[id] && cart[id].qty > 0) {
        qtyDiv.classList.remove('d-none');
        qtyDisplay.textContent = cart[id].qty;
    } else {
        qtyDiv.classList.add('d-none');
    }
}

        function updateCartUI() {
            const items = Object.entries(cart).filter(function([k, v]) { return v.qty > 0; });
            const total = items.reduce(function(sum, [k, v]) { return sum + v.price * v.qty; }, 0);
            const count = items.reduce(function(sum, [k, v]) { return sum + v.qty; }, 0);
            const summary = document.getElementById('cartSummary');
            if (count > 0) {
                summary.classList.remove('d-none');
                document.getElementById('cartCount').textContent = count;
                document.getElementById('cartTotal').textContent = total.toLocaleString('fr-FR');
            } else {
                summary.classList.add('d-none');
            }
        }

        function submitOrder() {
            const items = Object.entries(cart).filter(function([k, v]) { return v.qty > 0; });
            if (items.length === 0) return;
            let html  = '<ul class="list-group">';
            let total = 0;
            items.forEach(function([id, item]) {
                const sub = item.price * item.qty;
                total += sub;
                html += '<li class="list-group-item d-flex justify-content-between">' +
                    '<span>' + item.name + ' × ' + item.qty + '</span>' +
                    '<strong>' + sub.toLocaleString('fr-FR') + ' F</strong>' +
                    '</li>';
            });
            html += '</ul>';
            document.getElementById('modalCartItems').innerHTML = html;
            document.getElementById('modalTotal').textContent   = total.toLocaleString('fr-FR') + ' F';
            new bootstrap.Modal(document.getElementById('orderModal')).show();
        }

        function confirmOrder() {
            const items     = Object.entries(cart).filter(function([k, v]) { return v.qty > 0; });
            const container = document.getElementById('orderItemsContainer');
            container.innerHTML = '';
            items.forEach(function([id, item], i) {
                container.innerHTML +=
                    '<input type="hidden" name="items[' + i + '][id]" value="' + id + '">' +
                    '<input type="hidden" name="items[' + i + '][qty]" value="' + item.qty + '">';
            });
            document.getElementById('orderNotes').value = document.getElementById('notesInput').value;
            document.getElementById('orderForm').submit();
        }
    </script>
</body>
</html>