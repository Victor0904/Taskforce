<template>
  <div class="container">
    <div class="admin-header">
      <h2 class="admin-title">Gestion des Projets</h2>
      <div class="admin-actions">
        <button v-if="peutCreerProjet" @click="ouvrirModalAjout" class="btn btn-primary">
          <i class="icon">➕</i>
          Ajouter un projet
        </button>
        <button @click="refreshData(true)" class="btn btn-secondary">
          <i class="icon">🔄</i>
          Actualiser
        </button>
      </div>
    </div>

          <div class="projets-grid">
        <div v-for="projet in projets" :key="projet.id" class="projet-card">
          <div class="projet-actions-top">
            <button v-if="peutCreerProjet" @click="ouvrirModalModification(projet)" class="btn btn-info">
              ✏️ Modifier
            </button>
            <button v-if="peutCreerProjet" @click="supprimerProjet(projet.id)" class="btn btn-error">
              🗑️ Supprimer
            </button>
            <button @click="ouvrirGestionTaches(projet)" class="btn btn-primary">
              🧩 Gérer les tâches
            </button>
          </div>
          
          <div class="projet-header">
            <div class="projet-info">
              <h3 class="projet-title">{{ projet.titre }}</h3>
              <p class="projet-description">{{ projet.description }}</p>
              <div class="projet-meta">
                <span class="projet-responsable">
                  <strong>Responsable :</strong> {{ projet.responsable?.prenom }} {{ projet.responsable?.nom }}
                </span>
              </div>
            </div>
          </div>

        <div v-if="projet.taches?.length" class="taches-section">
          <h4 class="section-title">Tâches du projet</h4>
          <div class="taches-grid">
            <div v-for="tache in projet.taches" :key="tache.id" class="tache-card">
              <div class="tache-header">
                <h5 class="tache-title">{{ tache.titre }}</h5>
                <span class="badge" :class="'status-' + (tache.statut || '')">
                  {{ humanizeStatut(tache.statut) }}
                </span>
              </div>
              <div class="tache-meta">
                <span class="tache-priority">Priorité: <strong>{{ tache.priorite }}</strong></span>
                <span v-if="tache.competenceRequise" class="tache-competence">
                  Compétence: <strong>{{ tache.competenceRequise.nom }}</strong>
                </span>
              </div>
              <div class="tache-dates">
                <span>Début: {{ formatDate(tache.dateDebut) }}</span>
                <span>Fin: {{ formatDate(tache.dateFin) }}</span>
                <span>Charge: <strong>{{ tache.chargeEstimee }}h</strong></span>
              </div>
              <div class="tache-assign" v-if="tache.collaborateur">
                <span class="assignee">
                  Assigné à: <strong>{{ tache.collaborateur.prenom }} {{ tache.collaborateur.nom }}</strong>
                </span>
              </div>
              <div class="tache-assign task-unassigned" v-else>
                <span class="warning-text">⚠️ Non assignée</span>
              </div>
            </div>
          </div>
        </div>
        <p v-else class="no-taches">Aucune tâche pour ce projet.</p>
      </div>
    </div>

    <p v-if="projets.length === 0" class="no-projets">Aucun projet trouvé.</p>

    <!-- Modal d'ajout/modification de projet -->
    <div v-if="showModal" class="modal-overlay" @click="fermerModal">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h3>{{ modeEdition ? 'Modifier le projet' : 'Ajouter un projet' }}</h3>
          <button @click="fermerModal" class="btn-close">&times;</button>
        </div>
        <form @submit.prevent="sauvegarderProjet">
          <div class="form-group">
            <label class="form-label">Titre :</label>
            <input v-model="formData.titre" type="text" class="form-input" required />
          </div>
          <div class="form-group">
            <label class="form-label">Description :</label>
            <textarea v-model="formData.description" class="form-input" required></textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Date de début :</label>
            <input v-model="formData.date_debut" type="date" class="form-input" required />
          </div>
          <div class="form-group">
            <label class="form-label">Date de fin prévue :</label>
            <input v-model="formData.date_fin_prevue" type="date" class="form-input" required />
          </div>
          <div class="form-group">
            <label class="form-label">Responsable  :</label>
            <select v-model="formData.responsable" class="form-input" required>
              <option value="">Sélectionner un responsable</option>
              <option v-for="collab in collaborateurs" :key="collab.id" :value="collab.id">
                {{ collab.prenom }} {{ collab.nom }} ({{ formatRole(collab.role) }})
              </option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Priorité :</label>
            <select v-model="formData.priorite" class="form-input" required>
              <option value="1">1 - Très basse</option>
              <option value="2">2 - Basse</option>
              <option value="3">3 - Moyenne</option>
              <option value="4">4 - Haute</option>
              <option value="5">5 - Très haute</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Statut :</label>
            <select v-model="formData.statut" class="form-input" required>
              <option value="en_attente">En attente</option>
              <option value="en_cours">En cours</option>
              <option value="terminee">Terminée</option>
              <option value="annulee">Annulée</option>
            </select>
          </div>
          <div class="modal-actions">
            <button type="button" @click="fermerModal" class="btn btn-secondary">Annuler</button>
            <button type="submit" class="btn btn-primary">{{ modeEdition ? 'Modifier' : 'Ajouter' }}</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal de gestion des tâches -->
    <div v-if="showTachesModal" class="modal-overlay" @click="fermerGestionTaches">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h3>Gestion des tâches - {{ projetSelectionne?.titre }}</h3>
          <button @click="fermerGestionTaches" class="btn-close">&times;</button>
        </div>
        
        <div class="admin-actions mb-lg">
          <button @click="ouvrirFormTache()" class="btn btn-success">
            <i class="icon">➕</i>
            Ajouter une tâche
          </button>
        </div>

        <div v-if="tachesProjet.length" class="taches-grid">
          <div v-for="tache in tachesProjet" :key="tache.id" class="tache-card">
            <div class="tache-header">
              <h5 class="tache-title">{{ tache.titre }}</h5>
              <div class="tache-actions">
                <button @click="ouvrirFormTache(tache)" class="btn btn-info btn-small">✏️</button>
                <button @click="supprimerTache(tache.id)" class="btn btn-error btn-small">🗑️</button>
              </div>
            </div>
            <div class="tache-meta">
              <span class="badge" :class="'status-' + (tache.statut || '')">
                {{ humanizeStatut(tache.statut) }}
              </span>
              <span class="tache-priority">Priorité: <strong>{{ tache.priorite }}</strong></span>
              <span v-if="tache.competenceRequise" class="tache-competence">
                Compétence: <strong>{{ tache.competenceRequise.nom }}</strong>
              </span>
            </div>
            <div class="tache-dates">
              <span>Début: {{ formatDate(tache.dateDebut) }}</span>
              <span>Fin: {{ formatDate(tache.dateFin) }}</span>
              <span>Charge: <strong>{{ tache.chargeEstimee }}h</strong></span>
            </div>
            <div class="tache-assign" v-if="tache.collaborateur">
              <span class="assignee">
                Assigné à: <strong>{{ tache.collaborateur.prenom }} {{ tache.collaborateur.nom }}</strong>
              </span>
            </div>
            <div class="tache-assign task-unassigned" v-else>
              <span class="warning-text">⚠️ Non assignée</span>
            </div>
          </div>
        </div>
        <p v-else class="no-taches">Aucune tâche pour ce projet.</p>

        <!-- Modal d'ajout/modification de tâche -->
        <div v-if="showFormTache" class="modal-overlay" @click="fermerFormTache">
          <div class="modal" @click.stop>
            <div class="modal-header">
              <h3>{{ tacheEnEdition ? 'Modifier la tâche' : 'Ajouter une tâche' }}</h3>
              <button @click="fermerFormTache" class="btn-close">&times;</button>
            </div>
            <form @submit.prevent="sauvegarderTache">
              <div class="form-group">
                <label class="form-label">Titre :</label>
                <input v-model="formTache.titre" type="text" class="form-input" required />
              </div>
              <div class="form-group">
                <label class="form-label">Description :</label>
                <textarea v-model="formTache.description" class="form-input"></textarea>
              </div>
              <div class="form-group">
                <label class="form-label">Priorité :</label>
                <select v-model="formTache.priorite" class="form-input" required>
                  <option value="1">1 - Très basse</option>
                  <option value="2">2 - Basse</option>
                  <option value="3">3 - Moyenne</option>
                  <option value="4">4 - Haute</option>
                  <option value="5">5 - Très haute</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Statut :</label>
                <select v-model="formTache.statut" class="form-input" required>
                  <option value="en_attente">En attente</option>
                  <option value="en_cours">En cours</option>
                  <option value="terminee">Terminée</option>
                  <option value="annulee">Annulée</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Date de début :</label>
                <input v-model="formTache.date_debut" type="date" class="form-input" required />
              </div>
              <div class="form-group">
                <label class="form-label">Date de fin :</label>
                <input v-model="formTache.date_fin" type="date" class="form-input" required />
              </div>
              <div class="form-group">
                <label class="form-label">Charge estimée (heures) :</label>
                <input v-model="formTache.charge_estimee" type="number" class="form-input" min="0" step="0.5" required />
              </div>
              <div class="form-group">
                <label class="form-label">Compétence requise : *</label>
                <select v-model="formTache.competence" class="form-input" required>
                  <option value="">Sélectionnez une compétence</option>
                  <option v-for="comp in competences" :key="comp.id" :value="comp.id">
                    {{ comp.nom }}
                  </option>
                </select>
              </div>
              <div class="modal-actions">
                <button type="button" @click="fermerFormTache" class="btn btn-secondary">Annuler</button>
                <button type="submit" class="btn btn-primary">{{ tacheEnEdition ? 'Modifier' : 'Ajouter' }}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import axios from 'axios'
import { jwtDecode } from 'jwt-decode'

/* ======= State ======= */
const projets = ref([])
const collaborateurs = ref([])
const competences = ref([])
const showModal = ref(false)
const modeEdition = ref(false)
const projetEnEdition = ref(null)
const projetsFiltres = ref(false)

const formData = ref({
  titre: '',
  description: '',
  date_debut: '',
  date_fin_prevue: '',
  responsable: '',
  priorite: 1,
  statut: 'en_attente'
})

const showTachesModal = ref(false)
const projetSelectionne = ref(null)
const tachesProjet = ref([])
const tacheEnEdition = ref(null)
const formTache = ref({
  titre: '',
  description: '',
  priorite: 1,
  statut: 'en_attente',
  date_debut: '',
  date_fin: '',
  charge_estimee: 0,
  competence: ''
})

const showFormTache = ref(false)

// ✅ Système de cache persistant avec localStorage
const CACHE_KEY = 'projet_cache'
const CACHE_TTL = 5 * 60 * 1000 // 5 minutes en millisecondes

// Fonction pour charger le cache depuis localStorage
const loadCache = () => {
  try {
    const cached = localStorage.getItem(CACHE_KEY)
    return cached ? JSON.parse(cached) : null
  } catch (error) {
    return null
  }
}

// Fonction pour sauvegarder le cache dans localStorage
const saveCache = (data) => {
  try {
    localStorage.setItem(CACHE_KEY, JSON.stringify(data))
  } catch (error) {
    console.warn('Impossible de sauvegarder le cache:', error)
  }
}

// Fonction pour vider le cache
const clearCache = () => {
  localStorage.removeItem(CACHE_KEY)
}

// Fonction pour vérifier si le cache est valide
const isCacheValid = (cacheData) => {
  if (!cacheData || !cacheData.timestamp) return false
  const now = Date.now()
  return (now - cacheData.timestamp) < CACHE_TTL
}

/* ======= Computed ======= */
const peutCreerProjet = computed(() => {
  const token = localStorage.getItem('token')
  if (!token) return false
  
  try {
    const decoded = jwtDecode(token)
    return decoded.roles.includes('ROLE_ADMIN') || decoded.roles.includes('ROLE_CHEF_PROJET')
  } catch (e) {
    return false
  }
})

/* ======= Methods ======= */
const authHeaders = () => {
  const token = localStorage.getItem('token')
  return { Authorization: `Bearer ${token}` }
}

const fetchProjets = async (forceRefresh = false) => {
  try {
    // ✅ Vérifier le cache d'abord
    if (!forceRefresh) {
      const cachedData = loadCache()
      if (cachedData && isCacheValid(cachedData) && cachedData.projets) {
        projets.value = cachedData.projets
        projetsFiltres.value = cachedData.projetsFiltres || false
        return
      }
    }
    const res = await axios.get('http://127.0.0.1:8000/api/missions', { headers: authHeaders() })
    const apiList = Array.isArray(res.data.data) ? res.data.data : res.data
    
    projetsFiltres.value = res.data.filtered || false
    
    const projetsAvecTaches = await Promise.all(apiList.map(async (p) => {
      try {
        const tRes = await axios.get(`http://127.0.0.1:8000/api/taches/projet/${p.id}`, { headers: authHeaders() })
        const taches = Array.isArray(tRes.data.data) ? tRes.data.data : tRes.data
        return { ...p, taches }
      } catch (error) {
        return { ...p, taches: [] }
      }
    }))
    
    projets.value = projetsAvecTaches
    
    // ✅ Sauvegarder dans le cache persistant
    const cacheData = {
      projets: projets.value,
      projetsFiltres: projetsFiltres.value,
      collaborateurs: collaborateurs.value,
      competences: competences.value,
      timestamp: Date.now()
    }
    saveCache(cacheData)
    
  } catch (e) {
    // Erreur silencieuse
  }
}

const fetchCollaborateurs = async (forceRefresh = false) => {
  try {
    // ✅ Vérifier le cache d'abord
    if (!forceRefresh) {
      const cachedData = loadCache()
      if (cachedData && isCacheValid(cachedData) && cachedData.collaborateurs) {
        collaborateurs.value = cachedData.collaborateurs
        return
      }
    }
    const res = await axios.get('http://127.0.0.1:8000/api/collaborateurs', { headers: authHeaders() })
    const allCollaborateurs = res.data.data || res.data
    
    // Filtrer uniquement les managers et chefs de projet
    collaborateurs.value = allCollaborateurs.filter(collab => {
      // Vérifier si le collaborateur a un rôle de manager ou chef de projet
      return collab.role === 'Manager' || collab.role === 'Chef de projet'
    })
    
    // ✅ Sauvegarder dans le cache persistant
    const cacheData = {
      projets: projets.value,
      projetsFiltres: projetsFiltres.value,
      collaborateurs: collaborateurs.value,
      competences: competences.value,
      timestamp: Date.now()
    }
    saveCache(cacheData)
    
  } catch (e) {
    // Gestion silencieuse de l'erreur
  }
}

const fetchCompetences = async (forceRefresh = false) => {
  try {
    // ✅ Vérifier le cache d'abord
    if (!forceRefresh) {
      const cachedData = loadCache()
      if (cachedData && isCacheValid(cachedData) && cachedData.competences) {
        competences.value = cachedData.competences
        return
      }
    }
    const res = await axios.get('http://127.0.0.1:8000/api/competences', { headers: authHeaders() })
    competences.value = res.data.data || res.data
    
    // ✅ Sauvegarder dans le cache persistant
    const cacheData = {
      projets: projets.value,
      projetsFiltres: projetsFiltres.value,
      collaborateurs: collaborateurs.value,
      competences: competences.value,
      timestamp: Date.now()
    }
    saveCache(cacheData)
    
  } catch (e) {
    // Gestion silencieuse de l'erreur
  }
}

const onKeyDown = (e) => { 
  if (e.key === 'Escape') { 
    if (showTachesModal.value) fermerGestionTaches()
    if (showModal.value) fermerModal() 
  } 
}

// ✅ Fonction pour rafraîchir toutes les données
const refreshData = (forceRefresh = false) => {
  fetchProjets(forceRefresh)
  fetchCollaborateurs(forceRefresh)
  fetchCompetences(forceRefresh)
}

onMounted(() => { 
  refreshData() // Utiliser la fonction refreshData au lieu d'appels séparés
  window.addEventListener('keydown', onKeyDown) 
})

onBeforeUnmount(() => { 
  window.removeEventListener('keydown', onKeyDown) 
})

/* ======= CRUD Projets ======= */
const ouvrirModalAjout = () => { 
  modeEdition.value = false
  projetEnEdition.value = null
  resetFormData()
  showModal.value = true 
}

const ouvrirModalModification = (projet) => {
  modeEdition.value = true
  projetEnEdition.value = projet
  formData.value = {
    titre: projet.titre,
    description: projet.description,
    date_debut: projet.dateDebut?.slice(0,10),
    date_fin_prevue: projet.dateFinPrevue?.slice(0,10),
    responsable: projet.responsable?.id || '',
    priorite: projet.priorite,
    statut: projet.statut
  }
  showModal.value = true
}

const fermerModal = () => { 
  showModal.value = false
  resetFormData() 
}

const resetFormData = () => { 
  formData.value = { 
    titre: '', 
    description: '', 
    date_debut: '', 
    date_fin_prevue: '', 
    responsable: '', 
    priorite: 1, 
    statut: 'en_attente' 
  } 
}

const sauvegarderProjet = async () => {
  try {
    const payload = { 
      titre: formData.value.titre, 
      description: formData.value.description, 
      dateDebut: formData.value.date_debut, 
      dateFinPrevue: formData.value.date_fin_prevue, 
      responsable: formData.value.responsable, 
      priorite: formData.value.priorite, 
      statut: formData.value.statut 
    }
    const config = { headers: { ...authHeaders(), 'Content-Type':'application/json' }}
    
    if (modeEdition.value) {
      await axios.put(`http://127.0.0.1:8000/api/missions/${projetEnEdition.value.id}`, payload, config)

    } else {
      await axios.post('http://127.0.0.1:8000/api/missions', payload, config)

    }
    
    fermerModal()
    clearCache() // Vider le cache car les données ont changé
    await fetchProjets(true) // Forcer le rechargement
  } catch (error) { 
    // Erreur silencieuse
  }
}

const supprimerProjet = async (id) => { 
  if (!confirm('Supprimer ce projet ?')) return
  
  try { 
    await axios.delete(`http://127.0.0.1:8000/api/missions/${id}`, { headers: authHeaders() })
    
    clearCache() // Vider le cache car les données ont changé
    await fetchProjets(true) // Forcer le rechargement 
  } catch (error) { 
    // Erreur silencieuse
  } 
}

/* ======= Gestion Tâches ======= */
const ouvrirGestionTaches = async (projet) => { 
  projetSelectionne.value = projet
  await fetchTachesProjet()
  fermerFormTache()
  showTachesModal.value = true 
}

const fermerGestionTaches = () => { 
  showTachesModal.value = false
  clearCache() // Vider le cache car les tâches ont pu changer
  fetchProjets(true) // Forcer le rechargement
  projetSelectionne.value = null
  tachesProjet.value = []
  fermerFormTache() 
}

const fetchTachesProjet = async () => {
  if (!projetSelectionne.value) return
  
  try { 
    const res = await axios.get(`http://127.0.0.1:8000/api/taches/projet/${projetSelectionne.value.id}`, { headers: authHeaders() })
    tachesProjet.value = Array.isArray(res.data.data) ? res.data.data : res.data 
  } catch (e) { 
    // Erreur silencieuse
  }
}

const ouvrirFormTache = (tache = null) => {
  tacheEnEdition.value = tache
  if (tache) {
    formTache.value = {
      titre: tache.titre,
      description: tache.description || '',
      priorite: tache.priorite ?? 1,
      statut: tache.statut || 'en_attente',
      date_debut: tache.dateDebut?.slice(0,10) || '',
      date_fin: tache.dateFin?.slice(0,10) || '',
      charge_estimee: tache.chargeEstimee || 0,
      competence: tache.competenceRequise?.id || ''
    }
  } else {
    formTache.value = {
      titre: '',
      description: '',
      priorite: 1,
      statut: 'en_attente',
      date_debut: '',
      date_fin: '',
      charge_estimee: 0,
      competence: ''
    }
  }
  showFormTache.value = true
}

const fermerFormTache = () => { 
  showFormTache.value = false
  tacheEnEdition.value = null 
}

const sauvegarderTache = async () => {
  try {
    if (!formTache.value.titre || !formTache.value.description || !formTache.value.date_debut || 
        !formTache.value.date_fin || !formTache.value.charge_estimee || !formTache.value.competence) {
      return
    }

    const payload = {
      titre: formTache.value.titre,
      description: formTache.value.description,
      priorite: parseInt(formTache.value.priorite),
      statut: formTache.value.statut,
      dateDebut: formTache.value.date_debut,
      dateFinPrevue: formTache.value.date_fin,
      chargeEstimee: parseFloat(formTache.value.charge_estimee),
      mission: projetSelectionne.value.id,
      competenceRequise: formTache.value.competence
    }
    
    const config = { headers: { ...authHeaders(), 'Content-Type':'application/json' }}
    
    if (tacheEnEdition.value) {
      await axios.put(`http://127.0.0.1:8000/api/taches/${tacheEnEdition.value.id}`, payload, config)

    } else {
      await axios.post('http://127.0.0.1:8000/api/taches', payload, config)

    }
    
    fermerFormTache()
    await fetchTachesProjet()
  } catch (error) {
    if (error.response?.status === 422) {
      const errorData = error.response.data
      let errorMessage = 'Erreur de validation :\n'
      
      if (errorData.message) {
        errorMessage += errorData.message + '\n'
      }
      if (errorData.details) {
        errorMessage += '\nDétails : ' + errorData.details
      }
      if (errorData.suggestion) {
        errorMessage += '\n\nSuggestion : ' + errorData.suggestion
      }
      

    } else {

    }
  }
}

const supprimerTache = async (id) => {
  if (!confirm('Supprimer cette tâche ?')) return
  
  try {
    await axios.delete(`http://127.0.0.1:8000/api/taches/${id}`, { headers: authHeaders() })
    
    await fetchTachesProjet()
  } catch (error) {
    // Erreur silencieuse
  }
}

/* ======= Utilitaires ======= */
const humanizeStatut = (statut) => {
  const statuts = {
    'en_attente': 'En attente',
    'en_cours': 'En cours',
    'terminee': 'Terminée',
    'annulee': 'Annulée'
  }
  return statuts[statut] || statut
}

const formatDate = (date) => {
  if (!date) return 'Non définie'
  return new Date(date).toLocaleDateString('fr-FR')
}

const formatRole = (role) => {
  if (!role) return 'Collaborateur'
  const roleMap = {
    'ROLE_MANAGER': 'Manager',
    'ROLE_CHEF_PROJET': 'Chef de Projet',
    'ROLE_ADMIN': 'Administrateur',
    'ROLE_USER': 'Collaborateur'
  }
  return roleMap[role] || role
}
</script>

<style scoped>
/* Container principal */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: var(--spacing-lg);
}

/* Header de la page */
.admin-header {
  text-align: center;
  margin-bottom: var(--spacing-2xl);
}

.admin-title {
  font-size: 2rem;
  color: var(--secondary-900);
  margin-bottom: var(--spacing-sm);
}

.admin-actions {
  display: flex;
  justify-content: center;
  gap: var(--spacing-md);
}

/* Grille des projets */
.projets-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: var(--spacing-xl);
  margin-bottom: var(--spacing-xl);
}

.projet-card {
  background: white;
  border: 1px solid var(--secondary-200);
  border-radius: var(--border-radius-xl);
  padding: var(--spacing-xl);
  box-shadow: var(--shadow-md);
  transition: all 0.2s ease;
}

.projet-card:hover {
  box-shadow: var(--shadow-lg);
  transform: translateY(-2px);
}

/* Boutons d'action en haut */
.projet-actions-top {
  display: flex;
  gap: var(--spacing-md);
  margin-bottom: var(--spacing-lg);
  padding-bottom: var(--spacing-lg);
  border-bottom: 1px solid var(--secondary-200);
  flex-wrap: wrap;
}

.projet-header {
  margin-bottom: var(--spacing-lg);
}

.projet-info {
  flex: 1;
}

.projet-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--secondary-900);
  margin: 0 0 var(--spacing-sm) 0;
}

.projet-description {
  color: var(--secondary-600);
  margin-bottom: var(--spacing-md);
  line-height: 1.5;
}

.projet-meta {
  color: var(--secondary-500);
  font-size: 0.9rem;
}

/* Section des tâches */
.taches-section {
  margin-top: var(--spacing-lg);
}

.section-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--secondary-800);
  margin-bottom: var(--spacing-md);
  border-bottom: 2px solid var(--secondary-200);
  padding-bottom: var(--spacing-sm);
}

.taches-grid {
  display: grid;
  gap: var(--spacing-md);
}

.tache-card {
  background: var(--secondary-50);
  border: 1px solid var(--secondary-200);
  border-radius: var(--border-radius-lg);
  padding: var(--spacing-lg);
  transition: all 0.2s ease;
}

.tache-card:hover {
  background: white;
  box-shadow: var(--shadow-sm);
}

.tache-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--spacing-md);
}

.tache-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: var(--secondary-900);
  margin: 0;
}

.tache-actions {
  display: flex;
  gap: var(--spacing-sm);
}

.tache-meta {
  display: flex;
  flex-wrap: wrap;
  gap: var(--spacing-md);
  margin-bottom: var(--spacing-sm);
}

.tache-dates {
  display: flex;
  flex-wrap: wrap;
  gap: var(--spacing-md);
  color: var(--secondary-600);
  font-size: 0.9rem;
  margin-bottom: var(--spacing-sm);
}

.tache-assign {
  margin-top: var(--spacing-sm);
  color: var(--secondary-700);
  font-size: 0.9rem;
}

.assignee {
  color: var(--success-700);
}

.task-unassigned {
  display: flex;
  align-items: center;
  gap: var(--spacing-sm);
  padding: var(--spacing-sm);
  background-color: var(--warning-100);
  border: 1px solid var(--warning-300);
  border-radius: var(--border-radius-md);
}

.warning-text {
  color: var(--warning-700);
  font-weight: 500;
}

/* Badges et statuts */
.badge {
  display: inline-block;
  padding: var(--spacing-xs) var(--spacing-sm);
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.status-en_attente {
  background: var(--warning-100);
  color: var(--warning-700);
}

.status-en_cours {
  background: var(--info-100);
  color: var(--info-700);
}

.status-terminee {
  background: var(--success-100);
  color: var(--success-700);
}

.status-annulee {
  background: var(--error-100);
  color: var(--error-700);
}

/* Boutons */
.btn {
  display: inline-flex;
  align-items: center;
  gap: var(--spacing-sm);
  padding: var(--spacing-sm) var(--spacing-lg);
  border: none;
  border-radius: var(--border-radius-md);
  font-size: 0.875rem;
  font-weight: 500;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.2s ease;
  line-height: 1;
}

.btn:hover {
  transform: translateY(-1px);
  box-shadow: var(--shadow-md);
}

.btn-primary {
  background: var(--primary-600);
  color: white;
}

.btn-primary:hover {
  background: var(--primary-700);
}

.btn-secondary {
  background: var(--secondary-600);
  color: white;
}

.btn-secondary:hover {
  background: var(--secondary-700);
}

.btn-success {
  background: var(--success-600);
  color: white;
}

.btn-success:hover {
  background: var(--success-700);
}

.btn-info {
  background: var(--info-600);
  color: white;
}

.btn-info:hover {
  background: var(--info-700);
}

.btn-error {
  background: var(--error-600);
  color: white;
}

.btn-error:hover {
  background: var(--error-700);
}

.btn-small {
  padding: var(--spacing-xs) var(--spacing-sm);
  font-size: 0.8rem;
}

.btn-close {
  background: transparent;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: var(--secondary-500);
  padding: 0;
}

.btn-close:hover {
  color: var(--secondary-700);
}

/* Modales */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: var(--spacing-md);
}

.modal {
  background: white;
  border-radius: var(--border-radius-xl);
  padding: var(--spacing-xl);
  width: 100%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: var(--shadow-xl);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: var(--spacing-lg);
  padding-bottom: var(--spacing-md);
  border-bottom: 1px solid var(--secondary-200);
}

.modal-header h3 {
  margin: 0;
  color: var(--secondary-900);
  font-size: 1.25rem;
}

/* Formulaires */
.form-group {
  margin-bottom: var(--spacing-lg);
}

.form-label {
  display: block;
  margin-bottom: var(--spacing-sm);
  color: var(--secondary-700);
  font-weight: 500;
}

.form-input {
  width: 100%;
  padding: var(--spacing-sm);
  border: 1px solid var(--secondary-300);
  border-radius: var(--border-radius-md);
  font-size: 0.875rem;
  transition: border-color 0.2s ease;
}

.form-input:focus {
  outline: none;
  border-color: var(--primary-500);
  box-shadow: 0 0 0 3px var(--primary-100);
}

.form-input:disabled {
  background: var(--secondary-100);
  color: var(--secondary-500);
  cursor: not-allowed;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: var(--spacing-md);
  margin-top: var(--spacing-xl);
  padding-top: var(--spacing-lg);
  border-top: 1px solid var(--secondary-200);
}

/* Messages et états */
.no-projets, .no-taches {
  text-align: center;
  color: var(--secondary-500);
  font-style: italic;
  margin: var(--spacing-2xl) 0;
  padding: var(--spacing-xl);
  background: var(--secondary-50);
  border-radius: var(--border-radius-lg);
}

.mb-lg {
  margin-bottom: var(--spacing-xl);
}

/* Icônes */
.icon {
  font-size: 1.1rem;
}

/* Responsive */
@media (max-width: 768px) {
  .container {
    padding: var(--spacing-md);
  }
  
  .projets-grid {
    grid-template-columns: 1fr;
    gap: var(--spacing-lg);
  }
  
  .projet-actions-top {
    justify-content: center;
  }
  
  .projet-header {
    text-align: center;
  }
  
  .modal {
    margin: var(--spacing-md);
    padding: var(--spacing-lg);
  }
  
  .tache-meta, .tache-dates {
    flex-direction: column;
    gap: var(--spacing-sm);
  }
}
</style>
