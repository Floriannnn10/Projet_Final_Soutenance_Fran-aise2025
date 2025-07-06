<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Presence;
use App\Models\Etudiant;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Contrôleur API pour la gestion des présences
 */
class PresenceController extends Controller
{
    /**
     * Lister toutes les présences
     */
    public function index(Request $request): JsonResponse
    {
        $presences = Presence::with(['etudiant', 'planning.matiere', 'planning.typeCours'])->paginate(20);
        return response()->json(['success' => true, 'data' => $presences]);
    }

    /**
     * Afficher une présence spécifique
     */
    public function show($id): JsonResponse
    {
        $presence = Presence::with(['etudiant', 'planning.matiere', 'planning.typeCours'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $presence]);
    }

    /**
     * Créer une nouvelle présence
     */
    public function store(Request $request): JsonResponse
    {
        $presence = Presence::create($request->all());
        return response()->json(['success' => true, 'data' => $presence], 201);
    }

    /**
     * Mettre à jour une présence
     */
    public function update(Request $request, $id): JsonResponse
    {
        $presence = Presence::findOrFail($id);
        $presence->update($request->all());
        return response()->json(['success' => true, 'data' => $presence]);
    }

    /**
     * Supprimer une présence
     */
    public function destroy($id): JsonResponse
    {
        $presence = Presence::findOrFail($id);
        $presence->delete();
        return response()->json(['success' => true, 'message' => 'Présence supprimée']);
    }
} 