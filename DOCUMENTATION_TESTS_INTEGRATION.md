# 📋 Documentation des Tests d'Intégration - Taskforce

## 🎯 Vue d'ensemble

Cette documentation présente tous les tests d'intégration du projet Taskforce, leurs attentes et résultats attendus dans tous les cas de figure.

---

## 🔧 Tests d'Intégration Backend

### 1. **Test de Connectivité API** (`test-integration.php`)

#### **Objectif**
Vérifier que tous les endpoints API sont accessibles et répondent correctement.

#### **Endpoints testés**
- `GET /api/taches`
- `GET /api/collaborateurs` 
- `GET /api/competences`
- `GET /api/missions`

#### **Cas de test et résultats attendus**

| **Cas** | **Attente** | **Résultat Attendu** | **Action si Échec** |
|---------|-------------|---------------------|-------------------|
| **Serveur démarré** | HTTP 200-299 | ✅ OK (200) | Vérifier `php -S localhost:8000 -t public/` |
| **Serveur arrêté** | Erreur réseau | ❌ ERREUR: Connection refused | Démarrer le serveur Symfony |
| **Base de données non configurée** | HTTP 500 | ⚠️ HTTP 500 | Configurer la base de données |
| **Migrations non exécutées** | HTTP 500 | ⚠️ HTTP 500 | Exécuter `php bin/console doctrine:migrations:migrate` |

#### **Métriques de succès**
- **Taux de réussite** : 100% (4/4 endpoints)
- **Temps de réponse** : < 10 secondes par endpoint
- **Format de réponse** : JSON valide

---

### 2. **Test de Workflow Complet des Tâches** (`ApiIntegrationTest::testCompleteTacheWorkflow`)

#### **Objectif**
Tester le cycle de vie complet d'une tâche : création → assignation → mise à jour → suppression.

#### **Étapes du test**

| **Étape** | **Action** | **Attente** | **Résultat Attendu** |
|-----------|------------|-------------|---------------------|
| **1. Création compétence** | POST `/api/competences` | HTTP 201 | Compétence créée avec ID |
| **2. Création mission** | POST `/api/missions` | HTTP 201 | Mission créée avec ID |
| **3. Création collaborateur** | POST `/api/collaborateurs` | HTTP 201 | Collaborateur + compte utilisateur créés |
| **4. Assignation compétence** | POST `/api/collaborateur-competences` | HTTP 201 | Compétence assignée au collaborateur |
| **5. Création tâche** | POST `/api/taches` | HTTP 201 | Tâche créée et **automatiquement assignée** |
| **6. Vérification assignation** | GET `/api/taches/{id}` | HTTP 200 | Tâche contient `collaborateurAssigne` |
| **7. Mise à jour tâche** | PUT `/api/taches/{id}` | HTTP 200 | Statut et charge réelle mis à jour |
| **8. Récupération tâches collaborateur** | GET `/api/taches/collaborateur/email/{email}` | HTTP 200 | Liste des tâches du collaborateur |
| **9. Statistiques de charge** | GET `/api/collaborateurs/{id}/workload-stats` | HTTP 200 | Charge actuelle et pourcentage |
| **10. Suppression tâche** | DELETE `/api/taches/{id}` | HTTP 204 | Tâche supprimée |

#### **Données de test**
```json
{
  "titre": "Tâche de test intégration",
  "description": "Description de la tâche de test",
  "chargeEstimee": 15.0,
  "dateDebut": "2024-01-01",
  "dateFinPrevue": "2024-01-15",
  "statut": "planifiée",
  "priorite": 2
}
```

#### **Validations critiques**
- ✅ **Assignation automatique** : La tâche doit être assignée au collaborateur ayant la compétence requise
- ✅ **Création compte utilisateur** : Un compte utilisateur doit être créé avec le collaborateur
- ✅ **Calcul de charge** : Les statistiques de charge doivent être calculées correctement

---

### 3. **Test de Workflow Collaborateur** (`ApiIntegrationTest::testCollaborateurWorkflow`)

#### **Objectif**
Tester le cycle de vie complet d'un collaborateur : création → lecture → mise à jour → suppression.

#### **Étapes du test**

| **Étape** | **Action** | **Attente** | **Résultat Attendu** |
|-----------|------------|-------------|---------------------|
| **1. Création collaborateur** | POST `/api/collaborateurs` | HTTP 201 | Collaborateur + compte utilisateur créés |
| **2. Vérification création** | GET `/api/collaborateurs/{id}` | HTTP 200 | Données collaborateur correctes |
| **3. Mise à jour collaborateur** | PUT `/api/collaborateurs/{id}` | HTTP 200 | Prénom et rôle mis à jour |
| **4. Mise à jour disponibilité** | POST `/api/collaborateurs/{id}/update-availability` | HTTP 200 | Disponibilité mise à jour |
| **5. Suppression collaborateur** | DELETE `/api/collaborateurs/{id}` | HTTP 204 | Collaborateur supprimé |

#### **Données de test**
```json
{
  "prenom": "Jean",
  "nom": "Dupont", 
  "email": "jean.dupont.integration@example.com",
  "role": "Collaborateur",
  "disponible": true
}
```

#### **Validations critiques**
- ✅ **Création compte utilisateur** : `userAccount` dans la réponse
- ✅ **Email unique** : Pas de doublon d'email
- ✅ **Mise à jour partielle** : Seuls les champs fournis sont mis à jour

---

### 4. **Test d'Authentification et Autorisation** (`ApiIntegrationTest::testAuthenticationAndAuthorization`)

#### **Objectif**
Vérifier que le système d'authentification JWT fonctionne correctement.

#### **Cas de test**

| **Cas** | **Token** | **Attente** | **Résultat Attendu** |
|---------|-----------|-------------|---------------------|
| **Sans authentification** | Aucun | HTTP 401 | `{"message": "JWT Token not found"}` |
| **Token invalide** | `invalid_token` | HTTP 401 | `{"message": "Invalid JWT Token"}` |
| **Token expiré** | Token expiré | HTTP 401 | `{"message": "Expired JWT Token"}` |
| **Token valide** | Token JWT valide | HTTP 200 | Accès autorisé aux données |

#### **Endpoints testés**
- `GET /api/taches`
- `GET /api/collaborateurs`
- `GET /api/competences`

---

### 5. **Test de Gestion d'Erreurs** (`ApiIntegrationTest::testErrorHandling`)

#### **Objectif**
Vérifier que les erreurs sont gérées correctement avec des messages appropriés.

#### **Cas d'erreur testés**

| **Cas** | **Action** | **Attente** | **Résultat Attendu** |
|---------|------------|-------------|---------------------|
| **Données invalides** | POST `/api/taches` avec champs manquants | HTTP 422 | `{"message": "Champ obligatoire manquant"}` |
| **Ressource inexistante** | GET `/api/taches/99999` | HTTP 404 | `{"message": "Tâche non trouvée"}` |
| **Email déjà utilisé** | POST `/api/collaborateurs` avec email existant | HTTP 422 | `{"message": "Email déjà utilisé"}` |
| **Compétence inexistante** | POST `/api/taches` avec compétence invalide | HTTP 422 | `{"message": "Compétence non trouvée"}` |

---

## 🔐 Tests d'Accès Utilisateur

### 6. **Test des Permissions Admin** (`UserAccessTest::testAdminCanCreateUser`)

#### **Objectif**
Vérifier qu'un administrateur peut créer des utilisateurs.

#### **Cas de test**

| **Rôle** | **Action** | **Attente** | **Résultat Attendu** |
|----------|------------|-------------|---------------------|
| **Admin** | POST `/api/users` | HTTP 201 | Utilisateur créé avec succès |
| **User** | POST `/api/users` | HTTP 403 | `{"message": "Access denied"}` |

#### **Données de test**
```json
{
  "email": "new@test.local",
  "plainPassword": "Pass1234!"
}
```

---

## 📊 Métriques et KPIs

### **Taux de Réussite Global**
- **Objectif** : 100% des tests passent
- **Seuil d'alerte** : < 95%
- **Seuil critique** : < 90%

### **Temps de Réponse**
- **API Endpoints** : < 2 secondes
- **Tests d'intégration** : < 30 secondes
- **Tests complets** : < 2 minutes

### **Couverture de Test**
- **Endpoints API** : 100% (8/8)
- **Workflows métier** : 100% (3/3)
- **Gestion d'erreurs** : 100% (4/4)

---

## 🚨 Gestion des Échecs

### **Échecs Critiques**
1. **Serveur non démarré** → Démarrer avec `php -S localhost:8000 -t public/`
2. **Base de données non configurée** → Configurer `.env.local`
3. **Migrations non exécutées** → Exécuter `php bin/console doctrine:migrations:migrate`
4. **Fixtures non chargées** → Exécuter `php bin/console doctrine:fixtures:load`

### **Échecs de Performance**
1. **Temps de réponse > 10s** → Vérifier la base de données
2. **Mémoire insuffisante** → Augmenter `memory_limit` dans PHP
3. **Timeout de connexion** → Vérifier la configuration réseau

### **Échecs de Données**
1. **Données corrompues** → Réinitialiser la base de données
2. **Contraintes violées** → Vérifier les validations
3. **Relations cassées** → Vérifier les clés étrangères

---

## 🔄 Commandes de Test

### **Lancer tous les tests**
```bash
# Tests d'intégration complets
php bin/phpunit tests/Integration/

# Test de connectivité rapide
php test-integration.php

# Tests d'accès utilisateur
php bin/phpunit tests/UserAccessTest.php
```

### **Tests spécifiques**
```bash
# Test workflow tâches uniquement
php bin/phpunit --filter testCompleteTacheWorkflow

# Test workflow collaborateurs uniquement  
php bin/phpunit --filter testCollaborateurWorkflow

# Test authentification uniquement
php bin/phpunit --filter testAuthenticationAndAuthorization
```

### **Tests avec couverture**
```bash
# Avec rapport de couverture
php bin/phpunit --coverage-html coverage/
```

---

## 📈 Monitoring et Alertes

### **Alertes Automatiques**
- **Échec de test** → Notification immédiate
- **Performance dégradée** → Alerte si > 5s
- **Taux de réussite < 95%** → Alerte équipe

### **Rapports Réguliers**
- **Quotidien** : Résumé des tests d'intégration
- **Hebdomadaire** : Analyse des tendances de performance
- **Mensuel** : Rapport de couverture et qualité

---

## 🎯 Bonnes Pratiques

### **Avant chaque déploiement**
1. ✅ Lancer `php test-integration.php`
2. ✅ Vérifier que tous les tests passent
3. ✅ Contrôler les temps de réponse
4. ✅ Valider la couverture de test

### **En cas d'échec**
1. 🔍 Analyser les logs d'erreur
2. 🔧 Corriger le problème identifié
3. 🔄 Relancer les tests
4. 📝 Documenter la résolution

### **Maintenance**
1. 📅 Tests quotidiens automatiques
2. 🔄 Mise à jour des données de test
3. 📊 Analyse des métriques de performance
4. 🛠️ Optimisation continue

---

*Cette documentation est mise à jour à chaque modification des tests d'intégration.*
