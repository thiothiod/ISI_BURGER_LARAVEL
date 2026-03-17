<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Burger - ISI BURGER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .navbar { background: #1d1d1d !important; }
        .navbar-brand { color: #e63946 !important; font-weight: 900; }
        .card { border: none; box-shadow: 0 2px 12px rgba(0,0,0,.08); border-radius: 12px; }
        .btn-danger { background: #e63946; border-color: #e63946; }
        .form-control:focus, .form-select:focus { border-color: #e63946; box-shadow: 0 0 0 .2rem rgba(230,57,70,.25); }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark mb-4">
        <div class="container-fluid">
            <span class="navbar-brand">🍔 ISI BURGER</span>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-light btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </nav>

    <div class="container">
        <h2 class="fw-bold mb-4">✏️ Modifier : {{ $product->name }}</h2>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body p-4">

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $e)
                                        <li>{{ $e }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST"
                              action="{{ route('admin.products.update', $product) }}"
                              enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label fw-semibold">Nom du burger *</label>
                                    <input type="text" name="name" class="form-control"
                                           value="{{ old('name', $product->name) }}" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Catégorie *</label>
                                    <select name="category_id" class="form-select" required>
                                        <option value="">Choisir...</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}"
                                                {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea name="description" class="form-control"
                                          rows="3">{{ old('description', $product->description) }}</textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Prix (FCFA) *</label>
                                    <input type="number" name="price" class="form-control"
                                           value="{{ old('price', $product->price) }}"
                                           min="0" step="50" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Stock *</label>
                                    <input type="number" name="stock" class="form-control"
                                           value="{{ old('stock', $product->stock) }}"
                                           min="0" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-semibold">Statut *</label>
                                    <select name="status" class="form-select" required>
                                        <option value="disponible" {{ old('status', $product->status) === 'disponible' ? 'selected' : '' }}>Disponible</option>
                                        <option value="rupture" {{ old('status', $product->status) === 'rupture' ? 'selected' : '' }}>Rupture</option>
                                        <option value="archive" {{ old('status', $product->status) === 'archive' ? 'selected' : '' }}>Archivé</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Image</label>
                                @if($product->image)
                                    <div class="mb-2">
                                        <img src="{{ Storage::url($product->image) }}"
                                             class="rounded" style="height:100px; width:auto">
                                        <small class="d-block text-muted mt-1">Image actuelle</small>
                                    </div>
                                @endif
                                <input type="file" name="image" class="form-control"
                                       accept="image/jpeg,image/png,image/webp">
                                <small class="text-muted">Laisser vide pour garder l'image actuelle.</small>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-danger px-4">
                                    <i class="fas fa-save me-2"></i>Mettre à jour
                                </button>
                                <a href="{{ route('admin.products.index') }}"
                                   class="btn btn-outline-secondary">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>