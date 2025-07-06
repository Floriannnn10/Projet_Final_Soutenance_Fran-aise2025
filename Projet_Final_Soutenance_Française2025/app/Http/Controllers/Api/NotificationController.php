<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Contrôleur API pour la gestion des notifications
 */
class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $notifications = Notification::with('user')->latest()->paginate(20);
        return response()->json(['success' => true, 'data' => $notifications]);
    }

    public function show($id): JsonResponse
    {
        $notification = Notification::with('user')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $notification]);
    }

    public function store(Request $request): JsonResponse
    {
        $notification = Notification::create($request->all());
        return response()->json(['success' => true, 'data' => $notification], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $notification = Notification::findOrFail($id);
        $notification->update($request->all());
        return response()->json(['success' => true, 'data' => $notification]);
    }

    public function destroy($id): JsonResponse
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();
        return response()->json(['success' => true, 'message' => 'Notification supprimée']);
    }

    public function markAsRead($id): JsonResponse
    {
        $notification = Notification::findOrFail($id);
        $notification->update(['read_at' => now()]);
        return response()->json(['success' => true, 'data' => $notification]);
    }
} 