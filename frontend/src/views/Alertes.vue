<template>
  <div class="admin-dashboard">
    <div class="header">
      <h2>Alertes</h2>
      <div class="header-actions">
        <button @click="fetchAlertes" class="btn btn-refresh">
          🔄 Actualiser
        </button>
      </div>
    </div>

    <div v-if="alertes.length" class="alertes-list">
      <div 
        v-for="alerte in alertes" 
        :key="alerte.id" 
        class="alerte-card"
        :class="{ critique: alerte.critique }"
      >
        <h3>{{ alerte.titre }}</h3>
        <p>{{ alerte.message }}</p>
        <p class="alerte-date">Créée le {{ formatDate(alerte.createdAt) }}</p>

        <div class="card-actions">
          <button @click="supprimerAlerte(alerte.id)" class="btn btn-danger btn-small">
            🗑️ Supprimer
          </button>
        </div>
      </div>
    </div>

    <div v-else class="empty-state">
      <div class="empty-icon">✅</div>
      <h3>Aucune alerte active</h3>
      <p>Tout est sous contrôle !</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const alertes = ref([])

const fetchAlertes = async () => {
  try {
    const token = localStorage.getItem('token')
    const res = await axios.get('http://127.0.0.1:8000/api/alertes', {
      headers: { Authorization: `Bearer ${token}` }
    })
    alertes.value = res.data.data
  } catch (error) {
    console.error("Erreur chargement alertes :", error)
    alert("Impossible de charger les alertes")
  }
}

const supprimerAlerte = async (id) => {
  if (!confirm("Supprimer cette alerte ?")) return

  try {
    const token = localStorage.getItem('token')
    await axios.delete(`http://127.0.0.1:8000/api/alertes/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    alert("Alerte supprimée avec succès")
    fetchAlertes()
  } catch (e) {
    console.error("Erreur suppression :", e)
    alert("Erreur lors de la suppression de l'alerte")
  }
}

const formatDate = (iso) => {
  return new Date(iso).toLocaleString('fr-FR')
}

onMounted(() => {
  fetchAlertes()
})
</script>

<style scoped>
.alertes-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
  gap: 1.5rem;
}

.alerte-card {
  background: white;
  border-left: 6px solid #facc15;
  padding: 1.5rem;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.alerte-card.critique {
  border-left-color: #ef4444;
  background: #fef2f2;
}

.alerte-card h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1.1rem;
  color: #dc2626;
}

.alerte-card p {
  margin: 0.25rem 0;
  color: #374151;
}

.alerte-date {
  font-size: 0.8rem;
  color: #6b7280;
}

.card-actions {
  margin-top: 1rem;
  display: flex;
  justify-content: flex-end;
}
</style>
