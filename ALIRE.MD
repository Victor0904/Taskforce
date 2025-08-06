### 1. Authentification & Sécurité

* [X] **Login JWT** (`POST /api/login`) via LexikJWT (json_login)
* [X] **Firewall API** protégé par JWT (`^/api`)
* [X] **Rôles & contrôle d’accès** (`ROLE_USER`, `ROLE_ADMIN`, hiérarchie ROLE_ADMIN > ROLE_USER)
* [X] **Stockage du token** en `localStorage` et injection dans `Authorization: Bearer …`

### 2. Backend — Missions (Projets)

* [X] **Entité `Mission`**
  * Champs : `id`, `titre`, `description`, `priorite`, `dateDebut`, `dateFinPrevue`, `statut`, `responsable`
  * Serializer Groups (`mission:read`, `mission:write`, `tache:read`)
* [X] **MissionController**
  * `GET /api/missions` → liste des missions
  * `GET /api/missions/{id}` → détail d’une mission
  * `POST /api/missions` → création (avec validation du responsable)
  * `PUT /api/missions/{id}` → mise à jour
  * `DELETE /api/missions/{id}` → suppression
* [X] **Tests manuels** via Postman / front pour CRUD missions

### 3. Backend — Tâches

* [X] **Entité `Tache`**
  * Champs : `id`, `titre`, `description`, `chargeEstimee`, `chargeReelle`, `dateDebut`, `dateFinPrevue`, `statut`, `priorite`, `mission`, `collaborateur`, `competenceRequise`
  * Serializer Groups (`tache:read`, `tache:write`)
* [X] **TacheController**
  * `GET /api/taches` → liste complète
  * `GET /api/taches/{id}` → détail
  * `POST /api/taches` → création
  * `PUT /api/taches/{id}` → mise à jour
  * `DELETE /api/taches/{id}` → suppression
* [ ] **Route `GET /api/taches/projet/{id}`** → 404 pour l’instant (à corriger le préfixe de la route)

### 4. Backend — Autres entités & relations

* [X] **Collaborateur** : ajout de `#[Groups(['tache:read'])]` sur `nom`, `prenom`, etc.
* [X] **Competence** : ajout de `#[Groups(['tache:read'])]` sur `nom`
* [X] **CollaborateurCompetence** et **Alerte** : existants, non modifiés

### 5. CORS

* [ ] **Configuration CORS** pour autoriser `http://localhost:5173` (à vérifier / ajouter si nécessaire)

### 6. Frontend — Routes Vue Router

* [X] Définies dans `router/index.js` :
  * `/` (Dashboard)
  * `/login`
  * `/admin` (ROLE_ADMIN)
  * `/projets` (ROLE_ADMIN)
  * `/projets/:id/taches` → composant `Taches.vue`
  * etc.

### 7. Frontend — Composant **Projet.vue**

* [X] **Affichage** de la liste des projets (titre, description, responsable)
* [X] **Modal** Ajout / Modification de projet
* [X] **CRUD** projets via axios (`GET`, `POST`, `PUT`, `DELETE`)
* [X] **Chargement** des collaborateurs pour le `select responsable`
* [X] **Affichage** brut des tâches associées (si `p.taches` chargé côté front)

### 8. Frontend — Composant **Taches.vue**

* [X] **Skeleton** de la vue tâches par projet (titre, statut, priorité, collaborateur)
* [ ] **CRUD des tâches** (ajout / édition / suppression) non finalisé faute de retour de l’API
* [ ] **Chargement effectif** des tâches (404 sur l’endpoint back)

---

**Prochaines priorités**

1. Corriger la route Symfony `GET /api/taches/projet/{id}` pour qu’elle réponde bien.
2. Tester l’appel depuis Vue (`fetchProjets` + `fetchTaches`).
3. Implémenter les modals et actions CRUD (POST/PUT/DELETE) dans  **Taches.vue** .
4. Ajuster la configuration CORS si besoin.
