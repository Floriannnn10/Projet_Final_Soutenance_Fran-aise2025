<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Contrôleur API pour la gestion des notifications
 *
 * @group Notifications
 *
 * Ce contrôleur gère toutes les opérations liées aux notifications du système.
 * Les notifications sont utilisées pour informer les utilisateurs des événements importants
 * comme les absences, les étudiants droppés, les cours annulés, etc.
 */
class NotificationController extends Controller
{
    /**
     * Récupérer la liste des notifications
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "current_page": 1,
     *     "data": [
     *       {
     *         "id": 1,
     *         "user_id": 2,
     *         "message": "📚 ATTENTION : L'étudiant...",
     *         "type": "assistance",
     *         "lue_le": null,
     *         "created_at": "2025-07-07T12:36:38.000000Z",
     *         "utilisateur": {
     *           "id": 2,
     *           "nom": "Dupont",
     *           "prenom": "Marie",
     *           "email": "marie.dupont@ifran.fr"
     *         }
     *       }
     *     ],
     *     "per_page": 20,
     *     "total": 1
     *   }
     * }
     */
    public function index(): JsonResponse
    {
        $notifications = Notification::with('utilisateur')->latest()->paginate(20);
        return response()->json(['success' => true, 'data' => $notifications]);
    }

    public function show($id): JsonResponse
    {
        $notification = Notification::with('utilisateur')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $notification]);
    }

    public function store(Request $request): JsonResponse
    {
        // Vérifier si c'est le nouveau format avec titre/contenu
        if ($request->has('titre') && $request->has('contenu')) {
            $request->validate([
                'titre' => 'required|string|max:100',
                'contenu' => 'required|string|max:500',
                'type' => 'required|string|in:droppé,absence,cours_annulé,cours_reporté,système,info,warning,error',
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'integer|exists:users,id'
            ]);

            $message = $request->titre . " : " . $request->contenu;

            // Utiliser la méthode creerEtEnvoyer du modèle Notification
            $notification = \App\Models\Notification::creerEtEnvoyer(
                $message,
                $request->type,
                $request->user_ids
            );

            return response()->json(['success' => true, 'data' => $notification], 201);
        } else {
            // Ancien format avec message direct
            $request->validate([
                'message' => 'required|string|max:500',
                'type' => 'required|string|in:droppé,absence,cours_annulé,cours_reporté,système,info,warning,error'
            ]);

            $notification = Notification::create([
                'message' => $request->message,
                'type' => $request->type
            ]);

            return response()->json(['success' => true, 'data' => $notification], 201);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'message' => 'sometimes|required|string|max:500',
            'type' => 'sometimes|required|string|in:droppé,absence,cours_annulé,cours_reporté,système,info,warning,error'
        ]);

        $notification = Notification::findOrFail($id);
        $notification->update($request->only(['message', 'type']));

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
