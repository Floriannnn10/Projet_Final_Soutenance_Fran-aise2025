<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Etudiant;
use App\Models\Classe;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

/**
 * Contrôleur API pour la gestion des étudiants
 */
class EtudiantController extends Controller
{
    /**
     * Récupérer la liste de tous les étudiants avec filtres avancés
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Récupérer les paramètres de filtrage
            $classeId = $request->get('classe_id');
            $search = $request->get('search');
            $perPage = $request->get('per_page', 15);
            $sortBy = $request->get('sort_by', 'nom');
            $sortOrder = $request->get('sort_order', 'asc');
            $status = $request->get('status'); // droppe, assistance, normal
            $dateDebut = $request->get('date_debut');
            $dateFin = $request->get('date_fin');

            // Construire la requête de base avec les relations
            $query = Etudiant::with(['classe', 'parents', 'utilisateur']);

            // Filtre par classe
            if ($classeId) {
                $query->where('classe_id', $classeId);
            }

            // Recherche textuelle
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nom', 'LIKE', "%{$search}%")
                      ->orWhere('prenom', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            // Tri
            if (in_array($sortBy, ['nom', 'prenom', 'email', 'created_at'])) {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Récupérer les étudiants avec pagination
            $etudiants = $query->paginate($perPage);

            // Filtrer par statut si demandé
            if ($status) {
                $etudiants->getCollection()->transform(function ($etudiant) use ($status, $dateDebut, $dateFin) {
                    $tauxGlobal = $etudiant->calculerTauxPresenceGlobal($dateDebut, $dateFin);
                    
                    switch ($status) {
                        case 'droppe':
                            return $etudiant->estDroppeGlobal($dateDebut, $dateFin) ? $etudiant : null;
                        case 'assistance':
                            return $etudiant->necessiteAssistanceGlobal($dateDebut, $dateFin) ? $etudiant : null;
                        case 'normal':
                            return (!$etudiant->estDroppeGlobal($dateDebut, $dateFin) && 
                                   !$etudiant->necessiteAssistanceGlobal($dateDebut, $dateFin)) ? $etudiant : null;
                        default:
                            return $etudiant;
                    }
                });
                
                // Supprimer les éléments null
                $etudiants->setCollection($etudiants->getCollection()->filter());
            }

            return response()->json([
                'success' => true,
                'data' => $etudiants,
                'filters' => [
                    'classe_id' => $classeId,
                    'search' => $search,
                    'status' => $status,
                    'date_debut' => $dateDebut,
                    'date_fin' => $dateFin,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder
                ],
                'message' => 'Liste des étudiants récupérée avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des étudiants', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des étudiants'
            ], 500);
        }
    }

    /**
     * Récupérer un étudiant spécifique
     */
    public function show(int $id): JsonResponse
    {
        try {
            $etudiant = Etudiant::with([
                'classe', 
                'parents', 
                'utilisateur',
                'presences.planning.matiere'
            ])->findOrFail($id);

            $tauxGlobal = $etudiant->calculerTauxPresenceGlobal();
            $noteAssiduite = $etudiant->calculerNoteAssiduite();
            $estDroppe = $etudiant->estDroppeGlobal();
            $necessiteAssistance = $etudiant->necessiteAssistanceGlobal();

            return response()->json([
                'success' => true,
                'data' => [
                    'etudiant' => $etudiant,
                    'statistiques' => [
                        'taux_global' => $tauxGlobal,
                        'note_assiduite' => $noteAssiduite,
                        'est_droppe' => $estDroppe,
                        'necessite_assistance' => $necessiteAssistance
                    ]
                ],
                'message' => 'Données de l\'étudiant récupérées avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Étudiant non trouvé'
            ], 404);
        }
    }

    /**
     * Créer un nouvel étudiant
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:255',
                'prenom' => 'required|string|max:255',
                'email' => 'required|email|unique:etudiants,email',
                'date_naissance' => 'required|date',
                'classe_id' => 'required|exists:classes,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $etudiant = Etudiant::create($request->all());
            $etudiant->load(['classe', 'parents', 'utilisateur']);

            return response()->json([
                'success' => true,
                'data' => $etudiant,
                'message' => 'Étudiant créé avec succès'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'étudiant'
            ], 500);
        }
    }

    /**
     * Mettre à jour un étudiant
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $etudiant = Etudiant::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nom' => 'sometimes|required|string|max:255',
                'prenom' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:etudiants,email,' . $id,
                'classe_id' => 'sometimes|required|exists:classes,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Données invalides',
                    'errors' => $validator->errors()
                ], 422);
            }

            $etudiant->update($request->all());
            $etudiant->load(['classe', 'parents', 'utilisateur']);

            return response()->json([
                'success' => true,
                'data' => $etudiant,
                'message' => 'Étudiant mis à jour avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'étudiant'
            ], 500);
        }
    }

    /**
     * Supprimer un étudiant
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $etudiant = Etudiant::findOrFail($id);
            
            if ($etudiant->presences()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer cet étudiant car il a des présences'
                ], 400);
            }

            $etudiant->delete();

            return response()->json([
                'success' => true,
                'message' => 'Étudiant supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'étudiant'
            ], 500);
        }
    }

    /**
     * Récupérer les statistiques d'un étudiant
     */
    public function statistiques(int $id, Request $request): JsonResponse
    {
        try {
            $etudiant = Etudiant::findOrFail($id);
            
            $dateDebut = $request->get('date_debut');
            $dateFin = $request->get('date_fin');

            $tauxGlobal = $etudiant->calculerTauxPresenceGlobal($dateDebut, $dateFin);
            $noteAssiduite = $etudiant->calculerNoteAssiduite(null, $dateDebut, $dateFin);
            
            $absencesNonJustifiees = $etudiant->getAbsencesNonJustifiees();
            $absencesJustifiees = $etudiant->getAbsencesJustifiees();

            return response()->json([
                'success' => true,
                'data' => [
                    'etudiant' => [
                        'id' => $etudiant->id,
                        'nom_complet' => $etudiant->nom_complet,
                        'classe' => $etudiant->classe->nom ?? 'N/A'
                    ],
                    'statistiques_globales' => [
                        'taux_presence' => $tauxGlobal,
                        'note_assiduite' => $noteAssiduite,
                        'est_droppe' => $etudiant->estDroppeGlobal($dateDebut, $dateFin),
                        'necessite_assistance' => $etudiant->necessiteAssistanceGlobal($dateDebut, $dateFin)
                    ],
                    'absences' => [
                        'non_justifiees' => $absencesNonJustifiees->count(),
                        'justifiees' => $absencesJustifiees->count(),
                        'total' => $absencesNonJustifiees->count() + $absencesJustifiees->count()
                    ]
                ],
                'message' => 'Statistiques récupérées avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques'
            ], 500);
        }
    }
} 