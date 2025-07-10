<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Etudiant;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;

/**
 * @group Alertes
 *
 * Détection et gestion des étudiants droppés et nécessitant une assistance
 */
class AlerteDroppeController extends Controller
{
    /**
     * Liste des étudiants droppés
     *
     * Récupère la liste des étudiants droppés et nécessitant une assistance,
     * avec leurs taux de présence et matières concernées.
     *
     * @response 200 {
     *   "etudiantsDroppes": [
     *     {
     *       "id": 1,
     *       "nom_complet": "Jean Dupont",
     *       "classe": "L1 Informatique",
     *       "taux_global": 25.5,
     *       "matieres_droppees": ["Mathématiques", "Physique"]
     *     }
     *   ],
     *   "etudiantsAssistance": [
     *     {
     *       "id": 2,
     *       "nom_complet": "Marie Martin",
     *       "classe": "L2 Informatique",
     *       "taux_global": 45.2,
     *       "matieres_assistance": ["Algorithmes"]
     *     }
     *   ],
     *   "totalDroppes": 1,
     *   "totalAssistance": 1,
     *   "timestamp": "2025-01-07T12:00:00.000000Z"
     * }
     */
    public function getEtudiantsDroppes(): JsonResponse
    {
        $etudiantsDroppes = [];
        $etudiantsAssistance = [];
        $etudiants = Etudiant::with(['classe', 'parents'])->get();

        foreach ($etudiants as $etudiant) {
            $tauxGlobal = $etudiant->calculerTauxPresenceGlobal();
            $matieresDroppees = [];
            $matieresAssistance = [];

            if ($etudiant->estDroppeGlobal()) {
                $matieresDroppees[] = 'toutes les matières';
            } elseif ($etudiant->necessiteAssistanceGlobal()) {
                $matieresAssistance[] = 'toutes les matières';
            }

            $matieres = Matiere::all();
            foreach ($matieres as $matiere) {
                if ($etudiant->estDroppe($matiere->id)) {
                    $matieresDroppees[] = $matiere->nom;
                } elseif ($etudiant->necessiteAssistance($matiere->id)) {
                    $matieresAssistance[] = $matiere->nom;
                }
            }

            if (!empty($matieresDroppees)) {
                $etudiantsDroppes[] = [
                    'id' => $etudiant->id,
                    'nom_complet' => $etudiant->nom_complet,
                    'classe' => $etudiant->classe->nom ?? 'N/A',
                    'taux_global' => $tauxGlobal,
                    'matieres_droppees' => array_unique($matieresDroppees)
                ];
            }

            if (!empty($matieresAssistance)) {
                $etudiantsAssistance[] = [
                    'id' => $etudiant->id,
                    'nom_complet' => $etudiant->nom_complet,
                    'classe' => $etudiant->classe->nom ?? 'N/A',
                    'taux_global' => $tauxGlobal,
                    'matieres_assistance' => array_unique($matieresAssistance)
                ];
            }
        }

        return response()->json([
            'etudiantsDroppes' => $etudiantsDroppes,
            'etudiantsAssistance' => $etudiantsAssistance,
            'totalDroppes' => count($etudiantsDroppes),
            'totalAssistance' => count($etudiantsAssistance),
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Déclencher la détection
     *
     * Déclenche manuellement la détection des étudiants droppés
     * en exécutant la commande Artisan correspondante.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Détection des étudiants droppés déclenchée avec succès",
     *   "output": "Détection terminée. 3 étudiants droppés détectés."
     * }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Erreur lors de la détection : [message d'erreur]"
     * }
     */
    public function declencherDetection(): JsonResponse
    {
        try {
            Artisan::call('ifran:detecter-droppes');

            return response()->json([
                'success' => true,
                'message' => 'Détection des étudiants droppés déclenchée avec succès',
                'output' => Artisan::output()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la détection : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Envoyer des notifications
     *
     * Envoie des notifications manuelles pour un étudiant spécifique
     * (type 'droppe' ou 'assistance').
     *
     * @bodyParam etudiant_id integer required L'ID de l'étudiant. Example: 1
     * @bodyParam type string required Le type de notification (droppe ou assistance). Example: droppe
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Notifications 'droppé' envoyées pour Jean Dupont"
     * }
     *
     * @response 422 {
     *   "message": "Les données fournies sont invalides.",
     *   "errors": {
     *     "etudiant_id": ["L'étudiant sélectionné est invalide."],
     *     "type": ["Le type sélectionné est invalide."]
     *   }
     * }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Erreur lors de l'envoi des notifications : [message d'erreur]"
     * }
     */
    public function envoyerNotifications(Request $request): JsonResponse
    {
        $request->validate([
            'etudiant_id' => 'required',
            'type' => 'required|in:droppe,assistance'
        ]);

        // Accepter un seul ID ou un tableau d'IDs
        $ids = is_array($request->etudiant_id) ? $request->etudiant_id : [$request->etudiant_id];
        $success = [];
        $errors = [];

        foreach ($ids as $id) {
            $etudiant = Etudiant::find($id);
            if (!$etudiant) {
                $errors[] = "L'étudiant avec l'ID $id n'existe pas.";
                continue;
            }
            try {
                if ($request->type === 'droppe') {
                    $etudiant->envoyerNotificationDroppe();
                    $success[] = "Notifications 'droppé' envoyées pour {$etudiant->nom_complet}";
                } else {
                    $etudiant->envoyerNotificationAssistance();
                    $success[] = "Notifications 'assistance' envoyées pour {$etudiant->nom_complet}";
                }
            } catch (\Exception $e) {
                $errors[] = "Erreur pour l'étudiant {$etudiant->nom_complet} : " . $e->getMessage();
            }
        }

        if (!empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => 'Certaines notifications n\'ont pas pu être envoyées.',
                'details' => $errors,
                'envoyees' => $success
            ], 207); // 207 Multi-Status
        }

        return response()->json([
            'success' => true,
            'message' => 'Notifications envoyées avec succès.',
            'details' => $success
        ]);
    }
}
