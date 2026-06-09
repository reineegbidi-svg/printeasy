<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Reçu {{ $payment->transaction_id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #2563eb; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #2563eb; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #eee; }
        .total { font-size: 16px; font-weight: bold; color: #2563eb; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #888; }
    </style>
</head>
<body>
    <div class="header">
        <h1>PrintEasy</h1>
        <p>Reçu de paiement</p>
    </div>
    <p><strong>N° Transaction:</strong> {{ $payment->transaction_id }}</p>
    <p><strong>Date:</strong> {{ $payment->paid_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</p>
    <p><strong>Client:</strong> {{ $payment->user->name }} ({{ $payment->user->email }})</p>
    <table>
        <tr><th>Commande</th><td>{{ $payment->order->reference }}</td></tr>
        <tr><th>Service</th><td>{{ $payment->order->service_type }}</td></tr>
        <tr><th>Méthode</th><td>{{ $payment->method }}</td></tr>
        <tr><th>Statut</th><td>{{ $payment->status }}</td></tr>
        <tr class="total"><th>Montant</th><td>{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</td></tr>
    </table>
    <div class="footer">
        <p>Merci d'utiliser PrintEasy — Plus de rapidité, plus de transparence.</p>
    </div>
</body>
</html>
