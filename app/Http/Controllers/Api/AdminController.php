<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard(): JsonResponse
    {
        $stats = [
            'users_count' => User::where('role', 'user')->count(),
            'printers_count' => User::where('role', 'printer')->count(),
            'orders_count' => Order::count(),
            'orders_pending' => Order::where('status', 'pending')->count(),
            'orders_completed' => Order::where('status', 'completed')->count(),
            'revenue_total' => Payment::where('status', 'completed')->sum('amount'),
            'revenue_month' => Payment::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];

        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // Compatible SQLite + MySQL (évite MONTH() MySQL-only)
        $ordersByMonth = Order::query()
            ->whereYear('created_at', now()->year)
            ->get(['id', 'total_price', 'created_at'])
            ->groupBy(fn ($order) => $order->created_at->month)
            ->map(fn ($group, $month) => [
                'month' => (int) $month,
                'count' => $group->count(),
                'revenue' => $group->sum('total_price'),
            ])
            ->sortBy('month')
            ->values();

        $recentOrders = Order::with(['user:id,name', 'printer:id,name'])
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'stats' => $stats,
            'orders_by_status' => $ordersByStatus,
            'orders_by_month' => $ordersByMonth,
            'recent_orders' => $recentOrders,
        ]);
    }

    public function users(Request $request): JsonResponse
    {
        $query = User::query();

        if ($role = $request->get('role')) {
            $query->where('role', $role);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return response()->json($query->latest()->paginate($request->integer('per_page', 15)));
    }

    public function updateUser(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'role' => 'sometimes|in:user,printer,admin',
            'is_active' => 'sometimes|boolean',
            'is_available' => 'sometimes|boolean',
            'is_approved' => 'sometimes|boolean',
        ]);

        $user->update($data);

        return response()->json(['message' => 'Utilisateur mis à jour.', 'user' => $user]);
    }

    public function deleteUser(User $user): JsonResponse
    {
        if ($user->isAdmin() && User::where('role', 'admin')->count() <= 1) {
            return response()->json(['message' => 'Impossible de supprimer le dernier admin.'], 422);
        }

        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé.']);
    }
}
