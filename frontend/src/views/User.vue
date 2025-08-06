<template>
  <div class="admin-dashboard">
    <div class="header">
      <h2>Mes Tâches Assignées</h2>
      <div class="header-actions">
        <button @click="fetchMesTaches" class="btn btn-refresh">
          🔄 Actualiser
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
            <span class="label">Projet :</span>
            <span class="value">{{ tache.projet?.nom || '—' }}</span>
          </div>
          <div class="info-row">
            <span class="label">Description :</span>
            <span class="value">{{ tache.description }}</span>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="empty-state">
      <div class="empty-icon">🧘</div>
      <h3>Vous n'avez aucune tâche</h3>
      <p>Revenez plus tard, une tâche vous sera peut-être assignée.</p>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'
import { jwtDecode } from 'jwt-decode'

const taches = ref([])

const fetchMesTaches = async () => {
  try {
    const token = localStorage.getItem('token')
    const decoded = jwtDecode(token)
    const userId = decoded.id // ou decoded.sub selon ton backend

    const res = await axios.get(`http://127.0.0.1:8000/api/taches/collaborateur/${userId}`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })

    taches.value = res.data.data
  } catch (e) {
    console.error("Erreur chargement des tâches personnelles :", e)
    alert("Impossible de charger vos tâches")
  }
}

onMounted(() => {
  fetchMesTaches()
})
</script>
