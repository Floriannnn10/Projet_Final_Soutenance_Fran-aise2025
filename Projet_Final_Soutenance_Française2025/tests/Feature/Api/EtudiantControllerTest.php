<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Etudiant;
use App\Models\Classe;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class EtudiantControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer un utilisateur pour les tests
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_list_all_etudiants()
    {
        // Créer des étudiants de test
        $classe = Classe::factory()->create();
        Etudiant::factory()->count(3)->create(['classe_id' => $classe->id]);

        $response = $this->actingAs($this->user)
                        ->getJson('/api/etudiants');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'data' => [
                            '*' => [
                                'id',
                                'nom',
                                'prenom',
                                'email',
                                'classe_id',
                                'created_at',
                                'updated_at'
                            ]
                        ],
                        'current_page',
                        'per_page',
                        'total'
                    ],
                    'message'
                ])
                ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_show_specific_etudiant()
    {
        $classe = Classe::factory()->create();
        $etudiant = Etudiant::factory()->create(['classe_id' => $classe->id]);

        $response = $this->actingAs($this->user)
                        ->getJson("/api/etudiants/{$etudiant->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'etudiant' => [
                            'id',
                            'nom',
                            'prenom',
                            'email',
                            'classe_id',
                            'classe'
                        ],
                        'statistiques' => [
                            'taux_global',
                            'note_assiduite',
                            'est_droppe',
                            'necessite_assistance'
                        ]
                    ],
                    'message'
                ])
                ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_create_new_etudiant()
    {
        $classe = Classe::factory()->create();
        
        $etudiantData = [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@test.com',
            'date_naissance' => '2000-01-01',
            'classe_id' => $classe->id,
            'telephone' => '0123456789'
        ];

        $response = $this->actingAs($this->user)
                        ->postJson('/api/etudiants', $etudiantData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'nom',
                        'prenom',
                        'email',
                        'classe_id'
                    ],
                    'message'
                ])
                ->assertJson(['success' => true]);

        $this->assertDatabaseHas('etudiants', [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@test.com'
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_etudiant()
    {
        $response = $this->actingAs($this->user)
                        ->postJson('/api/etudiants', []);

        $response->assertStatus(422)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'errors' => [
                        'nom',
                        'prenom',
                        'email',
                        'date_naissance',
                        'classe_id'
                    ]
                ])
                ->assertJson(['success' => false]);
    }

    /** @test */
    public function it_can_update_etudiant()
    {
        $classe = Classe::factory()->create();
        $etudiant = Etudiant::factory()->create(['classe_id' => $classe->id]);

        $updateData = [
            'nom' => 'Martin',
            'prenom' => 'Pierre'
        ];

        $response = $this->actingAs($this->user)
                        ->putJson("/api/etudiants/{$etudiant->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'id',
                        'nom',
                        'prenom'
                    ],
                    'message'
                ])
                ->assertJson(['success' => true]);

        $this->assertDatabaseHas('etudiants', [
            'id' => $etudiant->id,
            'nom' => 'Martin',
            'prenom' => 'Pierre'
        ]);
    }

    /** @test */
    public function it_can_delete_etudiant()
    {
        $classe = Classe::factory()->create();
        $etudiant = Etudiant::factory()->create(['classe_id' => $classe->id]);

        $response = $this->actingAs($this->user)
                        ->deleteJson("/api/etudiants/{$etudiant->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message'
                ])
                ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('etudiants', ['id' => $etudiant->id]);
    }

    /** @test */
    public function it_can_get_etudiant_statistiques()
    {
        $classe = Classe::factory()->create();
        $etudiant = Etudiant::factory()->create(['classe_id' => $classe->id]);

        $response = $this->actingAs($this->user)
                        ->getJson("/api/etudiants/{$etudiant->id}/statistiques");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'etudiant' => [
                            'id',
                            'nom_complet',
                            'classe'
                        ],
                        'statistiques_globales' => [
                            'taux_presence',
                            'note_assiduite',
                            'est_droppe',
                            'necessite_assistance'
                        ],
                        'absences' => [
                            'non_justifiees',
                            'justifiees',
                            'total'
                        ]
                    ],
                    'message'
                ])
                ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_can_search_etudiants()
    {
        $classe = Classe::factory()->create();
        Etudiant::factory()->create([
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'classe_id' => $classe->id
        ]);

        $response = $this->actingAs($this->user)
                        ->getJson('/api/etudiants/search?q=Dupont');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        '*' => [
                            'id',
                            'nom',
                            'prenom',
                            'email'
                        ]
                    ],
                    'message'
                ])
                ->assertJson(['success' => true]);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_etudiant()
    {
        $response = $this->actingAs($this->user)
                        ->getJson('/api/etudiants/999');

        $response->assertStatus(404)
                ->assertJson(['success' => false]);
    }
} 