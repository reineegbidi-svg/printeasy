<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PricingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function __construct(
        protected PricingService $pricing,
        protected OrderService $orders
    ) {}

    public function index(Request $request): JsonResponse
    {
        $query = $this->baseQuery($request);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($service = $request->get('service_type')) {
            $query->where('service_type', $service);
        }

        $orders = $query->with(['user:id,name,email', 'printer:id,name,email,phone,address'])
            ->latest()
            ->paginate($request->integer('per_page', 10));

        return response()->json($orders);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'service_type' => 'required|in:print,photocopy,scan',
            'format' => 'required|in:A4,A3,A5,letter',
            'color_mode' => 'required|in:color,bw',
            'quantity' => 'required|integer|min:1|max:10000',
            'pages' => 'nullable|integer|min:1|max:10000',
            'page_start' => 'nullable|integer|min:1|max:10000',
            'page_end' => 'nullable|integer|min:1|max:10000',
            'printer_id' => 'nullable|exists:users,id',
            'payment_method' => 'required|in:online,on_delivery',
            'notes' => 'nullable|string|max:1000',
            'files' => 'required|array|min:1',
            'files.*' => 'file|max:20480|extensions:pdf,doc,docx,jpg,jpeg,png',
        ]);

        $pricing = $this->pricing->calculate(
            $data['service_type'],
            $data['format'],
            $data['color_mode'],
            $data['quantity'],
            $data['pages'] ?? 1,
            $data['printer_id'] ?? null
        );

        $firstFile = $request->file('files')[0];

        $order = Order::create([
            'reference' => $this->orders->generateReference(),
            'user_id' => $request->user()->id,
            'printer_id' => null,
            'service_type' => $data['service_type'],
            'format' => $data['format'],
            'color_mode' => $data['color_mode'],
            'quantity' => $data['quantity'],
            'pages' => $data['pages'] ?? 1,
            'unit_price' => $pricing['unit_price'],
            'total_price' => $pricing['total_price'],
            'payment_method' => $data['payment_method'],
            'file_path' => $firstFile->store('orders', 'public'),
            'file_name' => $firstFile->getClientOriginalName(),
            'file_type' => $firstFile->getClientMimeType(),
            'file_size' => $firstFile->getSize(),
            'notes' => $data['notes'] ?? null,
        ]);

        foreach ($request->file('files') as $file) {
            $order->files()->create([
                'file_path' => $file->store('orders', 'public'),
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);
        }

        return response()->json([
            'message' => 'Commande créée avec succès.',
            'order' => $order->load(['user', 'printer', 'files']),
        ], 201);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        $this->authorizeOrder($request, $order);

        return response()->json([
            'order' => $order->load(['user', 'printer:id,name,email,phone,address', 'statusHistories.changedByUser', 'payment', 'files']),
        ]);
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $this->authorizeOrder($request, $order, true);

        $user = $request->user();

        if ($user->isPrinter() && $order->printer_id !== $user->id) {
            abort(403, 'Cette commande ne vous est pas attribuée.');
        }

        $data = $request->validate([
            'status' => 'required|in:in_progress,completed,delivered,rejected,cancelled',
            'comment' => 'nullable|string|max:500',
        ]);

        if ($order->status === 'pending') {
            return response()->json([
                'message' => 'Utilisez « Accepter la commande » pour prendre une commande en attente.',
            ], 422);
        }

        $order = $this->orders->updateStatus($order, $data['status'], $user, $data['comment'] ?? null);

        return response()->json(['message' => 'Statut mis à jour.', 'order' => $order]);
    }

    public function calculatePrice(Request $request): JsonResponse
    {
        $data = $request->validate([
            'service_type' => 'required|in:print,photocopy,scan',
            'format' => 'required|in:A4,A3,A5,letter',
            'color_mode' => 'required|in:color,bw',
            'quantity' => 'required|integer|min:1',
            'pages' => 'nullable|integer|min:1',
            'printer_id' => 'nullable|exists:users,id',
        ]);

        return response()->json(
            $this->pricing->calculate(
                $data['service_type'],
                $data['format'],
                $data['color_mode'],
                $data['quantity'],
                $data['pages'] ?? 1,
                $data['printer_id'] ?? null
            )
        );
    }

    public function destroy(Request $request, Order $order): JsonResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        if ($order->file_path) {
            Storage::disk('public')->delete($order->file_path);
        }

        foreach ($order->files as $file) {
            Storage::disk('public')->delete($file->file_path);
        }

        $order->delete();

        return response()->json(['message' => 'Commande supprimée.']);
    }

    protected function baseQuery(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return Order::query();
        }

        if ($user->isPrinter()) {
            return Order::where('printer_id', $user->id)->whereNotNull('printer_id');
        }

        return Order::where('user_id', $user->id);
    }

    protected function authorizeOrder(Request $request, Order $order, bool $manage = false): void
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return;
        }

        if ($user->isPrinter() && $user->isApprovedPrinter()) {
            if ($order->printer_id === $user->id) {
                return;
            }

            if (! $manage && $order->status === 'pending' && $order->printer_id === null) {
                return;
            }
        }

        if (! $manage && $order->user_id === $user->id) {
            return;
        }

        abort(403, 'Accès non autorisé à cette commande.');
    }
}
