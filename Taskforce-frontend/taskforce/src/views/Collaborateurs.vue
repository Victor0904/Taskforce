<template>
  <div class="min-vh-100 bg-light">
    <!-- Header -->
    <div class="bg-white shadow-sm border-bottom">
      <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <h1 class="h2 fw-bold text-dark mb-1">Collaborateurs</h1>
            <p class="text-muted small mb-0">Gérez vos collaborateurs et leurs informations</p>
          </div>
          <button 
            @click="goToNew" 
            class="btn btn-primary d-flex align-items-center"
            type="button"
          >
            <svg class="me-2" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nouveau Collaborateur
          </button>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="container-fluid py-4">
      <!-- Stats Cards -->
      <div class="row g-4 mb-4">
        <div class="col-md-4">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="p-3 bg-primary bg-opacity-10 rounded">
                  <svg class="text-primary" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                </div>
                <div class="ms-3">
                  <p class="text-muted small mb-1 fw-medium">Total</p>
                  <h3 class="h4 fw-semibold mb-0">{{ collaborateurs.length }}</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="p-3 bg-success bg-opacity-10 rounded">
                  <svg class="text-success" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div class="ms-3">
                  <p class="text-muted small mb-1 fw-medium">Actifs</p>
                  <h3 class="h4 fw-semibold mb-0">{{ collaborateursActifs }}</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="card h-100 border-0 shadow-sm">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="p-3 bg-danger bg-opacity-10 rounded">
                  <svg class="text-danger" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div class="ms-3">
                  <p class="text-muted small mb-1 fw-medium">Inactifs</p>
                  <h3 class="h4 fw-semibold mb-0">{{ collaborateursInactifs }}</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Search and Filter -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-8">
              <label for="search" class="visually-hidden">Rechercher</label>
              <div class="position-relative">
                <div class="position-absolute top-50 start-0 translate-middle-y ms-3">
                  <svg class="text-muted" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                  </svg>
                </div>
                <input 
                  v-model="searchTerm"
                  type="text" 
                  id="search"
                  class="form-control ps-5"
                  placeholder="Rechercher par nom, prénom ou email..."
                >
              </div>
            </div>
            <div class="col-md-4">
              <select 
                v-model="statusFilter"
                class="form-select"
              >
                <option value="">Tous les statuts</option>
                <option value="actif">Actifs seulement</option>
                <option value="inactif">Inactifs seulement</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="card border-0 shadow-sm">
        <div class="table-responsive">
          <table class="table table-hover mb-0">
            <thead class="table-light">
              <tr>
                <th class="border-0 text-uppercase small fw-medium text-muted">
                  Collaborateur
                </th>
                <th class="border-0 text-uppercase small fw-medium text-muted">
                  Contact
                </th>
                <th class="border-0 text-uppercase small fw-medium text-muted">
                  Poste
                </th>
                <th class="border-0 text-uppercase small fw-medium text-muted">
                  Statut
                </th>
                <th class="border-0 text-uppercase small fw-medium text-muted text-end">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody>
              <tr 
                v-for="collab in filteredCollaborateurs" 
                :key="collab.id"
              >
                <td class="py-3">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                      <div class="rounded-circle bg-gradient d-flex align-items-center justify-content-center" 
                           style="width: 40px; height: 40px; background: linear-gradient(135deg, #007bff, #6f42c1);">
                        <span class="text-white small fw-medium">
                          {{ getInitials(collab.nom, collab.prenom) }}
                        </span>
                      </div>
                    </div>
                    <div class="ms-3">
                      <div class="fw-medium text-dark">
                        {{ collab.prenom }} {{ collab.nom }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="py-3">
                  <div class="text-dark">{{ collab.email }}</div>
                </td>
                <td class="py-3">
                  <div class="text-dark">{{ collab.poste }}</div>
                </td>
                <td class="py-3">
                  <span 
                    :class="collab.actif 
                      ? 'badge bg-success bg-opacity-10 text-success'
                      : 'badge bg-danger bg-opacity-10 text-danger'"
                  >
                    <i 
                      :class="collab.actif ? 'bi bi-circle-fill text-success' : 'bi bi-circle-fill text-danger'"
                      style="font-size: 0.5rem;"
                    ></i>
                    {{ collab.actif ? 'Actif' : 'Inactif' }}
                  </span>
                </td>
                <td class="py-3 text-end">
                  <div class="d-flex justify-content-end gap-2">
                    <button 
                      @click="goToEdit(collab.id)" 
                      class="btn btn-sm btn-outline-primary d-flex align-items-center"
                      type="button"
                    >
                      <svg class="me-1" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                      Modifier
                    </button>
                    <button 
                      @click="deleteCollaborateur(collab.id)" 
                      class="btn btn-sm btn-outline-danger d-flex align-items-center"
                      type="button"
                    >
                      <svg class="me-1" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                      Supprimer
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          
          <!-- Empty State -->
          <div v-if="filteredCollaborateurs.length === 0" class="text-center py-5">
            <svg class="mx-auto mb-3 text-muted" width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h5 class="text-dark mb-2">Aucun collaborateur</h5>
            <p class="text-muted mb-4">
              {{ searchTerm || statusFilter ? 'Aucun collaborateur ne correspond à vos critères de recherche.' : 'Commencez par ajouter un nouveau collaborateur.' }}
            </p>
            <button 
              @click="goToNew" 
              class="btn btn-primary d-flex align-items-center mx-auto"
              type="button"
            >
              <svg class="me-2" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              Ajouter un collaborateur
            </button>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
          <div class="d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary me-3" role="status" aria-label="Chargement...">
              <span class="visually-hidden">Chargement...</span>
            </div>
            <span class="text-muted">Chargement des collaborateurs...</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Toast Notifications -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1050;">
      <div 
        v-if="notification.show"
        :class="[
          'toast show',
          notification.type === 'success' ? 'border-success' : 'border-danger'
        ]"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
      >
        <div class="toast-header">
          <div 
            :class="[
              'rounded me-2',
              notification.type === 'success' ? 'bg-success' : 'bg-danger'
            ]"
            style="width: 20px; height: 20px;"
          ></div>
          <strong class="me-auto">{{ notification.title }}</strong>
          <button 
            type="button" 
            class="btn-close" 
            @click="hideNotification"
            aria-label="Fermer"
          ></button>
        </div>
        <div class="toast-body">
          {{ notification.message }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { api } from '../api'

const router = useRouter()
const collaborateurs = ref([])
const loading = ref(false)
const searchTerm = ref('')
const statusFilter = ref('')

// Notification system
const notification = ref({
  show: false,
  type: 'success', // 'success' or 'error'
  title: '',
  message: ''
})

// Computed properties
const collaborateursActifs = computed(() => {
  return collaborateurs.value.filter(c => c.actif).length
})

const collaborateursInactifs = computed(() => {
  return collaborateurs.value.filter(c => !c.actif).length
})

const filteredCollaborateurs = computed(() => {
  let filtered = collaborateurs.value

  // Filter by search term
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase()
    filtered = filtered.filter(collab => 
      collab.nom.toLowerCase().includes(term) ||
      collab.prenom.toLowerCase().includes(term) ||
      collab.email.toLowerCase().includes(term)
    )
  }

  // Filter by status
  if (statusFilter.value === 'actif') {
    filtered = filtered.filter(collab => collab.actif)
  } else if (statusFilter.value === 'inactif') {
    filtered = filtered.filter(collab => !collab.actif)
  }

  return filtered
})

// Methods
const fetchCollaborateurs = async () => {
  loading.value = true
  try {
    const res = await api.get('/api/collaborateurs')
    collaborateurs.value = res.data
  } catch (error) {
    console.error('Erreur en récupérant les collaborateurs:', error)
    showNotification('error', 'Erreur', 'Impossible de charger les collaborateurs')
  } finally {
    loading.value = false
  }
}

const goToNew = () => {
  router.push({ name: 'collaborateurs-new' })
}

const goToEdit = (id) => {
  router.push({ name: 'collaborateurs-edit', params: { id } })
}

const deleteCollaborateur = async (id) => {
  const collab = collaborateurs.value.find(c => c.id === id)
  const confirmMessage = `Êtes-vous sûr de vouloir supprimer ${collab?.prenom} ${collab?.nom} ?`
  
  if (confirm(confirmMessage)) {
    try {
      await api.delete(`/api/collaborateurs/${id}`)
      showNotification('success', 'Suppression réussie', 'Le collaborateur a été supprimé avec succès')
      await fetchCollaborateurs()
    } catch (error) {
      console.error('Erreur lors de la suppression:', error)
      showNotification('error', 'Erreur de suppression', 'Impossible de supprimer le collaborateur')
    }
  }
}

const getInitials = (nom, prenom) => {
  return `${prenom?.charAt(0) || ''}${nom?.charAt(0) || ''}`.toUpperCase()
}

const showNotification = (type, title, message) => {
  notification.value = {
    show: true,
    type,
    title,
    message
  }
  
  // Auto-hide after 5 seconds
  setTimeout(() => {
    hideNotification()
  }, 5000)
}

const hideNotification = () => {
  notification.value.show = false
}

// Lifecycle
onMounted(() => {
  fetchCollaborateurs()
})
</script>

<style scoped>
.bg-gradient {
  background: linear-gradient(135deg, #007bff, #6f42c1) !important;
}

.table-hover tbody tr:hover {
  background-color: rgba(0, 0, 0, 0.025);
}

.badge {
  font-size: 0.75rem;
  padding: 0.5rem 0.75rem;
  border-radius: 0.5rem;
}

.toast {
  min-width: 300px;
}

.card {
  transition: all 0.2s ease-in-out;
}

.card:hover {
  transform: translateY(-2px);
}

.btn {
  transition: all 0.2s ease-in-out;
}

.btn:hover {
  transform: translateY(-1px);
}

.spinner-border {
  width: 2rem;
  height: 2rem;
}
</style>
