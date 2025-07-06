<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Etudiant;
use App\Models\Matiere;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Contrôleur API pour la gestion des alertes droppé
 */
class AlerteDroppeController extends Controller
{
    /**
     * Récupérer les étudiants droppés et nécessitant une assistance
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
     * Déclencher manuellement la détection des étudiants droppés
     */
    public function declencherDetection(): JsonResponse
    {
        try {
            \Artisan::call('ifran:detecter-droppes');
            
            return response()->json([
                'success' => true,
                'message' => 'Détection des étudiants droppés déclenchée avec succès',
                'output' => \Artisan::output()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la détection : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Envoyer des notifications manuelles pour un étudiant spécifique
     */
    public function envoyerNotifications(Request $request): JsonResponse
    {
        $request->validate([
            'etudiant_id' => 'required|exists:etudiants,id',
            'type' => 'required|in:droppe,assistance'
        ]);
        
        try {
            $etudiant = Etudiant::findOrFail($request->etudiant_id);
            
            if ($request->type === 'droppe') {
                $etudiant->envoyerNotificationDroppe();
                $message = "Notifications 'droppé' envoyées pour {$etudiant->nom_complet}";
            } else {
                $etudiant->envoyerNotificationAssistance();
                $message = "Notifications 'assistance' envoyées pour {$etudiant->nom_complet}";
            }
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi des notifications : ' . $e->getMessage()
            ], 500);
        }
    }
} 