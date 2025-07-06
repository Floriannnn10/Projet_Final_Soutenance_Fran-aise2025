<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EtudiantResource extends JsonResource
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
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'nom_complet' => $this->nom_complet,
            'email' => $this->email,
            'date_naissance' => $this->date_naissance,
            'telephone' => $this->telephone,
            'adresse' => $this->adresse,
            'classe_id' => $this->classe_id,
            'classe' => [
                'id' => $this->classe->id ?? null,
                'nom' => $this->classe->nom ?? null,
                'niveau' => $this->classe->niveau ?? null,
            ],
            'parents' => $this->whenLoaded('parents', function () {
                return $this->parents->map(function ($parent) {
                    return [
                        'id' => $parent->id,
                        'nom' => $parent->nom,
                        'prenom' => $parent->prenom,
                        'email' => $parent->email,
                    ];
                });
            }),
            'utilisateur' => $this->whenLoaded('utilisateur', function () {
                return [
                    'id' => $this->utilisateur->id ?? null,
                    'email' => $this->utilisateur->email ?? null,
                    'role' => $this->utilisateur->role->nom ?? null,
                ];
            }),
            'statistiques' => $this->when($request->include_stats, function () {
                return [
                    'taux_global' => $this->calculerTauxPresenceGlobal(),
                    'note_assiduite' => $this->calculerNoteAssiduite(),
                    'est_droppe' => $this->estDroppeGlobal(),
                    'necessite_assistance' => $this->necessiteAssistanceGlobal(),
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 