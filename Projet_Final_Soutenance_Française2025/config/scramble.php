<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Scramble Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for Scramble API documentation.
    |
    */

    'route' => [
        'uri' => 'api-docs',
        'middleware' => ['web'],
    ],

    'info' => [
        'title' => 'API IFRAN - Système de Gestion des Présences',
        'description' => 'Documentation complète de l\'API pour le système de gestion des présences de l\'IFRAN',
        'version' => '1.0.0',
        'contact' => [
            'name' => 'Support IFRAN',
            'email' => 'support@ifran.fr',
        ],
        'license' => [
            'name' => 'MIT',
            'url' => 'https://opensource.org/licenses/MIT',
        ],
    ],

    'servers' => [
        [
            'url' => env('APP_URL', 'http://localhost:8000'),
            'description' => 'Serveur de développement',
        ],
    ],

    'auth' => [
        'type' => 'bearer',
        'name' => 'Authorization',
        'description' => 'Token d\'authentification Bearer (Laravel Sanctum)',
    ],

    'extensions' => [
        // Extensions personnalisées si nécessaire
    ],

    'tags' => [
        'Étudiants' => 'Gestion des étudiants et de leurs informations',
        'Présences' => 'Gestion des présences et absences',
        'Classes' => 'Gestion des classes et groupes',
        'Matières' => 'Gestion des matières et cours',
        'Notifications' => 'Système de notifications automatiques',
        'Alertes' => 'Détection et gestion des étudiants droppés',
        'Justifications' => 'Gestion des justifications d\'absence',
        'Statistiques' => 'Statistiques et rapports',
    ],

    'security' => [
        'sanctum' => [
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT',
        ],
    ],
];
