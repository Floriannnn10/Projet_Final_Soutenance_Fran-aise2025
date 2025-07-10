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
        $user = \Auth::user();
        // Validation des données reçues
        $validated = $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'planning_id' => 'required|exists:plannings,id',
            'statut' => 'required|in:present,absent,retard',
        ]);

        $planning = \App\Models\Planning::find($validated['planning_id']);
        if (!$planning || !$planning->typeCours) {
            return response()->json(['success' => false, 'message' => 'Planning ou type de cours introuvable'], 422);
        }
        $typeCours = $planning->typeCours->nom;

        // Restriction d'accès selon le rôle et le type de cours
        if ($user->isParent() || $user->isEtudiant()) {
            return response()->json(['success' => false, 'message' => 'Action non autorisée'], 403);
        }
        if ($user->isEnseignant() && $typeCours !== \App\Models\TypeCours::PRESENTIEL) {
            return response()->json(['success' => false, 'message' => 'Les enseignants ne peuvent enregistrer que les présences en présentiel'], 403);
        }
        // Le coordinateur et l'admin peuvent tout faire

        $validated['enregistre_par_user_id'] = $user->id;
        $presence = Presence::create($validated);
        return response()->json(['success' => true, 'data' => $presence], 201);
    }

    /**
     * Mettre à jour une présence
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = \Auth::user();
        $presence = Presence::findOrFail($id);
        $planning = $presence->planning;
        if (!$planning || !$planning->typeCours) {
            return response()->json(['success' => false, 'message' => 'Planning ou type de cours introuvable'], 422);
        }
        $typeCours = $planning->typeCours->nom;

        // Restriction d'accès selon le rôle et le type de cours
        if ($user->isParent() || $user->isEtudiant()) {
            return response()->json(['success' => false, 'message' => 'Action non autorisée'], 403);
        }
        if ($user->isEnseignant() && $typeCours !== \App\Models\TypeCours::PRESENTIEL) {
            return response()->json(['success' => false, 'message' => 'Les enseignants ne peuvent modifier que les présences en présentiel'], 403);
        }
        // Le coordinateur et l'admin peuvent tout faire

        $validated = $request->validate([
            'statut' => 'sometimes|in:present,absent,retard',
        ]);
        $validated['enregistre_par_user_id'] = $user->id;
        $presence->update($validated);
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