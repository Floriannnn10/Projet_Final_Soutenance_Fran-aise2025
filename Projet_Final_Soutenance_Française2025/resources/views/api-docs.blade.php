<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentation API IFRAN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>
</head>

<body class="bg-gradient-to-br from-blue-50 to-white min-h-screen">
    <!-- Header -->
    <header class="bg-gradient-to-r from-blue-700 to-blue-500 text-white shadow-lg">
        <div class="container mx-auto px-6 py-10">
            <h1 class="text-5xl font-extrabold mb-2 tracking-tight flex items-center gap-3">
                <span>📚</span> API IFRAN
            </h1>
            <p class="text-2xl opacity-90">Système de Gestion des Présences</p>
            <p class="text-sm opacity-80 mt-2">Documentation complète de l'API REST IFRAN</p>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="bg-white/90 shadow sticky top-0 z-30 border-b border-blue-100 backdrop-blur">
        <div class="container mx-auto px-6">
            <div class="flex space-x-8 py-4 text-blue-700 font-semibold text-lg">
                <a href="#introduction" class="hover:text-blue-900 transition">Introduction</a>
                <a href="#auth" class="hover:text-blue-900 transition">Authentification</a>
                <a href="#routes" class="hover:text-blue-900 transition">Endpoints</a>
                <a href="#exemples" class="hover:text-blue-900 transition">Exemples</a>
                <a href="#erreurs" class="hover:text-blue-900 transition">Erreurs</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-10">
        <!-- Introduction -->
        <section id="introduction" class="mb-16">
            <div class="bg-white rounded-xl shadow-lg p-8 border-l-8 border-blue-500">
                <h2 class="text-3xl font-bold text-blue-700 mb-4 flex items-center gap-2"><span>ℹ️</span> Introduction
                </h2>
                <p class="text-gray-700 mb-4">
                    L'API IFRAN permet de gérer le système de présences des étudiants, les notifications automatiques,
                    la détection des étudiants en difficulté, et bien plus. Cette API REST utilise Laravel Sanctum pour
                    l'authentification.
                </p>
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-2">
                    <p class="text-blue-700">
                        <strong>Base URL :</strong> <code
                            class="bg-blue-100 px-2 py-1 rounded">http://127.0.0.1:8000/api</code>
                    </p>
                </div>
            </div>
        </section>

        <!-- Authentification -->
        <section id="auth" class="mb-16">
            <div class="bg-white rounded-xl shadow-lg p-8 border-l-8 border-green-500">
                <h2 class="text-3xl font-bold text-green-700 mb-4 flex items-center gap-2"><span>🔐</span>
                    Authentification</h2>
                <p class="text-gray-700 mb-4">
                    L'API utilise Laravel Sanctum pour l'authentification par token Bearer.
                </p>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Obtenir un Token</h3>
                <pre><code class="language-bash">curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@ifran.fr",
    "password": "password"
  }'</code></pre>
                <h3 class="text-xl font-semibold text-gray-800 mb-2 mt-6">Utiliser le Token</h3>
                <pre><code class="language-bash">curl -X GET http://127.0.0.1:8000/api/etudiants \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Accept: application/json"</code></pre>
            </div>
        </section>

        <!-- Endpoints -->
        <section id="routes" class="mb-16">
            <div class="bg-white rounded-xl shadow-lg p-8 border-l-8 border-purple-500">
                <h2 class="text-3xl font-bold text-purple-700 mb-4 flex items-center gap-2"><span>🛣️</span> Endpoints
                </h2>
                <div class="mb-8">
                    <h3 class="text-2xl font-semibold text-green-600 mb-2 flex items-center gap-2"><span>🌐</span> Route
                        Publique</h3>
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2">
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold">GET</span>
                            <code class="bg-gray-100 px-2 py-1 rounded">/api/health</code>
                            <span class="text-gray-600 text-sm">Vérifier la santé de l'API</span>
                        </div>
                    </div>
                </div>
                <h3 class="text-2xl font-semibold text-blue-600 mb-4 flex items-center gap-2"><span>🔒</span> Routes
                    Protégées</h3>
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Étudiants -->
                    <div class="border border-gray-200 rounded-lg p-4 bg-blue-50">
                        <h4 class="font-semibold text-blue-800 mb-2 flex items-center gap-2">👥 Étudiants</h4>
                        <ul class="text-sm space-y-1">
                            <li><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">GET</span>
                                <code>/api/etudiants</code> - Liste des étudiants</li>
                            <li><span
                                    class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-bold">POST</span>
                                <code>/api/etudiants</code> - Créer un étudiant</li>
                            <li><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">GET</span>
                                <code>/api/etudiants/{id}</code> - Détails d'un étudiant</li>
                            <li><span
                                    class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded text-xs font-bold">PUT</span>
                                <code>/api/etudiants/{id}</code> - Modifier un étudiant</li>
                            <li><span
                                    class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs font-bold">DELETE</span>
                                <code>/api/etudiants/{id}</code> - Supprimer un étudiant</li>
                            <li><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">GET</span>
                                <code>/api/etudiants/search</code> - Rechercher des étudiants</li>
                            <li><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">GET</span>
                                <code>/api/etudiants/{id}/statistiques</code> - Statistiques d'un étudiant</li>
                        </ul>
                    </div>
                    <!-- Présences -->
                    <div class="border border-gray-200 rounded-lg p-4 bg-green-50">
                        <h4 class="font-semibold text-green-800 mb-2 flex items-center gap-2">📊 Présences</h4>
                        <ul class="text-sm space-y-1">
                            <li><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">GET</span>
                                <code>/api/presences</code> - Liste des présences</li>
                            <li><span
                                    class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-bold">POST</span>
                                <code>/api/presences</code> - Créer une présence</li>
                            <li><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">GET</span>
                                <code>/api/presences/{id}</code> - Détails d'une présence</li>
                            <li><span
                                    class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded text-xs font-bold">PUT</span>
                                <code>/api/presences/{id}</code> - Modifier une présence</li>
                            <li><span
                                    class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs font-bold">DELETE</span>
                                <code>/api/presences/{id}</code> - Supprimer une présence</li>
                        </ul>
                    </div>
                    <!-- Classes -->
                    <div class="border border-gray-200 rounded-lg p-4 bg-yellow-50">
                        <h4 class="font-semibold text-yellow-800 mb-2 flex items-center gap-2">🏫 Classes</h4>
                        <ul class="text-sm space-y-1">
                            <li><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">GET</span>
                                <code>/api/classes</code> - Liste des classes</li>
                            <li><span
                                    class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-bold">POST</span>
                                <code>/api/classes</code> - Créer une classe</li>
                            <li><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">GET</span>
                                <code>/api/classes/{id}</code> - Détails d'une classe</li>
                            <li><span
                                    class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded text-xs font-bold">PUT</span>
                                <code>/api/classes/{id}</code> - Modifier une classe</li>
                            <li><span
                                    class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs font-bold">DELETE</span>
                                <code>/api/classes/{id}</code> - Supprimer une classe</li>
                        </ul>
                    </div>
                    <!-- Matières -->
                    <div class="border border-gray-200 rounded-lg p-4 bg-purple-50">
                        <h4 class="font-semibold text-purple-800 mb-2 flex items-center gap-2">📚 Matières</h4>
                        <ul class="text-sm space-y-1">
                            <li><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">GET</span>
                                <code>/api/matieres</code> - Liste des matières</li>
                            <li><span
                                    class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-bold">POST</span>
                                <code>/api/matieres</code> - Créer une matière</li>
                            <li><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">GET</span>
                                <code>/api/matieres/{id}</code> - Détails d'une matière</li>
                            <li><span
                                    class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded text-xs font-bold">PUT</span>
                                <code>/api/matieres/{id}</code> - Modifier une matière</li>
                            <li><span
                                    class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs font-bold">DELETE</span>
                                <code>/api/matieres/{id}</code> - Supprimer une matière</li>
                        </ul>
                    </div>
                    <!-- Justifications -->
                    <div class="border border-gray-200 rounded-lg p-4 bg-pink-50">
                        <h4 class="font-semibold text-pink-800 mb-2 flex items-center gap-2">📝 Justifications</h4>
                        <ul class="text-sm space-y-1">
                            <li><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">GET</span>
                                <code>/api/justifications</code> - Liste des justifications</li>
                            <li><span
                                    class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-bold">POST</span>
                                <code>/api/justifications</code> - Créer une justification</li>
                            <li><span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">GET</span>
                                <code>/api/justifications/{id}</code> - Détails d'une justification</li>
                            <li><span
                                    class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded text-xs font-bold">PUT</span>
                                <code>/api/justifications/{id}</code> - Modifier une justification</li>
                            <li><span
                                    class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs font-bold">DELETE</span>
                                <code>/api/justifications/{id}</code> - Supprimer une justification</li>
                        </ul>
                    </div>
                    <!-- Notifications -->
                    <div class="border border-gray-200 rounded-lg p-4 bg-orange-50">
                        <h4 class="font-semibold text-orange-800 mb-2 flex items-center gap-2">🔔 Notifications</h4>
                        <ul class="text-sm space-y-1">
                            <li><span
                                    class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">GET</span>
                                <code>/api/notifications</code> - Liste des notifications</li>
                            <li><span
                                    class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-bold">POST</span>
                                <code>/api/notifications</code> - Créer une notification</li>
                            <li><span
                                    class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">GET</span>
                                <code>/api/notifications/{id}</code> - Détails d'une notification</li>
                            <li><span
                                    class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded text-xs font-bold">PUT</span>
                                <code>/api/notifications/{id}</code> - Modifier une notification</li>
                            <li><span
                                    class="bg-red-100 text-red-700 px-2 py-0.5 rounded text-xs font-bold">DELETE</span>
                                <code>/api/notifications/{id}</code> - Supprimer une notification</li>
                            <li><span
                                    class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded text-xs font-bold">PATCH</span>
                                <code>/api/notifications/{id}/read</code> - Marquer comme lue</li>
                        </ul>
                    </div>
                    <!-- Alertes Droppé -->
                    <div class="border border-gray-200 rounded-lg p-4 bg-red-50">
                        <h4 class="font-semibold text-red-800 mb-2 flex items-center gap-2">🚨 Alertes Droppé</h4>
                        <ul class="text-sm space-y-1">
                            <li><span
                                    class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">GET</span>
                                <code>/api/alertes/etudiants/droppes</code> - Étudiants droppés</li>
                            <li><span
                                    class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-bold">POST</span>
                                <code>/api/alertes/detection</code> - Déclencher détection</li>
                            <li><span
                                    class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-bold">POST</span>
                                <code>/api/alertes/notifications</code> - Envoyer notifications</li>
                        </ul>
                    </div>
                    <!-- Statistiques -->
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <h4 class="font-semibold text-gray-800 mb-2 flex items-center gap-2">📈 Statistiques</h4>
                        <ul class="text-sm space-y-1">
                            <li><span
                                    class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-bold">GET</span>
                                <code>/api/statistiques/globales</code> - Statistiques globales</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Exemples -->
        <section id="exemples" class="mb-16">
            <div class="bg-white rounded-xl shadow-lg p-8 border-l-8 border-cyan-500">
                <h2 class="text-3xl font-bold text-cyan-700 mb-4 flex items-center gap-2"><span>💡</span> Exemples
                    d'Utilisation</h2>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Récupérer la liste des étudiants</h3>
                <pre><code class="language-bash">curl -X GET "http://127.0.0.1:8000/api/etudiants" \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Accept: application/json"</code></pre>
                <h3 class="text-xl font-semibold text-gray-800 mb-2 mt-6">Créer un nouvel étudiant</h3>
                <pre><code class="language-bash">curl -X POST "http://127.0.0.1:8000/api/etudiants" \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "nom": "Dupont",
    "prenom": "Jean",
    "email": "jean.dupont@ifran.fr",
    "date_naissance": "2000-01-01",
    "classe_id": 1
  }'</code></pre>
                <h3 class="text-xl font-semibold text-gray-800 mb-2 mt-6">Récupérer les notifications</h3>
                <pre><code class="language-bash">curl -X GET "http://127.0.0.1:8000/api/notifications" \
  -H "Authorization: Bearer VOTRE_TOKEN" \
  -H "Accept: application/json"</code></pre>
            </div>
        </section>

        <!-- Erreurs -->
        <section id="erreurs" class="mb-16">
            <div class="bg-white rounded-xl shadow-lg p-8 border-l-8 border-red-500">
                <h2 class="text-3xl font-bold text-red-700 mb-4 flex items-center gap-2"><span>❗</span> Codes d'Erreur
                </h2>
                <div class="grid md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold text-green-800 mb-2">Codes de Succès</h3>
                        <ul class="space-y-2 text-sm">
                            <li><span class="bg-green-100 text-green-800 px-2 py-1 rounded">200</span> - Succès</li>
                            <li><span class="bg-green-100 text-green-800 px-2 py-1 rounded">201</span> - Créé avec
                                succès</li>
                            <li><span class="bg-green-100 text-green-800 px-2 py-1 rounded">204</span> - Supprimé avec
                                succès</li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-red-800 mb-2">Codes d'Erreur</h3>
                        <ul class="space-y-2 text-sm">
                            <li><span class="bg-red-100 text-red-800 px-2 py-1 rounded">400</span> - Requête invalide
                            </li>
                            <li><span class="bg-red-100 text-red-800 px-2 py-1 rounded">401</span> - Non authentifié
                            </li>
                            <li><span class="bg-red-100 text-red-800 px-2 py-1 rounded">403</span> - Non autorisé</li>
                            <li><span class="bg-red-100 text-red-800 px-2 py-1 rounded">404</span> - Ressource non
                                trouvée</li>
                            <li><span class="bg-red-100 text-red-800 px-2 py-1 rounded">422</span> - Données invalides
                            </li>
                            <li><span class="bg-red-100 text-red-800 px-2 py-1 rounded">500</span> - Erreur serveur
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-r from-blue-700 to-blue-500 text-white py-8 mt-10">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2025 IFRAN - Système de Gestion des Présences</p>
            <p class="text-blue-200 text-sm mt-2">API Documentation v1.0.0</p>
        </div>
    </footer>

    <script>
        // Smooth scrolling pour les ancres
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>

</html>
