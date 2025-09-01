<template>
  <div class="dashboard-container">
    <!-- En-tête du dashboard -->
    <div class="dashboard-header">
      <div class="dashboard-title-section">
        <h1 class="dashboard-title">Tableau de bord</h1>
        <p class="dashboard-subtitle">Vue d'ensemble de votre activité et de votre équipe</p>
      </div>
      <div class="dashboard-actions">
        <button @click="refreshData(true)" class="btn btn-primary">
          <i class="icon">🔄</i>
          Actualiser
        </button>
      </div>
    </div>

    <!-- Section 1: Métriques clés -->
    <div class="metrics-grid">
      <!-- Collaborateurs -->
      <div class="metric-card metric-card-blue">
        <div class="metric-content">
          <div class="metric-icon metric-icon-blue">
            <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
          </div>
                 <div class="metric-info">
         <p class="metric-label">Collaborateurs</p>
         <p class="metric-value">{{ stats.totalCollaborateurs }}</p>
       </div>
     </div>
     <div class="metric-footer">
       <span class="metric-change metric-change-positive">{{ stats.collaborateursDisponibles }} disponibles</span>
       <span class="metric-period">sur {{ stats.totalCollaborateurs }}</span>
     </div>
      </div>

      <!-- Tâches en cours -->
      <div class="metric-card metric-card-yellow">
        <div class="metric-content">
          <div class="metric-icon metric-icon-yellow">
            <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
          </div>
                 <div class="metric-info">
         <p class="metric-label">Tâches en cours</p>
         <p class="metric-value">{{ stats.tachesEnCours }}</p>
       </div>
     </div>
     <div class="metric-footer">
       <span class="metric-change metric-change-warning">{{ stats.tachesEnRetard }} en retard</span>
       <span class="metric-period">• {{ stats.totalTaches }} total</span>
     </div>
      </div>

      <!-- Projets actifs -->
      <div class="metric-card metric-card-green">
        <div class="metric-content">
          <div class="metric-icon metric-icon-green">
            <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
          </div>
                 <div class="metric-info">
         <p class="metric-label">Projets actifs</p>
         <p class="metric-value">{{ stats.projetsEnCours }}</p>
       </div>
     </div>
     <div class="metric-footer">
       <span class="metric-change metric-change-success">{{ stats.totalProjets }}</span>
       <span class="metric-period">projets total</span>
     </div>
      </div>

      <!-- Tâches terminées -->
      <div class="metric-card metric-card-purple">
        <div class="metric-content">
          <div class="metric-icon metric-icon-purple">
            <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
                 <div class="metric-info">
         <p class="metric-label">Tâches terminées</p>
         <p class="metric-value">{{ stats.tachesTerminees }}</p>
       </div>
     </div>
     <div class="metric-footer">
       <span class="metric-change metric-change-info">{{ stats.totalTaches }}</span>
       <span class="metric-period">tâches total</span>
     </div>
      </div>
    </div>

    <!-- Section 2: Projets en cours -->
    <div class="dashboard-section">
      <div class="section-header">
        <div class="section-title-wrapper">
          <div class="section-icon section-icon-blue">
            <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
          </div>
          <h3 class="section-title">Projets en cours</h3>
        </div>
                 <span class="section-count">{{ projets.length }} projets</span>
       </div>
       <div v-if="loading" class="loading-state">
         <p>Chargement des projets...</p>
       </div>
       <div v-else-if="projets.length === 0" class="empty-state">
         <p>Aucun projet trouvé</p>
       </div>
       <div v-else class="projects-grid">
         <div v-for="projet in projets.slice(0, 3)" :key="projet.id" class="project-card">
           <div class="project-header">
             <div class="project-status project-status-green"></div>
             <h4 class="project-name">{{ projet.titre || projet.nom }}</h4>
           </div>
           <p class="project-description">{{ projet.description || 'Aucune description' }}</p>
           <div class="project-progress">
             <div class="progress-bar">
               <div class="progress-fill progress-fill-green" :style="{ width: getProjectProgress(projet) + '%' }"></div>
             </div>
             <span class="progress-text">{{ getProjectProgress(projet) }}%</span>
           </div>
           <div class="project-deadline">
             <span class="deadline-label">Échéance:</span> {{ formatDate(projet.date_fin_prevue || projet.dateFinPrevue) }}
           </div>
         </div>
       </div>
    </div>

    <!-- Section 3: Équipe -->
    <div class="dashboard-section">
      <div class="section-header">
        <div class="section-title-wrapper">
          <div class="section-icon section-icon-green">
            <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
          </div>
          <h3 class="section-title">Mon équipe</h3>
        </div>
                 <span class="section-count">{{ collaborateurs.length }} collaborateurs</span>
       </div>
       <div v-if="loading" class="loading-state">
         <p>Chargement de l'équipe...</p>
       </div>
       <div v-else-if="collaborateurs.length === 0" class="empty-state">
         <p>Aucun collaborateur trouvé</p>
       </div>
       <div v-else class="team-grid">
         <div v-for="collab in (showAllCollaborateurs ? collaborateurs : collaborateurs.slice(0, 4))" :key="collab.id" class="team-card">
           <div class="team-member">
             <div class="member-avatar member-avatar-blue">{{ (collab.prenom || collab.nom).charAt(0).toUpperCase() }}</div>
             <div class="member-info">
               <h4 class="member-name">{{ collab.prenom }} {{ collab.nom }}</h4>
               <p class="member-role">{{ collab.role }}</p>
             </div>
           </div>
           <div class="member-status">
             <div class="status-indicator" :class="collab.disponible ? 'status-online' : 'status-offline'"></div>
             <span class="status-text">{{ collab.disponible ? 'Disponible' : 'Indisponible' }}</span>
             <span class="member-tasks member-tasks-blue">{{ collab.computedChargeHours || 0 }}h</span>
           </div>
         </div>
         
         <!-- Bouton Afficher plus/moins pour les collaborateurs -->
         <div v-if="collaborateurs.length > 4" class="show-more-container">
           <button @click="showAllCollaborateurs = !showAllCollaborateurs" class="btn-show-more">
             {{ showAllCollaborateurs ? 'Afficher moins' : 'Afficher plus' }}
           </button>
         </div>
       </div>
    </div>

    <!-- Section 4: Tâches récentes -->
    <div class="dashboard-section">
      <div class="section-header">
        <div class="section-title-wrapper">
          <div class="section-icon section-icon-yellow">
            <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
          </div>
          <h3 class="section-title">Tâches récentes</h3>
        </div>
        <span class="section-count">Aujourd'hui</span>
      </div>
             <div v-if="loading" class="loading-state">
         <p>Chargement des tâches...</p>
       </div>
       <div v-else-if="taches.length === 0" class="empty-state">
         <p>Aucune tâche trouvée</p>
       </div>
       <div v-else class="tasks-grid">
         <div v-for="tache in (showAllTaches ? taches : taches.slice(0, 4))" :key="tache.id" class="task-card">
           <div class="task-header">
             <div class="task-priority" :class="'task-priority-' + (tache.priorite || 'normal')"></div>
             <h4 class="task-name">{{ tache.titre }}</h4>
           </div>
           <p class="task-description">{{ tache.description || 'Aucune description' }}</p>
           <div class="task-footer">
             <span class="task-priority-label" :class="'task-priority-' + (tache.priorite || 'normal') + '-text'">{{ tache.priorite || 'Normale' }}</span>
             <span class="task-assignee">{{ tache.collaborateur ? tache.collaborateur.prenom + ' ' + tache.collaborateur.nom : 'Non assigné' }}</span>
           </div>
         </div>
         
         <!-- Bouton Afficher plus/moins pour les tâches -->
         <div v-if="taches.length > 4" class="show-more-container">
           <button @click="showAllTaches = !showAllTaches" class="btn-show-more">
             {{ showAllTaches ? 'Afficher moins' : 'Afficher plus' }}
           </button>
         </div>
       </div>
    </div>

    <!-- Section 5: Activité récente -->
    <div class="dashboard-section">
      <div class="section-header">
        <div class="section-title-wrapper">
          <div class="section-icon section-icon-purple">
            <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h3 class="section-title">Activité récente</h3>
        </div>
                 <button @click="refreshData" class="btn-refresh">🔄 Actualiser</button>
       </div>
       <div class="activity-grid">
         <div class="activity-card">
           <div class="activity-icon activity-icon-blue">
             <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
             </svg>
           </div>
           <div class="activity-content">
             <p class="activity-text">{{ stats.tachesEnCours }} tâches en cours</p>
             <p class="activity-time">Mise à jour récente</p>
           </div>
         </div>

         <div class="activity-card">
           <div class="activity-icon activity-icon-green">
             <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
             </svg>
           </div>
           <div class="activity-content">
             <p class="activity-text">{{ stats.tachesTerminees }} tâches terminées</p>
             <p class="activity-time">Ce mois</p>
           </div>
         </div>

         <div class="activity-card">
           <div class="activity-icon activity-icon-yellow">
             <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
             </svg>
           </div>
           <div class="activity-content">
             <p class="activity-text">{{ stats.tachesEnRetard }} tâches en retard</p>
             <p class="activity-time">Attention requise</p>
           </div>
         </div>

         <div class="activity-card">
           <div class="activity-icon activity-icon-purple">
             <svg class="icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
               <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
             </svg>
           </div>
           <div class="activity-content">
             <p class="activity-text">{{ stats.collaborateursDisponibles }} collaborateurs disponibles</p>
             <p class="activity-time">Sur {{ stats.totalCollaborateurs }}</p>
           </div>
         </div>
       </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

// Données réactives pour le dashboard
const collaborateurs = ref([])
const projets = ref([])
const taches = ref([])
const competences = ref([])
const loading = ref(true)

// États d'affichage pour "Afficher plus"
const showAllCollaborateurs = ref(false)
const showAllTaches = ref(false)

// ✅ Système de cache persistant avec localStorage
const CACHE_KEY = 'dashboard_cache'
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

// Statistiques calculées
const stats = computed(() => ({
  totalCollaborateurs: collaborateurs.value.length,
  collaborateursDisponibles: collaborateurs.value.filter(c => c.disponible).length,
  totalProjets: projets.value.length,
  projetsEnCours: projets.value.filter(p => p.statut === 'en_cours').length,
  totalTaches: taches.value.length,
  tachesEnCours: taches.value.filter(t => t.statut === 'en_cours').length,
  tachesTerminees: taches.value.filter(t => t.statut === 'terminee').length,
  tachesEnRetard: taches.value.filter(t => {
    if (t.dateFin && t.statut !== 'terminee') {
      return new Date(t.dateFin) < new Date()
    }
    return false
  }).length
}))

// Récupération des collaborateurs
const fetchCollaborateurs = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get('http://127.0.0.1:8000/api/collaborateurs', {
      headers: { Authorization: `Bearer ${token}` }
    })
    collaborateurs.value = response.data.data || response.data || []
  } catch (error) {
    collaborateurs.value = []
  }
}

// Récupération des projets
const fetchProjets = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get('http://127.0.0.1:8000/api/missions', {
      headers: { Authorization: `Bearer ${token}` }
    })
    const apiList = Array.isArray(response.data.data) ? response.data.data : response.data
    
    // Récupérer les tâches pour chaque projet
    const projetsAvecTaches = await Promise.all(apiList.map(async (projet) => {
      try {
        const tachesResponse = await axios.get(`http://127.0.0.1:8000/api/taches/projet/${projet.id}`, {
          headers: { Authorization: `Bearer ${token}` }
        })
        const taches = Array.isArray(tachesResponse.data.data) ? tachesResponse.data.data : tachesResponse.data
        return { ...projet, taches }
      } catch (error) {
        return { ...projet, taches: [] }
      }
    }))
    
    projets.value = projetsAvecTaches
  } catch (error) {
    projets.value = []
  }
}

// Récupération des tâches
const fetchTaches = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get('http://127.0.0.1:8000/api/taches', {
      headers: { Authorization: `Bearer ${token}` }
    })
    taches.value = response.data.data || response.data || []
  } catch (error) {
    taches.value = []
  }
}

// Récupération des compétences
const fetchCompetences = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get('http://127.0.0.1:8000/api/competences', {
      headers: { Authorization: `Bearer ${token}` }
    })
    competences.value = response.data.data || response.data || []
  } catch (error) {
    competences.value = []
  }
}

// Chargement de toutes les données
const loadDashboardData = async (forceRefresh = false) => {
  loading.value = true
  try {
    // ✅ Vérifier le cache d'abord
    if (!forceRefresh) {
      const cachedData = loadCache()
      if (cachedData && isCacheValid(cachedData)) {
        collaborateurs.value = cachedData.collaborateurs || []
        projets.value = cachedData.projets || []
        taches.value = cachedData.taches || []
        competences.value = cachedData.competences || []
        loading.value = false
        return
      }
    }
    await Promise.all([
      fetchCollaborateurs(),
      fetchProjets(),
      fetchTaches(),
      fetchCompetences()
    ])
    
    // ✅ Sauvegarder dans le cache persistant
    const cacheData = {
      collaborateurs: collaborateurs.value,
      projets: projets.value,
      taches: taches.value,
      competences: competences.value,
      timestamp: Date.now()
    }
    saveCache(cacheData)
    
  } catch (error) {
    // Erreur silencieuse
  } finally {
    loading.value = false
  }
}

// Actualisation des données
const refreshData = (forceRefresh = false) => {
  loadDashboardData(forceRefresh)
}



// Formatage des dates
const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('fr-FR', { 
    day: '2-digit', 
    month: '2-digit', 
    year: 'numeric' 
  })
}

// Calcul de la progression d'un projet
const getProjectProgress = (projet) => {
  if (!projet.taches || projet.taches.length === 0) return 0
  
  const totalTaches = projet.taches.length
  const tachesTerminees = projet.taches.filter(t => t.statut === 'terminee').length
  
  return Math.round((tachesTerminees / totalTaches) * 100)
}

// Chargement initial
onMounted(() => {
  loadDashboardData()
})

// Méthodes utilitaires
const getStatusColor = (status) => {
  const colors = {
    'en_cours': 'green',
    'en_retard': 'yellow',
    'termine': 'blue',
    'critique': 'red'
  }
  return colors[status] || 'gray'
}

const getPriorityColor = (priority) => {
  const colors = {
    'urgent': 'red',
    'high': 'orange',
    'medium': 'yellow',
    'low': 'green',
    'normal': 'blue'
  }
  return colors[priority] || 'gray'
}

const getTeamColor = (color) => {
  const colors = {
    'blue': 'bg-blue-600',
    'green': 'bg-green-600',
    'purple': 'bg-purple-600',
    'orange': 'bg-orange-600',
    'red': 'bg-red-600'
  }
  return colors[color] || 'bg-gray-600'
}
</script>

<style scoped>
/* Dashboard Container */
.dashboard-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: var(--spacing-xl);
}

/* Dashboard Header */
.dashboard-header {
  margin-bottom: var(--spacing-2xl);
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
}

.dashboard-title-section {
  flex: 1;
}

.dashboard-actions {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.dashboard-title {
  font-size: 2rem;
  font-weight: 700;
  color: var(--secondary-900);
  margin-bottom: var(--spacing-sm);
}

.dashboard-subtitle {
  color: var(--secondary-600);
  font-size: 1.125rem;
}

/* Metrics Grid */
.metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: var(--spacing-lg);
  margin-bottom: var(--spacing-2xl);
}

.metric-card {
  background: white;
  border-radius: var(--border-radius-xl);
  padding: var(--spacing-lg);
  box-shadow: var(--shadow-lg);
  border-left: 4px solid;
  transition: transform 0.2s ease-in-out;
}

.metric-card:hover {
  transform: translateY(-2px);
}

.metric-card-blue {
  border-left-color: var(--primary-500);
}

.metric-card-yellow {
  border-left-color: var(--warning-500);
}

.metric-card-green {
  border-left-color: var(--success-500);
}

.metric-card-purple {
  border-left-color: var(--info-500);
}

.metric-content {
  display: flex;
  align-items: center;
  margin-bottom: var(--spacing-md);
}

.metric-icon {
  width: 3rem;
  height: 3rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: var(--spacing-md);
}

.metric-icon-blue {
  background: var(--primary-100);
  color: var(--primary-600);
}

.metric-icon-yellow {
  background: var(--warning-100);
  color: var(--warning-600);
}

.metric-icon-green {
  background: var(--success-100);
  color: var(--success-600);
}

.metric-icon-purple {
  background: var(--info-100);
  color: var(--info-600);
}

.icon-svg {
  width: 2rem;
  height: 2rem;
}

.metric-info {
  flex: 1;
}

.metric-label {
  font-size: 0.875rem;
  font-weight: 500;
  color: var(--secondary-600);
  margin-bottom: 0.25rem;
}

.metric-value {
  font-size: 1.5rem;
  font-weight: 700;
  color: var(--secondary-900);
}

.metric-footer {
  display: flex;
  align-items: center;
  gap: var(--spacing-sm);
}

.metric-change {
  font-size: 0.875rem;
  font-weight: 500;
}

.metric-change-positive {
  color: var(--success-600);
}

.metric-change-warning {
  color: var(--warning-600);
}

.metric-change-success {
  color: var(--success-600);
}

.metric-change-info {
  color: var(--info-600);
}

.metric-period {
  font-size: 0.875rem;
  color: var(--secondary-600);
}

/* Dashboard Section */
.dashboard-section {
  background: white;
  border-radius: var(--border-radius-xl);
  padding: var(--spacing-lg);
  box-shadow: var(--shadow-lg);
  margin-bottom: var(--spacing-xl);
}

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: var(--spacing-lg);
}

.section-title-wrapper {
  display: flex;
  align-items: center;
}

.section-icon {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: var(--border-radius-lg);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: var(--spacing-md);
}

.section-icon-blue {
  background: var(--primary-100);
  color: var(--primary-600);
}

.section-icon-green {
  background: var(--success-100);
  color: var(--success-600);
}

.section-icon-yellow {
  background: var(--warning-100);
  color: var(--warning-600);
}

.section-icon-purple {
  background: var(--info-100);
  color: var(--info-600);
}

.section-icon-indigo {
  background: var(--primary-100);
  color: var(--primary-600);
}

.section-title {
  font-size: 1.125rem;
  font-weight: 600;
  color: var(--secondary-900);
}

.section-count {
  font-size: 0.875rem;
  color: var(--secondary-500);
}

/* Projects Grid */
.projects-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: var(--spacing-lg);
}

.project-card {
  background: var(--secondary-50);
  border-radius: var(--border-radius-lg);
  padding: var(--spacing-md);
}

.project-header {
  display: flex;
  align-items: center;
  margin-bottom: var(--spacing-md);
}

.project-status {
  width: 0.75rem;
  height: 0.75rem;
  border-radius: 50%;
  margin-right: var(--spacing-md);
}

.project-status-green {
  background: var(--success-500);
}

.project-status-yellow {
  background: var(--warning-500);
}

.project-status-blue {
  background: var(--primary-500);
}

.project-name {
  font-weight: 500;
  color: var(--secondary-900);
}

.project-description {
  font-size: 0.875rem;
  color: var(--secondary-600);
  margin-bottom: var(--spacing-md);
}

.project-progress {
  display: flex;
  align-items: center;
  margin-bottom: var(--spacing-md);
}

.progress-bar {
  flex: 1;
  height: 0.5rem;
  background: var(--secondary-200);
  border-radius: 9999px;
  margin-right: var(--spacing-md);
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  border-radius: 9999px;
}

.progress-fill-green {
  background: var(--success-500);
}

.progress-fill-yellow {
  background: var(--warning-500);
}

.progress-fill-blue {
  background: var(--primary-500);
}

.progress-text {
  font-size: 0.875rem;
  color: var(--secondary-600);
}

.project-deadline {
  font-size: 0.875rem;
  color: var(--secondary-500);
}

.deadline-label {
  font-weight: 500;
}

/* Team Grid */
.team-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: var(--spacing-md);
}

.team-card {
  background: var(--secondary-50);
  border-radius: var(--border-radius-lg);
  padding: var(--spacing-md);
}

.team-member {
  display: flex;
  align-items: center;
  margin-bottom: var(--spacing-md);
}

.member-avatar {
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 600;
  color: white;
  margin-right: var(--spacing-md);
}

.member-avatar-blue {
  background: var(--primary-600);
}

.member-avatar-green {
  background: var(--success-600);
}

.member-avatar-purple {
  background: var(--info-600);
}

.member-avatar-orange {
  background: var(--warning-600);
}

.member-info {
  flex: 1;
}

.member-name {
  font-weight: 500;
  color: var(--secondary-900);
  margin-bottom: 0.25rem;
}

.member-role {
  font-size: 0.75rem;
  color: var(--secondary-600);
}

.member-status {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.status-indicator {
  width: 0.5rem;
  height: 0.5rem;
  border-radius: 50%;
  margin-right: var(--spacing-sm);
}

.status-online {
  background: var(--success-500);
}

.status-offline {
  background: var(--secondary-400);
}

.status-text {
  font-size: 0.75rem;
  color: var(--secondary-500);
  flex: 1;
}

.member-tasks {
  font-size: 0.875rem;
  font-weight: 500;
}

.member-tasks-blue {
  color: var(--primary-600);
}

.member-tasks-green {
  color: var(--success-600);
}

.member-tasks-purple {
  color: var(--info-600);
}

.member-tasks-orange {
  color: var(--warning-600);
}

/* Tasks Grid */
.tasks-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: var(--spacing-md);
}

.task-card {
  background: var(--secondary-50);
  border-radius: var(--border-radius-lg);
  padding: var(--spacing-md);
}

.task-header {
  display: flex;
  align-items: center;
  margin-bottom: var(--spacing-sm);
}

.task-priority {
  width: 0.5rem;
  height: 0.5rem;
  border-radius: 50%;
  margin-right: var(--spacing-sm);
}

.task-priority-urgent {
  background: var(--error-500);
}

.task-priority-medium {
  background: var(--warning-500);
}

.task-priority-low {
  background: var(--success-500);
}

.task-priority-normal {
  background: var(--primary-500);
}

.task-name {
  font-weight: 500;
  color: var(--secondary-900);
  font-size: 0.875rem;
}

.task-description {
  font-size: 0.75rem;
  color: var(--secondary-600);
  margin-bottom: var(--spacing-sm);
}

.task-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.task-priority-label {
  font-size: 0.75rem;
  font-weight: 500;
}

.task-priority-urgent-text {
  color: var(--error-600);
}

.task-priority-medium-text {
  color: var(--warning-600);
}

.task-priority-low-text {
  color: var(--success-600);
}

.task-priority-normal-text {
  color: var(--primary-600);
}

.task-assignee {
  font-size: 0.75rem;
  color: var(--secondary-500);
}

/* Activity Grid */
.activity-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: var(--spacing-md);
}

.activity-card {
  background: var(--secondary-50);
  border-radius: var(--border-radius-lg);
  padding: var(--spacing-md);
  display: flex;
  align-items: center;
}

.activity-icon {
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: var(--spacing-md);
}

.activity-icon-blue {
  background: var(--primary-100);
  color: var(--primary-600);
}

.activity-icon-green {
  background: var(--success-100);
  color: var(--success-600);
}

.activity-icon-yellow {
  background: var(--warning-100);
  color: var(--warning-600);
}

.activity-icon-purple {
  background: var(--info-100);
  color: var(--info-600);
}

.activity-content {
  flex: 1;
}

.activity-text {
  font-size: 0.875rem;
  color: var(--secondary-900);
  margin-bottom: 0.25rem;
}

.activity-time {
  font-size: 0.75rem;
  color: var(--secondary-600);
}



/* Responsive Design */
@media (max-width: 1024px) {
  .metrics-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .projects-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .team-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .tasks-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .activity-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .dashboard-container {
    padding: var(--spacing-md);
  }
  
  .metrics-grid {
    grid-template-columns: 1fr;
  }
  
  .projects-grid {
    grid-template-columns: 1fr;
  }
  
  .team-grid {
    grid-template-columns: 1fr;
  }
  
  .tasks-grid {
    grid-template-columns: 1fr;
  }
  
  .activity-grid {
    grid-template-columns: 1fr;
  }
}

/* États de chargement et vides */
.loading-state {
  text-align: center;
  padding: var(--spacing-xl);
  color: var(--secondary-600);
}

.empty-state {
  text-align: center;
  padding: var(--spacing-xl);
  color: var(--secondary-500);
}

/* Bouton de rafraîchissement */
.btn-refresh {
  background: var(--primary-100);
  color: var(--primary-600);
  border: 1px solid var(--primary-200);
  border-radius: var(--border-radius-md);
  padding: var(--spacing-sm) var(--spacing-md);
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-refresh:hover {
  background: var(--primary-200);
  border-color: var(--primary-300);
}

/* Bouton Afficher plus/moins */
.show-more-container {
  grid-column: 1 / -1;
  text-align: center;
  margin-top: var(--spacing-md);
}

.btn-show-more {
  background: var(--secondary-100);
  color: var(--secondary-700);
  border: 1px solid var(--secondary-300);
  border-radius: var(--border-radius-md);
  padding: var(--spacing-sm) var(--spacing-lg);
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-show-more:hover {
  background: var(--secondary-200);
  border-color: var(--secondary-400);
  color: var(--secondary-800);
}
</style>
