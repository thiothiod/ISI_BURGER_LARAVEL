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
        .cart-sidebar {
            position: fixed;
            right: -400px;
            top: 0;
            width: 380px;
            height: 100vh;
            background: white;
            box-shadow: -5px 0 20px rgba(0,0,0,.15);
            z-index: 9999;
            transition: right .3s ease;
            display: flex;
            flex-direction: column;
        }
        .cart-sidebar.open { right: 0; }
        .cart-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,.5);
            z-index: 9998;
        }
        .cart-overlay.open { display: block; }
        .cart-header {
            background: #1d1d1d;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cart-body { flex: 1; overflow-y: auto; padding: 15px; }
        .cart-footer { padding: 15px; border-top: 2px solid #f0f0f0; background: #f8f9fa; }
        .cart-item { background: #f8f9fa; border-radius: 10px; padding: 12px; margin-bottom: 10px; }
        .qty-btn { width: 30px; height: 30px; border-radius: 50%; border: 2px solid #e63946; background: white; color: #e63946; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; }
        .qty-btn:hover { background: #e63946; color: white; }
        .cart-badge {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
            background: #e63946;
            color: white;
            border: none;
            border-radius: 50px;
            padding: 12px 20px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(230,57,70,.4);
            cursor: pointer;
            display: none;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}">🍔 ISI BURGER</a>
            <div class="d-flex align-items-center gap-3">
                @auth
                    <span class="text-white">{{ auth()->user()->name }}</span>
                    <a href="{{ route('client.orders.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-shopping-bag me-1"></i>Mes Commandes
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline-danger btn-sm">Déconnexion</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sign-in-alt me-1"></i>Connexion
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-user-plus me-1"></i>S'inscrire
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Overlay --}}
    <div class="cart-overlay" id="cartOverlay" onclick="closeCart()"></div>

    {{-- Panier Sidebar --}}
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Mon Panier</h5>
            <button onclick="closeCart()" style="background:none; border:none; color:white; font-size:1.5rem; cursor:pointer">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="cart-body" id="cartBody">
            <div id="cartEmpty" class="text-center py-5 text-muted">
                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                <p>Votre panier est vide</p>
            </div>
            <div id="cartItems"></div>
        </div>
        <div class="cart-footer">
            <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
                <span>Total</span>
                <span class="text-danger" id="cartTotal">0 F</span>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Notes (optionnel)</label>
                <textarea class="form-control" id="notesInput" rows="2"
                          placeholder="Instructions spéciales..."></textarea>
            </div>
            <button onclick="confirmOrder()" class="btn btn-danger w-100 py-2 fw-bold">
                <i class="fas fa-check me-2"></i>Confirmer la commande
            </button>
        </div>
    </div>

    {{-- Bouton panier flottant --}}
    <button class="cart-badge" id="cartBadge" onclick="openCart()">
        <i class="fas fa-shopping-cart me-2"></i>
        <span id="cartCount">0</span> article(s) -
        <span id="cartTotalBadge">0</span> F
    </button>

    <form id="orderForm" method="POST" action="{{ route('client.orders.store') }}">
        @csrf
        <div id="orderItemsContainer"></div>
        <input type="hidden" name="notes" id="orderNotesHidden">
    </form>

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
                               placeholder="Prix min" value="{{ request('min_price') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="max_price" class="form-control"
                               placeholder="Prix max" value="{{ request('max_price') }}">
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
                    </div>
                    <div class="card-footer bg-white border-0">
                        <button type="button" class="btn btn-danger w-100"
                                onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }})">
                            <i class="fas fa-cart-plus me-2"></i>Ajouter au panier
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

        <div class="mt-4 mb-5">{{ $products->withQueryString()->links() }}</div>
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
            openCart();
        }

        function increaseQty(id) {
            if (cart[id]) {
                cart[id].qty++;
                updateCartUI();
            }
        }

        function decreaseQty(id) {
            if (cart[id] && cart[id].qty > 1) {
                cart[id].qty--;
                updateCartUI();
            } else {
                removeFromCart(id);
            }
        }

        function removeFromCart(id) {
            delete cart[id];
            updateCartUI();
        }

        function updateCartUI() {
            const items = Object.entries(cart);
            const total = items.reduce(function(sum, entry) {
                return sum + entry[1].price * entry[1].qty;
            }, 0);
            const count = items.reduce(function(sum, entry) {
                return sum + entry[1].qty;
            }, 0);

            // Badge flottant
            const badge = document.getElementById('cartBadge');
            if (count > 0) {
                badge.style.display = 'block';
                document.getElementById('cartCount').textContent = count;
                document.getElementById('cartTotalBadge').textContent = total.toLocaleString('fr-FR');
            } else {
                badge.style.display = 'none';
            }

            // Total sidebar
            document.getElementById('cartTotal').textContent = total.toLocaleString('fr-FR') + ' F';

            // Items sidebar
            const cartItemsDiv = document.getElementById('cartItems');
            const cartEmpty    = document.getElementById('cartEmpty');

            if (items.length === 0) {
                cartEmpty.style.display = 'block';
                cartItemsDiv.innerHTML  = '';
                return;
            }

            cartEmpty.style.display = 'none';
            let html = '';
            items.forEach(function(entry) {
                const id   = entry[0];
                const item = entry[1];
                const sub  = item.price * item.qty;
                html += '<div class="cart-item">' +
                    '<div class="d-flex justify-content-between align-items-start mb-2">' +
                        '<strong>' + item.name + '</strong>' +
                        '<button onclick="removeFromCart(' + id + ')" style="background:none; border:none; color:#dc3545; cursor:pointer">' +
                            '<i class="fas fa-trash"></i>' +
                        '</button>' +
                    '</div>' +
                    '<div class="d-flex justify-content-between align-items-center">' +
                        '<div class="d-flex align-items-center gap-2">' +
                            '<button class="qty-btn" onclick="decreaseQty(' + id + ')">−</button>' +
                            '<span class="fw-bold">' + item.qty + '</span>' +
                            '<button class="qty-btn" onclick="increaseQty(' + id + ')">+</button>' +
                        '</div>' +
                        '<span class="fw-bold text-danger">' + sub.toLocaleString('fr-FR') + ' F</span>' +
                    '</div>' +
                '</div>';
            });
            cartItemsDiv.innerHTML = html;
        }

        function openCart() {
            document.getElementById('cartSidebar').classList.add('open');
            document.getElementById('cartOverlay').classList.add('open');
        }

        function closeCart() {
            document.getElementById('cartSidebar').classList.remove('open');
            document.getElementById('cartOverlay').classList.remove('open');
        }

     function confirmOrder() {
            @if(!auth()->check())
                // Sauvegarder le panier dans localStorage
                localStorage.setItem('isi_cart', JSON.stringify(cart));
                window.location.href = "{{ route('login') }}";
                return;
            @endif

            const items = Object.entries(cart);
            if (items.length === 0) {
                alert('Votre panier est vide !');
                return;
            }

            const container = document.getElementById('orderItemsContainer');
            container.innerHTML = '';
            items.forEach(function(entry, i) {
                const id  = entry[0];
                const item = entry[1];
                container.innerHTML +=
                    '<input type="hidden" name="items[' + i + '][id]" value="' + id + '">' +
                    '<input type="hidden" name="items[' + i + '][qty]" value="' + item.qty + '">';
            });

            document.getElementById('orderNotesHidden').value = document.getElementById('notesInput').value;
            document.getElementById('orderForm').submit();
        }


        // Restaurer le panier après connexion
    window.addEventListener('load', function() {
        const savedCart = localStorage.getItem('isi_cart');
        if (savedCart) {
            cart = JSON.parse(savedCart);
            localStorage.removeItem('isi_cart');
            updateCartUI();
            @auth
                openCart();
            @endauth
        }
    });
    </script>
</body>
</html>