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
            <a href="{{ route('client.orders.index') }}" class="btn btn-outline-light btn-sm">
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

        <div class="row g-4">
            {{-- Articles --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="fw-bold mb-0">Détail des articles</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Burger</th>
                                    <th class="text-center">Quantité</th>
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
                                                     class="rounded" width="50" height="50"
                                                     style="object-fit:cover">
                                            @else
                                                <span style="font-size:2rem">🍔</span>
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
            </div>

            {{-- Statut --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="fw-bold mb-0">Statut de la commande</h6>
                    </div>
                    <div class="card-body">
                        <span class="badge badge-{{ $order->status }} rounded-pill px-3 py-2 fs-6">
                            {{ $order->status_label }}
                        </span>

                        @php
                            $steps = [
                                'en_attente'     => ['icon' => 'clock',            'label' => 'En attente'],
                                'en_preparation' => ['icon' => 'fire',             'label' => 'En préparation'],
                                'prete'          => ['icon' => 'check-circle',     'label' => 'Prête'],
                                'payee'          => ['icon' => 'money-bill-wave',  'label' => 'Payée'],
                            ];
                            $statusOrder  = array_keys($steps);
                            $currentIndex = array_search($order->status, $statusOrder);
                        @endphp

                        @if($order->status !== 'annulee')
                        <div class="mt-3">
                            @foreach($steps as $key => $step)
                            @php $stepIndex = array_search($key, $statusOrder); @endphp
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                    <i class="fas fa-{{ $step['icon'] }} small
                                       text-{{ $stepIndex <= $currentIndex ? 'white' : 'muted' }}"></i>
                                </div>
                                <span class="{{ $stepIndex <= $currentIndex ? 'fw-semibold' : 'text-muted' }}">
                                    {{ $step['label'] }}
                                </span>
                                @if($key === $order->status)
                                    <span class="badge bg-success ms-auto">Actuel</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @else
                            <div class="alert alert-danger mt-3 mb-0">
                                <i class="fas fa-ban me-2"></i>Commande annulée.
                            </div>
                        @endif

                        @if($order->status === 'payee')
                        <hr>
                        <p class="mb-1 text-muted small">Payée le</p>
                        <strong>{{ $order->paid_at->format('d/m/Y à H:i') }}</strong>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>