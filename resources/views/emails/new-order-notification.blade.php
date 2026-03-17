<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nouvelle commande</title>
</head>
<body style="font-family:Arial,sans-serif; background:#f5f5f5; padding:20px; margin:0">
    <div style="max-width:600px; margin:0 auto; background:white; border-radius:12px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,.1)">
        <div style="background:linear-gradient(135deg,#1d1d1d,#333); padding:30px; text-align:center">
            <h1 style="color:white; margin:0; font-size:1.8rem">🍔 ISI BURGER</h1>
            <p style="color:#e63946; margin:8px 0 0; font-size:1.1rem; font-weight:bold">
                ⚠️ Nouvelle commande reçue !
            </p>
        </div>
        <div style="padding:30px">
            <h2 style="color:#333; margin-top:0">Commande {{ $order->reference }}</h2>
            <div style="background:#fff3cd; border:1px solid #ffc107; border-radius:8px; padding:15px; margin-bottom:20px">
                <p style="margin:0; font-weight:bold; color:#856404">
                    📋 Une nouvelle commande attend votre traitement !
                </p>
            </div>
            <table style="width:100%; border-collapse:collapse; margin-bottom:15px">
                <tr style="border-bottom:1px solid #eee">
                    <td style="padding:8px; color:#666">Client</td>
                    <td style="padding:8px; font-weight:bold">{{ $order->user->name }}</td>
                </tr>
                <tr style="border-bottom:1px solid #eee">
                    <td style="padding:8px; color:#666">Email</td>
                    <td style="padding:8px">{{ $order->user->email }}</td>
                </tr>
                <tr style="border-bottom:1px solid #eee">
                    <td style="padding:8px; color:#666">Montant</td>
                    <td style="padding:8px; font-weight:bold; color:#e63946; font-size:16px">
                        {{ number_format($order->total_amount, 0, ',', ' ') }} F
                    </td>
                </tr>
                <tr>
                    <td style="padding:8px; color:#666">Date</td>
                    <td style="padding:8px">{{ $order->created_at->format('d/m/Y à H:i') }}</td>
                </tr>
            </table>
            <p style="text-align:center">
                <a href="{{ url('/admin/orders/' . $order->id) }}"
                   style="background:#e63946; color:white; padding:12px 25px; border-radius:8px; text-decoration:none; font-weight:bold">
                    Voir la commande →
                </a>
            </p>
        </div>
        <div style="background:#1d1d1d; padding:15px; text-align:center">
            <p style="color:#999; margin:0; font-size:12px">ISI BURGER - Notification automatique</p>
        </div>
    </div>
</body>
</html>