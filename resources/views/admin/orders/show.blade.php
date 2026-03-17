<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande {{ $order->reference }} - ISI BURGER</title>
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
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-light btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">
                    Commande <span class="text-danger">{{ $order->reference }}</span>
                </h2>
                <p class="text-muted mb-0">{{ $order->created_at->format('d/m/Y à H:i') }}</p>
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

        <div class="row g-4">
            {{-- Articles --}}
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="fw-bold mb-0">Articles commandés</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Produit</th>
                                    <th class="text-center">Qté</th>
                                    <th class="text-end">Prix unit.</th>
                                    <th class="text-end">Sous-total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($item->product->image)
                                                <img src="{{ Storage::url($item->product->image) }}"
                                                     class="rounded" width="45" height="45"
                                                     style="object-fit:cover">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                                     style="width:45px;height:45px">
                                                    <i class="fas fa-burger text-muted"></i>
                                                </div>
                                            @endif
                                            <strong>{{ $item->product->name }}</strong>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">{{ $item->quantity }}</td>
                                    <td class="text-end align-middle">
                                        {{ number_format($item->unit_price, 0, ',', ' ') }} F
                                    </td>
                                    <td class="text-end align-middle">
                                        <strong>{{ number_format($item->subtotal, 0, ',', ' ') }} F</strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">TOTAL</td>
                                    <td class="text-end fw-bold text-danger fs-5">
                                        {{ number_format($order->total_amount, 0, ',', ' ') }} F
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                @if($order->notes)
                <div class="card">
                    <div class="card-body">
                        <h6 class="fw-bold"><i class="fas fa-sticky-note me-2"></i>Notes</h6>
                        <p class="mb-0 text-muted">{{ $order->notes }}</p>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-md-4">
                {{-- Client --}}
                <div class="card mb-4">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="fw-bold mb-0"><i class="fas fa-user me-2"></i>Client</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>{{ $order->user->name }}</strong></p>
                        <p class="mb-1 text-muted">{{ $order->user->email }}</p>
                        @if($order->user->phone)
                            <p class="mb-0 text-muted">{{ $order->user->phone }}</p>
                        @endif
                    </div>
                </div>

                {{-- Statut --}}
                <div class="card">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="fw-bold mb-0"><i class="fas fa-tasks me-2"></i>Gérer le Statut</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge badge-{{ $order->status }} rounded-pill px-3 py-2 fs-6">
                                {{ $order->status_label }}
                            </span>
                        </div>

                        @if(!in_array($order->status, ['annulee', 'payee']))
                        <form method="POST"
                              action="{{ route('admin.orders.update-status', $order) }}">
                            @csrf @method('PATCH')
                            <div class="mb-3">
                                <select name="status" class="form-select">
                                    @foreach($statusLabels as $key => $label)
                                        @if($key !== 'annulee')
                                        <option value="{{ $key }}"
                                            {{ $order->status === $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i>Mettre à jour
                            </button>
                        </form>

                        <hr>

                        <form method="POST"
                              action="{{ route('admin.orders.cancel', $order) }}"
                              onsubmit="return confirm('Annuler cette commande ?')">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-ban me-2"></i>Annuler la commande
                            </button>
                        </form>
                        @endif

                        @if($order->status === 'payee')
                        <div class="alert alert-success mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            Payée le {{ $order->paid_at->format('d/m/Y à H:i') }}<br>
                            <strong>{{ number_format($order->paid_amount, 0, ',', ' ') }} F</strong>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>