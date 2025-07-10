# 🚀 Guide de Test Postman - API IFRAN

## 📋 Prérequis
- Postman installé
- Serveur Laravel en cours d'exécution (`php artisan serve`) 
- URL de base : `http://127.0.0.1:8000`

## 🔧 Configuration Postman

### 1. Importer la Collection
1. Ouvrez Postman
2. Cliquez sur "Import"
3. Sélectionnez le fichier `IFRAN_API_Collection.postman_collection.json`
4. La collection sera importée avec toutes les routes

### 2. Variables d'environnement
- `base_url` : `http://127.0.0.1:8000` (déjà configuré)

## 🧪 Tests Rapides

### Test 1 : Vérifier que l'API fonctionne
**GET** `{{base_url}}/api/health`
- **Attendu** : Status 200 avec message de succès

### Test 2 : Récupérer tous les étudiants
**GET** `{{base_url}}/api/etudiants`
- **Attendu** : Status 200 avec liste des étudiants

### Test 3 : Rechercher un étudiant
**GET** `{{base_url}}/api/etudiants/search?q=Dupont`
- **Attendu** : Status 200 avec résultats de recherche

### Test 4 : Créer un nouvel étudiant
**POST** `{{base_url}}/api/etudiants`
**Headers** :
```
Accept: application/json
Content-Type: application/json
```
**Body** :
```json
{
    "nom": "Test",
    "prenom": "Étudiant",
    "email": "test.etudiant@student.ifran.fr",
    "date_naissance": "2000-01-15",
    "classe_id": 1
}
```
- **Attendu** : Status 201 avec données de l'étudiant créé

### Test 5 : Récupérer toutes les classes
**GET** `{{base_url}}/api/classes`
- **Attendu** : Status 200 avec liste des classes

### Test 6 : Récupérer toutes les matières
**GET** `{{base_url}}/api/matieres`
- **Attendu** : Status 200 avec liste des matières

### Test 7 : Récupérer toutes les notifications
**GET** `{{base_url}}/api/notifications`
- **Attendu** : Status 200 avec liste des notifications

## 🚨 Tests d'Alertes

### Test 8 : Détecter les étudiants droppés
**POST** `{{base_url}}/api/alertes/detection`
**Body** :
```json
{
    "seuil_absence": 3,
    "periode_jours": 30
}
```
- **Attendu** : Status 200 avec résultats de détection

### Test 9 : Récupérer les étudiants droppés
**GET** `{{base_url}}/api/alertes/etudiants/droppes`
- **Attendu** : Status 200 avec liste des étudiants droppés

## 📊 Données de Test Disponibles

Grâce aux seeders, vous avez :
- ✅ 38 utilisateurs (admin, coordinateurs, enseignants, étudiants, parents)
- ✅ 7 classes
- ✅ 8 matières
- ✅ 3 types de cours
- ✅ Relations parent-étudiant

### Comptes de test :
- **Admin** : `admin@ifran.fr` / `password`
- **Coordinateur** : `marie.dupont@ifran.fr` / `password`
- **Enseignant** : `sophie.bernard@ifran.fr` / `password`
- **Étudiant** : `lucas.dubois@student.ifran.fr` / `password`
- **Parent** : `marc.dubois@parent.ifran.fr` / `password`

## 🔍 Vérification des Réponses

### Réponse de succès typique :
```json
{
    "success": true,
    "data": [...],
    "message": "Opération réussie"
}
```

### Réponse d'erreur typique :
```json
{
    "success": false,
    "message": "Message d'erreur",
    "errors": {...}
}
```

## 🎯 Ordre de Test Recommandé

1. **Health Check** - Vérifier que l'API fonctionne
2. **GET /api/etudiants** - Vérifier les données existantes
3. **GET /api/classes** - Vérifier les classes
4. **GET /api/matieres** - Vérifier les matières
5. **POST /api/etudiants** - Tester la création
6. **GET /api/etudiants/search** - Tester la recherche
7. **POST /api/alertes/detection** - Tester les alertes

## 🚨 En cas de problème

### Erreur 500 (Internal Server Error)
- Vérifiez que le serveur Laravel fonctionne
- Consultez les logs dans `storage/logs/laravel.log`

### Erreur 404 (Not Found)
- Vérifiez l'URL de la route
- Vérifiez que la route existe avec `php artisan route:list`

### Erreur 422 (Validation Error)
- Vérifiez le format des données envoyées
- Consultez les messages d'erreur dans la réponse

## 📞 Support
Si vous rencontrez des problèmes, vérifiez :
1. Le serveur Laravel est-il démarré ?
2. La base de données est-elle remplie ?
3. Les routes sont-elles correctement définies ? 
