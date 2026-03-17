<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commande prête</title>
</head>
<body style="font-family:Arial,sans-serif; background:#f5f5f5; padding:20px; margin:0">
    <div style="max-width:600px; margin:0 auto; background:white; border-radius:12px; overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,.1)">
        <div style="background:linear-gradient(135deg,#198754,#157347); padding:30px; text-align:center">
            <h1 style="color:white; margin:0; font-size:2rem">🍔 ISI BURGER</h1>
            <p style="color:rgba(255,255,255,.9); margin:8px 0 0">Votre commande est prête !</p>
        </div>
        <div style="padding:30px">
            <h2 style="color:#333; margin-top:0">
                🎉 Bonne nouvelle {{ $order->user->name }} !
            </h2>
            <p style="color:#666; line-height:1.6; font-size:15px">
                Votre commande <strong style="color:#e63946">{{ $order->reference }}</strong>
                est <strong>prête</strong> et peut être récupérée.
            </p>
            <div style="background:#d1fae5; border:1px solid #6ee7b7; border-radius:8px; padding:15px; text-align:center; margin:20px 0">
                <p style="margin:0; color:#065f46; font-size:16px; font-weight:bold">
                    ✅ Montant à payer : {{ number_format($order->total_amount, 0, ',', ' ') }} F
                </p>
            </div>
            <p style="color:#666; font-size:13px">
                📎 Votre facture PDF est jointe à cet email.
            </p>
        </div>
        <div style="background:#1d1d1d; padding:15px; text-align:center">
            <p style="color:#999; margin:0; font-size:12px">ISI BURGER - noreply@isiburger.com</p>
        </div>
    </div>
</body>
</html>