<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation de commande</title>
</head>
<body style="font-family:Arial,sans-serif; background:#f5f5f5; padding:20px; margin:0">
    <div style="max-width:600px; margin:0 auto; background:white; border-radius:12px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,.1)">
        <div style="background:linear-gradient(135deg,#e63946,#c62836); padding:30px; text-align:center">
            <h1 style="color:white; margin:0; font-size:2rem">🍔 ISI BURGER</h1>
            <p style="color:rgba(255,255,255,.9); margin:8px 0 0">Confirmation de votre commande</p>
        </div>

        <div style="padding:30px">
            <h2 style="color:#333; margin-top:0">Bonjour {{ $order->user->name }} ! 👋</h2>
            <p style="color:#666; line-height:1.6">
                Votre commande a bien été reçue et est en cours de traitement.
                Nous vous tiendrons informé de son avancement.
            </p>

            <div style="background:#f8f9fa; border-radius:8px; padding:20px; margin:20px 0">
                <h3 style="color:#e63946; margin-top:0; font-size:14px; text-transform:uppercase">
                    Détails de la commande
                </h3>
                <p style="margin:5px 0"><strong>Référence :</strong> <code>{{ $order->reference }}</code></p>
                <p style="margin:5px 0"><strong>Date :</strong> {{ $order->created_at->format('d/m/Y à H:i') }}</p>
                <p style="margin:5px 0"><strong>Statut :</strong>
                    <span style="background:#ffc107; color:#000; padding:2px 10px; border-radius:10px; font-size:12px">
                        En attente de préparation
                    </span>
                </p>
            </div>

            <h3 style="color:#333">Articles commandés</h3>
            <table style="width:100%; border-collapse:collapse">
                <thead>
                    <tr style="background:#1d1d1d; color:white">
                        <th style="padding:10px; text-align:left">Produit</th>
                        <th style="padding:10px; text-align:center">Qté</th>
                        <th style="padding:10px; text-align:right">Prix</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr style="border-bottom:1px solid #eee">
                        <td style="padding:10px">{{ $item->product->name }}</td>
                        <td style="padding:10px; text-align:center">{{ $item->quantity }}</td>
                        <td style="padding:10px; text-align:right">{{ number_format($item->subtotal, 0, ',', ' ') }} F</td>
                    </tr>
                    @endforeach
                    <tr style="background:#f8f9fa; font-weight:bold">
                        <td colspan="2" style="padding:12px; text-align:right">TOTAL</td>
                        <td style="padding:12px; text-align:right; color:#e63946; font-size:16px">
                            {{ number_format($order->total_amount, 0, ',', ' ') }} F
                        </td>
                    </tr>
                </tbody>
            </table>

            <p style="color:#999; font-size:12px; margin-top:20px; text-align:center">
                Vous recevrez un email avec votre facture PDF lorsque votre commande sera prête.<br>
                Merci de votre confiance ! 🍔
            </p>
        </div>

        <div style="background:#1d1d1d; padding:15px; text-align:center">
            <p style="color:#999; margin:0; font-size:12px">ISI BURGER - noreply@isiburger.com</p>
        </div>
    </div>
</body>
</html>