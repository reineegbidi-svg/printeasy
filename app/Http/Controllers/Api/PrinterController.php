<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PricingRule;
use App\Models\PrinterAvailability;
use App\Services\OrderAssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class PrinterController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $printerId = $request->user()->id;

        $printer = $request->user();

        $stats = [
            'available_pool' => $printer->isApprovedPrinter()
                ? Order::whereNull('printer_id')->where('status', 'pending')->count()
                : 0,
            'pending' => Order::where('printer_id', $printerId)->where('status', 'accepted')->count(),
            'in_progress' => Order::where('printer_id', $printerId)->whereIn('status', ['in_progress'])->count(),
            'completed_today' => Order::where('printer_id', $printerId)
                ->where('status', 'completed')
                ->whereDate('completed_at', today())
                ->count(),
            'revenue_month' => Payment::whereHas('order', fn ($q) => $q->where('printer_id', $printerId))
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
        ];

        $recent = Order::where('printer_id', $printerId)
            ->with('user:id,name,email')
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'stats' => array_merge($stats, [
                'is_available' => $printer->is_available,
                'is_approved' => $printer->is_approved,
            ]),
            'recent_orders' => $recent,
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $printerId = $request->user()->id;

        $byStatus = Order::where('printer_id', $printerId)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $completed = Order::where('printer_id', $printerId)->where('status', 'completed')->count();
        $revenueTotal = Payment::whereHas('order', fn ($q) => $q->where('printer_id', $printerId))
            ->where('status', 'completed')
            ->sum('amount');

        $monthly = Order::where('printer_id', $printerId)
            ->where('status', 'completed')
            ->whereYear('completed_at', now()->year)
            ->get(['total_price', 'completed_at'])
            ->groupBy(fn ($o) => $o->completed_at?->month)
            ->map(fn ($group, $month) => [
                'month' => (int) $month,
                'count' => $group->count(),
                'revenue' => $group->sum('total_price'),
            ])
            ->sortBy('month')
            ->values();

        return response()->json([
            'by_status' => $byStatus,
            'completed_total' => $completed,
            'revenue_total' => $revenueTotal,
            'monthly' => $monthly,
        ]);
    }

    public function pricingRules(Request $request): JsonResponse
    {
        return response()->json(
            PricingRule::where('printer_id', $request->user()->id)->get()
        );
    }

    public function storePricingRule(Request $request): JsonResponse
    {
        $data = $request->validate([
            'service_type' => 'required|in:print,photocopy,scan',
            'format' => 'required|in:A4,A3,A5,letter',
            'color_mode' => 'required|in:color,bw',
            'price_per_unit' => 'required|numeric|min:0',
            'setup_fee' => 'nullable|numeric|min:0',
        ]);

        $rule = PricingRule::updateOrCreate(
            [
                'printer_id' => $request->user()->id,
                'service_type' => $data['service_type'],
                'format' => $data['format'],
                'color_mode' => $data['color_mode'],
            ],
            array_merge($data, ['is_active' => true])
        );

        return response()->json(['message' => 'Tarif enregistré.', 'rule' => $rule]);
    }

    public function availabilities(Request $request): JsonResponse
    {
        return response()->json(
            PrinterAvailability::where('printer_id', $request->user()->id)->orderBy('day_of_week')->get()
        );
    }

    public function storeAvailability(Request $request): JsonResponse
    {
        $data = $request->validate([
            'day_of_week' => 'required|integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_available' => 'boolean',
        ]);

        $avail = PrinterAvailability::updateOrCreate(
            ['printer_id' => $request->user()->id, 'day_of_week' => $data['day_of_week']],
            $data
        );

        $request->user()->update(['is_available' => true]);

        return response()->json(['message' => 'Disponibilité enregistrée.', 'availability' => $avail]);
    }

    public function availableOrders(Request $request): JsonResponse
    {
        $printer = $request->user();

        if (! $printer->isApprovedPrinter()) {
            return response()->json([
                'message' => 'Compte en attente de validation par un administrateur.',
                'data' => [],
            ]);
        }

        if (! $printer->is_available) {
            return response()->json([
                'message' => 'Activez votre disponibilité pour consulter les commandes en attente.',
                'data' => [],
            ]);
        }

        $orders = Order::query()
            ->where('status', 'pending')
            ->whereNull('printer_id')
            ->with('user:id,name,email,phone')
            ->latest()
            ->paginate($request->integer('per_page', 10));

        return response()->json($orders);
    }

    public function acceptOrder(Request $request, Order $order, OrderAssignmentService $assignment): JsonResponse
    {
        try {
            $accepted = $assignment->accept($order, $request->user());

            return response()->json([
                'message' => 'Commande acceptée. Elle vous a été attribuée.',
                'order' => $accepted,
            ]);
        } catch (ConflictHttpException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }
    }

    public function toggleAvailability(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->update(['is_available' => ! $user->is_available]);

        return response()->json([
            'message' => $user->is_available ? 'Vous êtes disponible.' : 'Vous êtes indisponible.',
            'is_available' => $user->is_available,
        ]);
    }
}
