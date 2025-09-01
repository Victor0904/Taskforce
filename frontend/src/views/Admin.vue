'ai '<template>
  <div class="container">
    <div class="admin-header">
      <h2 class="admin-title">Administration</h2>
      <div class="admin-actions">
        <button @click="refreshData(true)" class="btn btn-primary">
          <i class="icon">🔄</i>
          Actualiser
        </button>
      </div>
    </div>

    <div class="tabs">
      <button 
        @click="activeTab = 'collaborateurs'" 
        class="tab" 
        :class="{ active: activeTab === 'collaborateurs' }"
      >
        👥 Collaborateurs
      </button>
      <button 
        @click="activeTab = 'competences'" 
        class="tab" 
        :class="{ active: activeTab === 'competences' }"
      >
        🎯 Compétences
      </button>

      <button 
        @click="activeTab = 'gestion'" 
        class="tab" 
        :class="{ active: activeTab === 'gestion' }"
      >
        🎛️ Gestion
      </button>
    </div>

    <!-- Onglet Collaborateurs -->
    <div v-if="activeTab === 'collaborateurs'" class="tab-content">
      <div class="admin-actions mb-lg">
        <button @click="ouvrirFormCollaborateur(null)" class="btn btn-success">
          <i class="icon">➕</i>
          Ajouter un collaborateur
        </button>
      </div>

      <div class="collaborateurs-grid">
        <div v-for="collab in collaborateurs" :key="collab.id" class="collaborateur-card">
          <div class="collaborateur-header">
            <div class="collaborateur-info">
              <h3>{{ collab.prenom }} {{ collab.nom }}</h3>
              <span class="collaborateur-role">{{ collab.role }}</span>
            </div>
            <div class="flex gap-sm">
              <button @click="modifierCollaborateur(collab)" class="btn btn-info btn-small">
                ✏️
              </button>
              <button @click="supprimerCollaborateur(collab.id)" class="btn btn-error btn-small">
                🗑️
              </button>
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Email:</label>
            <input v-model="collab.email" class="form-input" disabled />
          </div>

          <div class="form-group">
            <label class="form-label">Disponible:</label>
            <input type="checkbox" v-model="collab.disponible" @change="sauvegarderCollaborateur(collab)" />
          </div>

          <div class="form-group">
            <label class="form-label">Charge:</label>
            <span class="text-lg">{{ getChargeInfo(collab).hours }}h / 60h ({{ getChargeInfo(collab).percent }}%)</span>
            <div class="progress">
              <div
                class="progress-bar"
                :class="getProgressClass(getChargeInfo(collab).percent)"
                :style="{ width: getChargeInfo(collab).percent + '%' }"
              ></div>
            </div>
            
            <!-- Indicateur de surcharge -->
            <div v-if="getChargeInfo(collab).percent > 60" class="alert" :class="getChargeInfo(collab).percent > 80 ? 'alert-error' : 'alert-warning'">
              <span v-if="getChargeInfo(collab).percent > 80">
                🚨 Surcharge critique ({{ getChargeInfo(collab).percent }}%)
              </span>
              <span v-else>
                ⚠️ Surcharge élevée ({{ getChargeInfo(collab).percent }}%)
              </span>
            </div>
          </div>

          <!-- Compétences du collaborateur -->
          <div class="form-group">
            <label class="form-label">Compétences:</label>
            <div class="flex flex-wrap gap-sm">
              <span 
                v-for="comp in collab.competences" 
                :key="comp.id" 
                class="badge badge-info"
              >
                {{ comp.competence.nom }} ({{ comp.niveau }}/10)
                <button @click="supprimerCompetenceCollaborateur(collab.id, comp.id)" class="btn btn-error btn-small">
                  ×
                </button>
              </span>
            </div>
            <button @click="ajouterCompetenceCollaborateur(collab)" class="btn btn-info btn-small">
              ➕ Ajouter compétence
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Onglet Compétences -->
    <div v-if="activeTab === 'competences'" class="tab-content">
      <div class="admin-actions mb-lg">
        <button @click="ouvrirFormCompetence(null)" class="btn btn-success">
          <i class="icon">➕</i>
          Ajouter une compétence
        </button>
      </div>

      <div class="grid grid-cols-3 gap-lg">
        <div v-for="comp in competences" :key="comp.id" class="card">
          <div class="card-header">
            <h3 class="card-title">{{ comp.nom }}</h3>
          </div>
          <div class="card-body">
            <p>{{ comp.description }}</p>
          </div>
          <div class="flex gap-sm">
            <button @click="modifierCompetence(comp)" class="btn btn-info btn-small">
              ✏️ Modifier
            </button>
            <button @click="supprimerCompetence(comp.id)" class="btn btn-error btn-small">
              🗑️ Supprimer
            </button>
          </div>
        </div>
      </div>
    </div>



    <!-- Onglet Gestion -->
    <div v-if="activeTab === 'gestion'" class="tab-content">
      

      <!-- Statistiques globales -->
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">📊</div>
          <div class="stat-content">
            <div class="stat-value">{{ stats.totalTaches }}</div>
            <div class="stat-label">Total des tâches</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">✅</div>
          <div class="stat-content">
            <div class="stat-value">{{ stats.tachesAssignees }}</div>
            <div class="stat-label">Tâches assignées</div>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">👥</div>
          <div class="stat-content">
            <div class="stat-value">{{ stats.collaborateursDisponibles }}</div>
            <div class="stat-label">Collaborateurs disponibles</div>
          </div>
        </div>
      </div>

      <!-- Messages d'assignation -->
      <div v-if="messageAssignation" class="message-assignation" :class="'message-' + typeMessage">
        <span class="message-icon">
          {{ typeMessage === 'success' ? '✅' : typeMessage === 'error' ? '❌' : 'ℹ️' }}
        </span>
        {{ messageAssignation }}
      </div>

      <!-- Analyse des compétences -->
      <div class="section">
        <h2>Analyse des compétences</h2>
        <div class="competences-grid">
          <div v-for="comp in analyseCompetences" :key="comp.id" class="competence-card">
            <div class="competence-header">
              <h3>{{ comp.nom }}</h3>
              <div class="competence-stats">
                <span class="stat">{{ comp.nbCollaborateurs }} collaborateurs</span>
                <span class="stat">{{ comp.nbTaches }} tâches</span>
              </div>
            </div>
            <div class="competence-body">
              <p>{{ comp.description }}</p>
              <div class="competence-coverage">
                <div class="coverage-bar">
                  <div 
                    class="coverage-fill" 
                    :style="{ width: comp.pourcentageCouverture + '%' }"
                    :class="getCoverageClass(comp.pourcentageCouverture)"
                  ></div>
                </div>
                <span class="coverage-text">{{ comp.pourcentageCouverture }}% de couverture</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Collaborateur -->
    <div v-if="showModalCollaborateur" class="modal-overlay" @click="fermerModalCollaborateur">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h3 class="modal-title">{{ collaborateurEnEdition ? 'Modifier' : 'Ajouter' }} un collaborateur</h3>
          <button @click="fermerModalCollaborateur" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="sauvegarderCollaborateur">
            <div class="form-group">
              <label class="form-label">Prénom:</label>
              <input v-model="formCollaborateur.prenom" class="form-input" required />
            </div>
            <div class="form-group">
              <label class="form-label">Nom:</label>
              <input v-model="formCollaborateur.nom" class="form-input" required />
            </div>
            <div class="form-group">
              <label class="form-label">Email:</label>
              <input v-model="formCollaborateur.email" type="email" class="form-input" required />
            </div>
            <div class="form-group">
              <label class="form-label">Rôle:</label>
              <select v-model="formCollaborateur.role" class="form-input" required>
                <option value="Collaborateur">Collaborateur</option>
                <option value="Manager">Manager</option>
                <option value="Chef de projet">Chef de projet</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Disponible:</label>
              <input type="checkbox" v-model="formCollaborateur.disponible" />
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button @click="fermerModalCollaborateur" class="btn btn-secondary">Annuler</button>
          <button @click="sauvegarderCollaborateur" class="btn btn-primary">
            {{ collaborateurEnEdition ? 'Modifier' : 'Ajouter' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Compétence -->
    <div v-if="showModalCompetence" class="modal-overlay" @click="fermerModalCompetence">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h3 class="modal-title">{{ competenceEnEdition ? 'Modifier' : 'Ajouter' }} une compétence</h3>
          <button @click="fermerModalCompetence" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="sauvegarderCompetence">
            <div class="form-group">
              <label class="form-label">Nom:</label>
              <input v-model="formCompetence.nom" class="form-input" required />
            </div>
            <div class="form-group">
              <label class="form-label">Description: <span class="text-muted">(optionnel)</span></label>
              <textarea v-model="formCompetence.description" class="form-input" rows="3" placeholder="Description de la compétence (optionnel)"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button @click="fermerModalCompetence" class="btn btn-secondary">Annuler</button>
          <button @click="sauvegarderCompetence" class="btn btn-primary">
            {{ competenceEnEdition ? 'Modifier' : 'Ajouter' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Modal Compétence Collaborateur -->
    <div v-if="showModalCompetenceCollaborateur" class="modal-overlay" @click="fermerModalCompetenceCollaborateur">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h3 class="modal-title">Ajouter une compétence à {{ collaborateurSelectionne?.prenom }} {{ collaborateurSelectionne?.nom }}</h3>
          <button @click="fermerModalCompetenceCollaborateur" class="modal-close">&times;</button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="sauvegarderCompetenceCollaborateur">
            <div class="form-group">
              <label class="form-label">Compétence:</label>
              <select v-model="formCompetenceCollaborateur.competence" class="form-input" required>
                <option value="">Sélectionner une compétence</option>
                <option v-for="comp in competences" :key="comp.id" :value="comp.id">
                  {{ comp.nom }}
                </option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Niveau (0-10):</label>
              <input v-model="formCompetenceCollaborateur.niveau" type="number" min="0" max="10" class="form-input" required />
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button @click="fermerModalCompetenceCollaborateur" class="btn btn-secondary">Annuler</button>
          <button @click="sauvegarderCompetenceCollaborateur" class="btn btn-primary">Ajouter</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'

const activeTab = ref('collaborateurs')
const collaborateurs = ref([])
const competences = ref([])
const showModalCollaborateur = ref(false)
const showModalCompetence = ref(false)
const showModalCompetenceCollaborateur = ref(false)
const collaborateurEnEdition = ref(null)
const competenceEnEdition = ref(null)
const collaborateurSelectionne = ref(null)
const messageAssignation = ref('')
const typeMessage = ref('')
const analyseCompetences = ref([])
const taches = ref([])

// ✅ Système de cache persistant avec localStorage
const CACHE_KEY = 'admin_cache'
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

const formCollaborateur = ref({
  prenom: '',
  nom: '',
  email: '',
  role: 'Collaborateur',
  disponible: true
})

const formCompetence = ref({
  nom: '',
  description: ''
})

const formCompetenceCollaborateur = ref({
  competence: '',
  niveau: 5
})

const stats = ref({
  totalTaches: 0,
  tachesAssignees: 0,
  collaborateursDisponibles: 0
})

const fetchCollaborateurs = async (forceRefresh = false) => {
  try {
    // ✅ Vérifier le cache d'abord
    if (!forceRefresh) {
      const cachedData = loadCache()
      if (cachedData && isCacheValid(cachedData) && cachedData.collaborateurs) {
        collaborateurs.value = cachedData.collaborateurs
        stats.value.collaborateursDisponibles = collaborateurs.value.filter(c => c.disponible).length
        return
      }
    }
    const token = localStorage.getItem('token')
    const response = await axios.get('http://127.0.0.1:8000/api/collaborateurs', {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    collaborateurs.value = response.data.data
    stats.value.collaborateursDisponibles = collaborateurs.value.filter(c => c.disponible).length
    
    await enrichirChargeCollaborateurs()
    
    // ✅ Sauvegarder dans le cache persistant
    const cacheData = {
      collaborateurs: collaborateurs.value,
      competences: competences.value,
      timestamp: Date.now()
    }
    saveCache(cacheData)
    
  } catch (error) {
    // Erreur silencieuse
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
    const token = localStorage.getItem('token')
    const response = await axios.get('http://127.0.0.1:8000/api/competences', {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    competences.value = response.data.data
    
    // ✅ Sauvegarder dans le cache persistant
    const cacheData = {
      collaborateurs: collaborateurs.value,
      competences: competences.value,
      timestamp: Date.now()
    }
    saveCache(cacheData)
    
  } catch (error) {
    // Erreur silencieuse
  }
}

const enrichirChargeCollaborateurs = async () => {
  try {
    const token = localStorage.getItem('token')
    for (const collab of collaborateurs.value) {
      const response = await axios.get(`http://127.0.0.1:8000/api/taches/collaborateur/email/${encodeURIComponent(collab.email)}`, {
        headers: { Authorization: `Bearer ${token}` }
      })
      
      const taches = response.data.data || []
      const chargeTotale = taches
        .filter(tache => !['terminee', 'annulee'].includes(tache.statut))
        .reduce((total, tache) => total + (tache.chargeEstimee || 0), 0)
      
      collab.computedChargeHours = chargeTotale
    }
  } catch (error) {
    // Erreur silencieuse
  }
}

const getChargeInfo = (collab) => {
  const charge = collab.computedChargeHours || 0
  const percent = Math.round((charge / 60) * 100)
  return {
    hours: charge.toFixed(1),
    percent: percent
  }
}

const getProgressClass = (percent) => {
  if (percent <= 60) return 'success'
  if (percent <= 80) return 'warning'
  return 'error'
}

const ouvrirFormCollaborateur = (collaborateur = null) => {
  // S'assurer que le modal est fermé avant de l'ouvrir
  showModalCollaborateur.value = false
  
  // Réinitialiser complètement le formulaire
  formCollaborateur.value = {
    prenom: '',
    nom: '',
    email: '',
    role: 'Collaborateur',
    disponible: true
  }
  collaborateurEnEdition.value = null
  
  // Si c'est une modification, remplir le formulaire
  if (collaborateur && typeof collaborateur === 'object' && collaborateur.id) {
    collaborateurEnEdition.value = collaborateur
    formCollaborateur.value = { ...collaborateur }
  } else {
    collaborateurEnEdition.value = null
    formCollaborateur.value = {
      prenom: '',
      nom: '',
      email: '',
      role: 'Collaborateur',
      disponible: true
    }
  }
  
  // Ouvrir le modal
  showModalCollaborateur.value = true
}

const fermerModalCollaborateur = () => {
  // Fermer le modal
  showModalCollaborateur.value = false
  
  // Nettoyer complètement l'état
  collaborateurEnEdition.value = null
  formCollaborateur.value = {
    prenom: '',
    nom: '',
    email: '',
    role: 'Collaborateur',
    disponible: true
  }
  

}

const sauvegarderCollaborateur = async (collaborateurDirect = null) => {
  try {
    // Validation des données
    if (!formCollaborateur.value.prenom || !formCollaborateur.value.nom || !formCollaborateur.value.email) {
      return
    }
    
    const token = localStorage.getItem('token')
    const config = { headers: { Authorization: `Bearer ${token}` } }
    
    let response
    
    // Si un collaborateur est passé directement (pour les modifications rapides)
    if (collaborateurDirect && collaborateurDirect.id) {

      response = await axios.put(`http://127.0.0.1:8000/api/collaborateurs/${collaborateurDirect.id}`, collaborateurDirect, config)
      await fetchCollaborateurs()
      return
    }
    
    // Si c'est une édition via le modal
    if (collaborateurEnEdition.value && collaborateurEnEdition.value.id) {

      response = await axios.put(`http://127.0.0.1:8000/api/collaborateurs/${collaborateurEnEdition.value.id}`, formCollaborateur.value, config)
    } else {

      response = await axios.post('http://127.0.0.1:8000/api/collaborateurs', formCollaborateur.value, config)
      
      // Afficher les informations du compte créé
      if (response.data.userAccount) {
        // Compte utilisateur créé avec succès
      }
    }
    
    console.log('Réponse API:', response.data)
    
    // Fermer le modal et rafraîchir les données
    fermerModalCollaborateur()
    await fetchCollaborateurs()
    
  } catch (error) {
    if (error.response?.status === 422) {
      alert('Erreur de validation: ' + (error.response.data.message || 'Données invalides'))
    } else {
      alert('Erreur lors de la sauvegarde: ' + (error.message || 'Erreur inconnue'))
    }
  }
}

const modifierCollaborateur = (collaborateur) => {
  ouvrirFormCollaborateur(collaborateur)
}

const supprimerCollaborateur = async (id) => {
  if (!confirm('Êtes-vous sûr de vouloir supprimer ce collaborateur ?')) return
  
  try {
    const token = localStorage.getItem('token')
    await axios.delete(`http://127.0.0.1:8000/api/collaborateurs/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    fetchCollaborateurs()
  } catch (error) {
    console.error('Erreur lors de la suppression:', error)
    alert('Erreur lors de la suppression')
  }
}

const ouvrirFormCompetence = (competence = null) => {
  // S'assurer que le modal est fermé avant de l'ouvrir
  showModalCompetence.value = false
  
  // Réinitialiser complètement le formulaire
  formCompetence.value = { nom: '', description: '' }
  competenceEnEdition.value = null
  
  // Si c'est une modification, remplir le formulaire
  if (competence && typeof competence === 'object' && competence.id) {
    competenceEnEdition.value = competence
    formCompetence.value = { 
      nom: competence.nom || '', 
      description: competence.description || '' 
    }
  } else {
    competenceEnEdition.value = null
    formCompetence.value = { nom: '', description: '' }
  }
  
  // Ouvrir le modal
  showModalCompetence.value = true
}

const fermerModalCompetence = () => {
  // Fermer le modal
  showModalCompetence.value = false
  
  // Nettoyer complètement l'état
  competenceEnEdition.value = null
  formCompetence.value = { nom: '', description: '' }
  

}

const sauvegarderCompetence = async () => {
  try {
    // Validation des données
    if (!formCompetence.value.nom) {
      return
    }
    
    const token = localStorage.getItem('token')
    const config = { headers: { Authorization: `Bearer ${token}` } }
    
    let response
    if (competenceEnEdition.value) {
      // Modification d'une compétence existante

      response = await axios.put(`http://127.0.0.1:8000/api/competences/${competenceEnEdition.value.id}`, formCompetence.value, config)

    } else {
      // Ajout d'une nouvelle compétence

      response = await axios.post('http://127.0.0.1:8000/api/competences', formCompetence.value, config)

    }
    
    console.log('Réponse API:', response.data)
    
    // Fermer le modal et rafraîchir les données
    fermerModalCompetence()
    await fetchCompetences()
    
  } catch (error) {
    if (error.response?.status === 422) {
      alert('Erreur de validation: ' + (error.response.data.message || 'Données invalides'))
    } else {
      alert('Erreur lors de la sauvegarde: ' + (error.message || 'Erreur inconnue'))
    }
  }
}

const modifierCompetence = (competence) => {
  ouvrirFormCompetence(competence)
}

const supprimerCompetence = async (id) => {
  if (!confirm('Êtes-vous sûr de vouloir supprimer cette compétence ?')) return
  
  try {
    const token = localStorage.getItem('token')
    await axios.delete(`http://127.0.0.1:8000/api/competences/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    fetchCompetences()
  } catch (error) {
    console.error('Erreur lors de la suppression:', error)
    alert('Erreur lors de la suppression')
  }
}

const ajouterCompetenceCollaborateur = (collaborateur) => {
  collaborateurSelectionne.value = collaborateur
  formCompetenceCollaborateur.value = { competence: '', niveau: 5 }
  showModalCompetenceCollaborateur.value = true
}

const fermerModalCompetenceCollaborateur = () => {
  showModalCompetenceCollaborateur.value = false
  collaborateurSelectionne.value = null
  formCompetenceCollaborateur.value = { competence: '', niveau: 5 }
}

const sauvegarderCompetenceCollaborateur = async () => {
  try {
    const token = localStorage.getItem('token')
    const config = { headers: { Authorization: `Bearer ${token}` } }
    
    const payload = {
      collaborateur: collaborateurSelectionne.value.id,
      competence: formCompetenceCollaborateur.value.competence,
      niveau: parseInt(formCompetenceCollaborateur.value.niveau)
    }
    
    await axios.post('http://127.0.0.1:8000/api/collaborateur-competences', payload, config)
    
    fermerModalCompetenceCollaborateur()
    fetchCollaborateurs()
  } catch (error) {
    alert('Erreur lors de la sauvegarde')
  }
}

const supprimerCompetenceCollaborateur = async (collaborateurId, competenceId) => {
  if (!confirm('Êtes-vous sûr de vouloir supprimer cette compétence ?')) return
  
  try {
    const token = localStorage.getItem('token')
    await axios.delete(`http://127.0.0.1:8000/api/collaborateur-competences/${competenceId}`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    fetchCollaborateurs()
  } catch (error) {
    console.error('Erreur lors de la suppression:', error)
    alert('Erreur lors de la suppression')
  }
}

const refreshData = (forceRefresh = false) => {
  fetchCollaborateurs(forceRefresh)
  fetchCompetences(forceRefresh)
  if (activeTab.value === 'gestion') {
    chargerDonnees(forceRefresh)
  }
}

// ======= Fonctions d'analyse des compétences =======
const afficherMessage = (message, type = 'success') => {
  messageAssignation.value = message
  typeMessage.value = type
  setTimeout(() => {
    messageAssignation.value = ''
    typeMessage.value = ''
  }, 5000)
}

// ======= Fonctions d'analyse des compétences =======
const chargerDonnees = async (forceRefresh = false) => {
  try {
    // ✅ Vérifier le cache d'abord pour la section Gestion
    if (!forceRefresh) {
      const cachedData = loadCache()
      if (cachedData && isCacheValid(cachedData) && cachedData.gestionData) {
        taches.value = cachedData.gestionData.taches || []
        analyseCompetences.value = cachedData.gestionData.analyseCompetences || []
        stats.value = cachedData.gestionData.stats || stats.value
        return
      }
    }
    // Charger d'abord les collaborateurs et tâches
    await Promise.all([
      chargerTaches(forceRefresh),
      fetchCollaborateurs(forceRefresh)
    ])
    
    // Ensuite charger les compétences avec les données disponibles
    await chargerCompetences(forceRefresh)
    
    // ✅ Sauvegarder les données de gestion dans le cache
    const cacheData = {
      collaborateurs: collaborateurs.value,
      competences: competences.value,
      gestionData: {
        taches: taches.value,
        analyseCompetences: analyseCompetences.value,
        stats: stats.value
      },
      timestamp: Date.now()
    }
    saveCache(cacheData)
    
  } catch (error) {
    afficherMessage('Erreur lors du chargement des données', 'error')
  }
}

const chargerTaches = async (forceRefresh = false) => {
  try {
    // ✅ Vérifier le cache d'abord
    if (!forceRefresh) {
      const cachedData = loadCache()
      if (cachedData && isCacheValid(cachedData) && cachedData.gestionData?.taches) {
        taches.value = cachedData.gestionData.taches
        stats.value.totalTaches = taches.value.length
        stats.value.tachesAssignees = taches.value.filter(t => t.collaborateur).length
        return
      }
    }
    const token = localStorage.getItem('token')
    const response = await axios.get('http://127.0.0.1:8000/api/taches', {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    taches.value = response.data.data || []
    
    stats.value.totalTaches = taches.value.length
    stats.value.tachesAssignees = taches.value.filter(t => t.collaborateur).length
    
    // ✅ Sauvegarder dans le cache
    const cacheData = {
      collaborateurs: collaborateurs.value,
      competences: competences.value,
      gestionData: {
        taches: taches.value,
        analyseCompetences: analyseCompetences.value,
        stats: stats.value
      },
      timestamp: Date.now()
    }
    saveCache(cacheData)
    
  } catch (error) {
    // Erreur silencieuse
  }
}

const chargerCompetences = async (forceRefresh = false) => {
  try {
    // ✅ Vérifier le cache d'abord
    if (!forceRefresh) {
      const cachedData = loadCache()
      if (cachedData && isCacheValid(cachedData) && cachedData.gestionData?.analyseCompetences) {
        analyseCompetences.value = cachedData.gestionData.analyseCompetences
        return
      }
    }
    const token = localStorage.getItem('token')
    const response = await axios.get('http://127.0.0.1:8000/api/competences', {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    const competencesData = response.data.data || []
    
    // Vérifier que les collaborateurs sont disponibles
    if (collaborateurs.value.length === 0) {
      return
    }
    
    analyseCompetences.value = competencesData.map(comp => {

      
              // Compter les collaborateurs ayant cette compétence
        const nbCollaborateurs = collaborateurs.value.filter(collab => {
          // Vérifier si le collaborateur a des compétences
          if (collab.competences && collab.competences.length > 0) {
            // Convertir le Proxy en tableau normal et vérifier les IDs
            const competencesArray = Array.from(collab.competences)
            
            // Vérifier si la compétence actuelle est dans la liste
            return competencesArray.some(competence => {
              // Vérifier par l'ID de la compétence dans l'objet competence
              if (competence.competence && competence.competence.id === comp.id) {
                return true
              }
              
              // Vérifier par nom (fallback)
              if (competence.competence && competence.competence.nom === comp.nom) {
                return true
              }
              
              return false
            })
          }
          
          return false
        }).length
      
      // Compter les tâches nécessitant cette compétence
      const nbTaches = taches.value.filter(tache => {
        if (tache.competenceRequise) {
          // Si c'est un objet avec ID
          if (tache.competenceRequise.id === comp.id) {
            return true
          }
          // Si c'est un objet avec nom
          if (tache.competenceRequise.nom === comp.nom) {
            return true
          }
          // Si c'est directement une chaîne
          if (tache.competenceRequise === comp.nom) {
            return true
          }
        }
        return false
      }).length
      
      // Calculer le pourcentage de couverture selon la logique métier
      let pourcentageCouverture = 0
      if (nbCollaborateurs === 1) {
        pourcentageCouverture = 33
      } else if (nbCollaborateurs === 2) {
        pourcentageCouverture = 66
      } else if (nbCollaborateurs >= 3) {
        pourcentageCouverture = 100
      }
      
      
      
      return {
        ...comp,
        nbCollaborateurs,
        nbTaches,
        pourcentageCouverture
      }
    })
    
    // ✅ Sauvegarder dans le cache
    const cacheData = {
      collaborateurs: collaborateurs.value,
      competences: competences.value,
      gestionData: {
        taches: taches.value,
        analyseCompetences: analyseCompetences.value,
        stats: stats.value
      },
      timestamp: Date.now()
    }
    saveCache(cacheData)
    
  } catch (error) {
    // Erreur silencieuse
  }
}

const getCoverageClass = (pourcentage) => {
  if (pourcentage >= 80) return 'coverage-excellent'
  if (pourcentage >= 60) return 'coverage-good'
  if (pourcentage >= 40) return 'coverage-warning'
  return 'coverage-poor'
}

onMounted(() => {
  fetchCollaborateurs()
  fetchCompetences()
})

watch(activeTab, (newTab) => {
  if (newTab === 'gestion') {
    chargerDonnees() // Utiliser le cache par défaut
  }
})
</script>

<style scoped>
/* Styles pour la gestion */
.admin-header {
  text-align: center;
  margin-bottom: 2rem;
}

.admin-header h2 {
  font-size: 2rem;
  color: #1f2937;
  margin-bottom: 0.5rem;
}

.subtitle {
  font-size: 1.1rem;
  color: #6b7280;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.stat-icon {
  font-size: 2rem;
}

.stat-value {
  font-size: 2rem;
  font-weight: bold;
  color: #1f2937;
}

.stat-label {
  color: #6b7280;
  font-size: 0.9rem;
}

.section {
  margin-bottom: 2rem;
}

.section h2 {
  margin-bottom: 1.5rem;
  color: #1f2937;
  border-bottom: 2px solid #e5e7eb;
  padding-bottom: 0.5rem;
}

.message-assignation {
  padding: 1rem 1.5rem;
  border-radius: 8px;
  margin-bottom: 2rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
  font-weight: 500;
}

.message-success {
  background-color: #d1fae5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

.message-error {
  background-color: #fee2e2;
  color: #991b1b;
  border: 1px solid #fca5a5;
}

.message-info {
  background-color: #dbeafe;
  color: #1e3a8a;
  border: 1px solid #93c5fd;
}

.message-icon {
  font-size: 1.2rem;
}

.mb-lg {
  margin-bottom: 2rem;
}

/* Compétences */
.competences-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}

.competence-card {
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.competence-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.competence-header h3 {
  margin: 0;
  color: #1f2937;
}

.competence-stats {
  display: flex;
  gap: 1rem;
}

.competence-stats .stat {
  background: #f3f4f6;
  color: #6b7280;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.8rem;
}

.competence-body p {
  color: #6b7280;
  margin-bottom: 1rem;
  line-height: 1.5;
}

.coverage-bar {
  width: 100%;
  height: 8px;
  background: #e5e7eb;
  border-radius: 4px;
  overflow: hidden;
  margin-bottom: 0.5rem;
}

.coverage-fill {
  height: 100%;
  transition: width 0.3s ease;
}

.coverage-excellent { background: #10b981; }
.coverage-good { background: #3b82f6; }
.coverage-warning { background: #f59e0b; }
.coverage-poor { background: #ef4444; }

.coverage-text {
  font-size: 0.875rem;
  color: #6b7280;
}

.text-muted {
  color: #6b7280;
  font-size: 0.875rem;
  font-weight: normal;
}

/* Responsive */
@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .competences-grid {
    grid-template-columns: 1fr;
  }
}
</style>