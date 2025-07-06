<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PresenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'etudiant_id' => $this->etudiant_id,
            'planning_id' => $this->planning_id,
            'statut' => $this->statut,
            'is_justifie' => $this->is_justifie,
            'commentaire' => $this->commentaire,
            'etudiant' => [
                'id' => $this->etudiant->id ?? null,
                'nom_complet' => $this->etudiant->nom_complet ?? null,
                'classe' => $this->etudiant->classe->nom ?? null,
            ],
            'planning' => [
                'id' => $this->planning->id ?? null,
                'date' => $this->planning->date ?? null,
                'heure_debut' => $this->planning->heure_debut ?? null,
                'heure_fin' => $this->planning->heure_fin ?? null,
                'matiere' => [
                    'id' => $this->planning->matiere->id ?? null,
                    'nom' => $this->planning->matiere->nom ?? null,
                ],
                'type_cours' => [
                    'id' => $this->planning->typeCours->id ?? null,
                    'nom' => $this->planning->typeCours->nom ?? null,
                ],
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 