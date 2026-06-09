<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Payment::with(['order', 'user:id,name,email']);

        $user = $request->user();
        if (! $user->isAdmin()) {
            if ($user->isPrinter()) {
                // Pour les imprimeurs : récupérer les paiements des commandes qui leur sont assignées
                $query->whereHas('order', function ($q) use ($user) {
                    $q->where('printer_id', $user->id);
                });
            } else {
                // Pour les clients : récupérer leurs propres paiements
                $query->where('user_id', $user->id);
            }
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        return response()->json($query->latest()->paginate($request->integer('per_page', 10)));
    }

    public function pay(Request $request, Order $order): JsonResponse
    {
        if ($order->user_id !== $request->user()->id && ! $request->user()->isAdmin()) {
            abort(403);
        }

        if ($order->payment_status === 'paid') {
            return response()->json(['message' => 'Commande déjà payée.'], 422);
        }

        $data = $request->validate([
            'method' => 'required|in:stripe,mobile_money,on_delivery,cash',
            'phone' => 'required_if:method,mobile_money|nullable|string',
        ]);

        $status = $data['method'] === 'on_delivery' ? 'pending' : 'completed';
        $transactionId = 'TXN-'.strtoupper(Str::random(12));

        $payment = Payment::create([
            'transaction_id' => $transactionId,
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'amount' => $order->total_price,
            'method' => $data['method'],
            'status' => $status,
            'provider_reference' => $data['method'] === 'mobile_money'
                ? 'MM-'.Str::random(10)
                : ($data['method'] === 'stripe' ? 'pi_sim_'.Str::random(16) : null),
            'metadata' => ['phone' => $data['phone'] ?? null, 'simulated' => true],
            'paid_at' => $status === 'completed' ? now() : null,
        ]);

        $order->update([
            'payment_status' => $status === 'completed' ? 'paid' : 'pending',
        ]);

        // Générer et stocker le reçu si paiement terminé
        if ($status === 'completed') {
            $payment->load(['order', 'user']);
            $pdf = Pdf::loadView('receipt', ['payment' => $payment]);
            $pdfPath = 'receipts/recu-'.$transactionId.'.pdf';
            \Storage::disk('public')->put($pdfPath, $pdf->output());
            $payment->update(['receipt_path' => $pdfPath]);
        }

        return response()->json([
            'message' => $status === 'completed' ? 'Paiement effectué.' : 'Paiement enregistré (à la réception).',
            'payment' => $payment->load('order'),
        ]);
    }

    public function receipt(Request $request, Payment $payment)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        }

        // Vérifier l'accès : propriétaire, admin, ou imprimeur assigné à la commande
        $payment->load('order');
        $isOwner = $payment->user_id === $user->id;
        $isAdmin = $user->isAdmin();
        $isAssignedPrinter = $user->isPrinter() && $payment->order->printer_id === $user->id;

        if (! $isOwner && ! $isAdmin && ! $isAssignedPrinter) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        $payment->load(['order', 'user']);

        try {
            // Si le reçu est déjà stocké, le télécharger
            if ($payment->receipt_path && \Storage::disk('public')->exists($payment->receipt_path)) {
                return response()->download(\Storage::disk('public')->path($payment->receipt_path), "recu-{$payment->transaction_id}.pdf");
            }

            // Sinon, générer à la volée
            $pdf = Pdf::loadView('receipt', ['payment' => $payment]);
            return $pdf->download("recu-{$payment->transaction_id}.pdf");
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Erreur génération PDF: '.$e->getMessage()], 500);
        }
    }
}
