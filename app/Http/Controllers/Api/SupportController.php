<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupportController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = SupportTicket::with(['user:id,name,email']);

        if (! $request->user()->isAdmin()) {
            $query->where('user_id', $request->user()->id);
        }

        return response()->json($query->latest()->paginate(10));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
            'order_id' => 'nullable|exists:orders,id',
            'priority' => 'nullable|in:low,medium,high',
        ]);

        $ticket = SupportTicket::create([
            'ticket_number' => 'TK-'.strtoupper(Str::random(8)),
            'user_id' => $request->user()->id,
            'order_id' => $data['order_id'] ?? null,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'priority' => $data['priority'] ?? 'medium',
        ]);

        return response()->json(['message' => 'Ticket créé.', 'ticket' => $ticket], 201);
    }

    public function reply(Request $request, SupportTicket $ticket): JsonResponse
    {
        if (! $request->user()->isAdmin()) {
            abort(403);
        }

        $data = $request->validate([
            'admin_reply' => 'required|string',
            'status' => 'nullable|in:open,in_progress,resolved,closed',
        ]);

        $ticket->update([
            'admin_reply' => $data['admin_reply'],
            'status' => $data['status'] ?? 'resolved',
            'assigned_to' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Réponse envoyée.', 'ticket' => $ticket]);
    }
}
