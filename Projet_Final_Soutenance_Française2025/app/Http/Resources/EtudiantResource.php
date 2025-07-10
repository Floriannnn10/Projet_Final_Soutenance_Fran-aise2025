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
            'date_naissance' => $this->date_naissance,
            'photo' => $this->photo,
            'classe_id' => $this->classe_id,
            'user_id' => $this->user_id,
            'classe' => $this->when($this->classe, function () {
                return [
                    'id' => $this->classe->id,
                    'nom' => $this->classe->nom,
                ];
            }),
            'user' => $this->when($this->user, function () {
                return [
                    'id' => $this->user->id,
                    'email' => $this->user->email,
                    'role' => $this->user->role ? $this->user->role->nom : null,
                ];
            }),
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