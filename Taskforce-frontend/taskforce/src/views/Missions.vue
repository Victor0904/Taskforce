<template>
  <div class="missions">
    <h1>Liste des Missions</h1>

    <router-link to="/mission/new" class="btn-create">Créer une nouvelle mission</router-link>

    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Titre</th>
          <th>Description</th>
          <th>Date début</th>
          <th>Date fin</th>
          <th>Nom</th>
          <th>Statut</th>
          <th>Compétences requises</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="mission in missions" :key="mission.id">
          <td>{{ mission.id }}</td>
          <td>{{ mission.titre }}</td>
          <td>{{ mission.description }}</td>
          <td>{{ formatDate(mission.date_debut) }}</td>
          <td>{{ formatDate(mission.date_fin) }}</td>
          <td>{{ mission.nom }}</td>
          <td>{{ mission.statut }}</td>
          <td>
            <ul>
              <li v-for="competence in mission.competences" :key="competence.id">
                {{ competence.nom }}
              </li>
            </ul>
          </td>
          <td>
            <router-link :to="`/mission/${mission.id}`">Voir</router-link> |
            <router-link :to="`/mission/${mission.id}/edit`">Modifier</router-link> |
            <button @click="confirmDelete(mission.id)">Supprimer</button>
          </td>
        </tr>
        <tr v-if="missions.length === 0">
          <td colspan="9">Aucune mission trouvée</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

const missions = ref([])
const router = useRouter()

function formatDate(dateStr) {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  return d.toISOString().slice(0, 10) // format YYYY-MM-DD
}

async function fetchMissions() {
  try {
    const res = await axios.get('/api/missions')
    missions.value = res.data
  } catch (error) {
    console.error('Erreur chargement missions:', error)
  }
}

function confirmDelete(id) {
  if (confirm('Confirmer la suppression de cette mission ?')) {
    deleteMission(id)
  }
}

async function deleteMission(id) {
  try {
    await axios.delete(`/api/missions/${id}`)
    missions.value = missions.value.filter(m => m.id !== id)
  } catch (error) {
    alert('Erreur lors de la suppression')
    console.error(error)
  }
}

onMounted(() => {
  fetchMissions()
})
</script>
