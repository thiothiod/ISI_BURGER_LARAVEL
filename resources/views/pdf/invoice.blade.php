<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $order->reference }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 13px; color: #333; }
        .header { background: #e63946; color: white; padding: 30px; }
        .header h1 { font-size: 28px; font-weight: 900; }
        .content { padding: 30px; }
        .info-grid { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .info-box { background: #f8f9fa; padding: 15px; border-radius: 8px; width: 48%; }
        .info-box h3 { color: #e63946; margin-bottom: 10px; font-size: 12px; text-transform: uppercase; }
        .info-box p { margin: 3px 0; line-height: 1.5; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead { background: #1d1d1d; color: white; }
        thead th { padding: 12px; text-align: left; font-size: 12px; }
        tbody tr:nth-child(even) { background: #f8f9fa; }
        tbody td { padding: 10px 12px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        .total-box { background: #1d1d1d; color: white; padding: 15px; border-radius: 8px; width: 300px; margin-left: auto; }
        .total-row { display: flex; justify-content: space-between; margin: 5px 0; }
        .total-row.final { font-size: 16px; font-weight: bold; color: #e63946; border-top: 1px solid #555; padding-top: 8px; margin-top: 8px; }
        .footer { margin-top: 40px; text-align: center; color: #999; font-size: 11px; border-top: 1px solid #eee; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🍔 ISI BURGER</h1>
        <p style="opacity:.8">Votre restaurant de burgers premium</p>
    </div>

    <div class="content">
        <div style="text-align:right; margin-bottom:20px">
            <h2 style="color:#e63946">FACTURE</h2>
            <p><strong>{{ $order->reference }}</strong></p>
            <p>{{ $order->created_at->format('d/m/Y') }}</p>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <h3>📍 Restaurant</h3>
                <p><strong>ISI BURGER</strong></p>
                <p>Dakar, Sénégal</p>
                <p>noreply@isiburger.com</p>
            </div>
            <div class="info-box">
                <h3>👤 Client</h3>
                <p><strong>{{ $order->user->name }}</strong></p>
                <p>{{ $order->user->email }}</p>
                @if($order->user->phone)
                <p>{{ $order->user->phone }}</p>
                @endif
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Désignation</th>
                    <th>Catégorie</th>
                    <th class="text-right">Qté</th>
                    <th class="text-right">Prix unit. (F)</th>
                    <th class="text-right">Sous-total (F)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $item->product->name }}</strong></td>
                    <td>{{ $item->product->category->name }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
                    <td class="text-right"><strong>{{ number_format($item->subtotal, 0, ',', ' ') }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-box">
            <div class="total-row">
                <span>Sous-total HT</span>
                <span>{{ number_format($order->total_amount, 0, ',', ' ') }} F</span>
            </div>
            <div class="total-row">
                <span>TVA (0%)</span>
                <span>0 F</span>
            </div>
            <div class="total-row final">
                <span>TOTAL TTC</span>
                <span>{{ number_format($order->total_amount, 0, ',', ' ') }} F</span>
            </div>
            @if($order->paid_at)
            <div class="total-row" style="margin-top:10px; font-size:11px; opacity:.7">
                <span>Payé le {{ $order->paid_at->format('d/m/Y H:i') }}</span>
                <span>✓</span>
            </div>
            @endif
        </div>
    </div>

    <div class="footer">
        <p>Merci pour votre confiance ! 🍔 ISI BURGER</p>
        <p style="margin-top:5px">Document généré le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>
</body>
</html>