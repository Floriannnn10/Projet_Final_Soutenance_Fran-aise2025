<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EtudiantResource;
use App\Models\Etudiant;
use App\Models\Classe;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Étudiants
 *
 * Gestion des étudiants et de leurs informations
 */
class EtudiantController extends Controller
{
    /**
     * Liste des étudiants
     *
     * Récupère la liste paginée de tous les étudiants avec leurs relations.
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "nom": "Dupont",
     *       "prenom": "Jean",
     *       "email": "jean.dupont@ifran.fr",
     *       "date_naissance": "2000-01-01",
     *       "classe": {
     *         "id": 1,
     *         "nom": "L1 Informatique"
     *       }
     *     }
     *   ],
     *   "links": {},
     *   "meta": {}
     * }
     */
    public function index(): AnonymousResourceCollection
    {
        $etudiants = Etudiant::with(['user', 'classe', 'parents'])->paginate(15);
        return EtudiantResource::collection($etudiants);
    }

    /**
     * Détails d'un étudiant
     *
     * Récupère les informations détaillées d'un étudiant spécifique.
     *
     * @urlParam id integer required L'ID de l'étudiant. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "nom": "Dupont",
     *     "prenom": "Jean",
     *     "email": "jean.dupont@ifran.fr",
     *     "date_naissance": "2000-01-01",
     *     "classe": {
     *       "id": 1,
     *       "nom": "L1 Informatique"
     *     },
     *     "presences": [],
     *     "justifications": []
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Étudiant non trouvé"
     * }
     */
    public function show($id): EtudiantResource
    {
        $etudiant = Etudiant::with(['user', 'classe', 'parents', 'presences', 'justifications'])->findOrFail($id);
        return new EtudiantResource($etudiant);
    }

    /**
     * Créer un étudiant
     *
     * Crée un nouvel étudiant avec un compte utilisateur associé.
     *
     * @bodyParam nom string required Le nom de l'étudiant. Example: Dupont
     * @bodyParam prenom string required Le prénom de l'étudiant. Example: Jean
     * @bodyParam email string required L'email de l'étudiant (doit être unique). Example: jean.dupont@ifran.fr
     * @bodyParam date_naissance date required La date de naissance. Example: 2000-01-01
     * @bodyParam classe_id integer required L'ID de la classe. Example: 1
     * @bodyParam password string required Le mot de passe (minimum 8 caractères). Example: password123
     *
     * @response 201 {
     *   "success": true,
     *   "message": "Étudiant créé avec succès",
     *   "data": {
     *     "id": 1,
     *     "nom": "Dupont",
     *     "prenom": "Jean",
     *     "email": "jean.dupont@ifran.fr"
     *   }
     * }
     *
     * @response 422 {
     *   "message": "Les données fournies sont invalides.",
     *   "errors": {
     *     "email": ["L'email a déjà été pris."]
     *   }
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'date_naissance' => 'required|date',
            'classe_id' => 'required|exists:classes,id',
            'password' => 'required|string|min:8',
        ]);

        // Créer d'abord l'utilisateur
        $user = \App\Models\User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => \App\Models\Role::where('nom', 'etudiant')->first()->id,
        ]);

        // Puis créer l'étudiant
        $etudiant = Etudiant::create([
            'user_id' => $user->id,
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'date_naissance' => $request->date_naissance,
            'classe_id' => $request->classe_id,
        ]);

        // Charger les relations pour la réponse
        $etudiant->load(['user', 'classe']);

        return response()->json([
            'success' => true,
            'message' => 'Étudiant créé avec succès',
            'data' => new EtudiantResource($etudiant)
        ], 201);
    }

    /**
     * Modifier un étudiant
     *
     * Met à jour les informations d'un étudiant existant.
     *
     * @urlParam id integer required L'ID de l'étudiant. Example: 1
     * @bodyParam nom string Le nom de l'étudiant. Example: Dupont
     * @bodyParam prenom string Le prénom de l'étudiant. Example: Jean
     * @bodyParam date_naissance date La date de naissance. Example: 2000-01-01
     * @bodyParam classe_id integer L'ID de la classe. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Étudiant mis à jour avec succès",
     *   "data": {
     *     "id": 1,
     *     "nom": "Dupont",
     *     "prenom": "Jean"
     *   }
     * }
     *
     * @response 404 {
     *   "message": "Étudiant non trouvé"
     * }
     */
    public function update(Request $request, $id): JsonResponse
    {
        $etudiant = Etudiant::findOrFail($id);

        $request->validate([
            'nom' => 'sometimes|string|max:255',
            'prenom' => 'sometimes|string|max:255',
            'date_naissance' => 'sometimes|date',
            'classe_id' => 'sometimes|exists:classes,id',
            'email' => 'sometimes|email|unique:users,email,' . $etudiant->user_id,
            'password' => 'sometimes|string|min:8',
        ]);

        $etudiant->update($request->only(['nom', 'prenom', 'date_naissance', 'classe_id']));

        // Mettre à jour l'utilisateur lié si email, password, nom ou prenom présents
        if ($request->hasAny(['email', 'password', 'nom', 'prenom'])) {
            $user = $etudiant->user;
            if ($request->has('email')) {
                $user->email = $request->email;
            }
            if ($request->has('password')) {
                $user->password = bcrypt($request->password);
            }
            if ($request->has('nom')) {
                $user->nom = $request->nom;
            }
            if ($request->has('prenom')) {
                $user->prenom = $request->prenom;
            }
            $user->save();
        }

        // Recharger les relations pour la réponse
        $etudiant->load(['user', 'classe', 'parents']);

        return response()->json([
            'success' => true,
            'message' => 'Étudiant mis à jour avec succès',
            'data' => new EtudiantResource($etudiant)
        ]);
    }

    /**
     * Supprimer un étudiant
     *
     * Supprime un étudiant et toutes ses données associées.
     *
     * @urlParam id integer required L'ID de l'étudiant. Example: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Étudiant supprimé avec succès"
     * }
     *
     * @response 404 {
     *   "message": "Étudiant non trouvé"
     * }
     */
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        $etudiant = Etudiant::findOrFail($id);
        // Suppression manuelle des présences liées
        $etudiant->presences()->delete();
        // Supprimer l'utilisateur lié si présent
        if ($etudiant->user) {
            $etudiant->user->delete();
        }
        $etudiant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Étudiant et utilisateur supprimés avec succès'
        ]);
    }

    /**
     * Statistiques d'un étudiant
     *
     * Récupère les statistiques détaillées d'un étudiant (taux de présence, note d'assiduité, etc.).
     *
     * @urlParam id integer required L'ID de l'étudiant. Example: 1
     *
     * @response 200 {
     *   "etudiant": {
     *     "id": 1,
     *     "nom": "Dupont",
     *     "prenom": "Jean"
     *   },
     *   "statistiques": {
     *     "taux_presence_global": 85.5,
     *     "note_assiduite": 17.1,
     *     "est_droppe": false,
     *     "necessite_assistance": false,
     *     "total_presences": 45,
     *     "total_absences": 8,
     *     "total_retards": 2,
     *     "total_justifications": 3
     *   }
     * }
     */
    public function statistiques($id): JsonResponse
    {
        $etudiant = Etudiant::with(['user', 'classe', 'presences', 'justifications'])->findOrFail($id);
        $tauxGlobal = $etudiant->calculerTauxPresenceGlobal();
        $noteAssiduite = $etudiant->calculerNoteAssiduite();
        $estDroppe = $etudiant->estDroppeGlobal();
        $necessiteAssistance = $etudiant->necessiteAssistanceGlobal();

        return response()->json([
            'etudiant' => new EtudiantResource($etudiant),
            'statistiques' => [
                'taux_presence_global' => $tauxGlobal,
                'note_assiduite' => $noteAssiduite,
                'est_droppe' => $estDroppe,
                'necessite_assistance' => $necessiteAssistance,
                'total_presences' => $etudiant->presences()->whereHas('status', function($q) { $q->where('name', 'present'); })->count(),
                'total_absences' => $etudiant->presences()->whereHas('status', function($q) { $q->where('name', 'absent'); })->count(),
                'total_retards' => $etudiant->presences()->whereHas('status', function($q) { $q->where('name', 'retard'); })->count(),
                'total_justifications' => $etudiant->justifications()->count(),
            ]
        ]);
    }

    /**
     * Rechercher des étudiants
     *
     * Recherche des étudiants par nom, prénom ou classe.
     *
     * @queryParam q string required Le terme de recherche. Example: Dupont
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "nom": "Dupont",
     *       "prenom": "Jean",
     *       "classe": {
     *         "nom": "L1 Informatique"
     *       }
     *     }
     *   ]
     * }
     */
    public function search(Request $request): AnonymousResourceCollection
    {
        $query = $request->get('q');

        $etudiants = Etudiant::with(['user', 'classe'])
            ->where('nom', 'like', "%{$query}%")
            ->orWhere('prenom', 'like', "%{$query}%")
            ->orWhereHas('classe', function($q) use ($query) {
                $q->where('nom', 'like', "%{$query}%");
            })
            ->paginate(15);

        return EtudiantResource::collection($etudiants);
    }

    /**
     * Étudiants par classe
     *
     * Récupère tous les étudiants d'une classe spécifique.
     *
     * @urlParam classe integer required L'ID de la classe. Example: 1
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "nom": "Dupont",
     *       "prenom": "Jean"
     *     }
     *   ]
     * }
     */
    public function parClasse(Classe $classe): AnonymousResourceCollection
    {
        $etudiants = $classe->etudiants()->with(['user', 'parents'])->paginate(15);
        return EtudiantResource::collection($etudiants);
    }
}
