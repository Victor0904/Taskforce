<template>
  <div class="admin-dashboard">
    <div class="header">
      <h2>Tâches du projet : {{ projet.titre }}</h2>
      <div class="header-actions">
        <button @click="fetchTaches" class="btn btn-refresh">
          🔄 Actualiser
        </button>
        <button @click="ouvrirModalAjout" class="btn btn-primary" v-if="hasRoleAdmin">
          ➕ Ajouter une tâche
        </button>
      </div>
    </div>

    <div class="collaborateurs-grid" v-if="taches.length">
      <div v-for="tache in taches" :key="tache.id" class="collaborateur-card">
        <div class="card-header">
          <h3>{{ tache.titre }}</h3>
          <div class="status-badge">{{ tache.priorite }}</div>
        </div>
        <div class="card-body">
          <div class="info-row">
            <span class="label">Description :</span>
            <span class="value">{{ tache.description }}</span>
          </div>
          <div class="info-row">
            <span class="label">Assigné à :</span>
            <span class="value">
              {{ tache.collaborateur?.prenom || 'Non assigné' }}
            </span>
          </div>
        </div>
        <div class="card-actions" v-if="hasRoleAdmin">
          <button @click="ouvrirModalModification(tache)" class="btn btn-secondary btn-small">
            ✏️ Modifier
          </button>
          <button @click="supprimerTache(tache.id)" class="btn btn-danger btn-small">
            🗑️ Supprimer
          </button>
        </div>
      </div>
    </div>

    <div v-else class="empty-state">
      <div class="empty-icon">📄</div>
      <h3>Aucune tâche trouvée</h3>
      <p>Ajoutez une tâche pour commencer</p>
    </div>

    <!-- TODO : Ajouter modale plus tard -->
  </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { jwtDecode } from 'jwt-decode'

const route = useRoute()
const router = useRouter()

const projetId = ref(route.params.id || null)

const taches = ref([])
const projet = ref({ titre: '' })

const hasRoleAdmin = computed(() => {
  const token = localStorage.getItem('token')
  if (!token) return false
  try {
    const decoded = jwtDecode(token)
    return decoded.roles?.includes('ROLE_ADMIN')
  } catch (e) {
    return false
  }
})

const fetchTaches = async () => {
  if (!projetId.value) return
  try {
    const token = localStorage.getItem('token')
    const config = {
      headers: {
        Authorization: `Bearer ${token}`
      }
    }
    const res = await axios.get(`http://127.0.0.1:8000/api/taches/projet/${projetId.value}`, config)
    taches.value = Array.isArray(res.data.data) ? res.data.data : res.data
  } catch (e) {
    alert("Erreur lors du chargement des tâches")
  }
}

const fetchProjet = async () => {
  if (!projetId.value) {
    alert("Projet introuvable. Redirection...")
    router.push('/projets') // retour à la liste
    return
  }

  try {
    const token = localStorage.getItem('token')
    const res = await axios.get(`http://127.0.0.1:8000/api/missions/${projetId.value}`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    projet.value = res.data
  } catch (e) {
    alert("Projet introuvable")
  }
}

onMounted(() => {
  fetchProjet()
  fetchTaches()
})

// ⚠️ Ces méthodes doivent être implémentées si tu veux la création/modification
const ouvrirModalAjout = () => {}
const ouvrirModalModification = (tache) => {}
const supprimerTache = (id) => {}
</script>
