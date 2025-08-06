<template>
  <div class="page-projets">
    <h1>Liste des projets</h1>

    <button @click="ouvrirModalAjout" class="btn btn-primary">
      ➕ Ajouter un projet
    </button>

    <ul v-if="projets.length" class="projets-list">
      <li v-for="projet in projets" :key="projet.id" class="projet-item">
        <strong>{{ projet.titre }}</strong> – {{ projet.description }}
        <br/>
        Responsable : {{ projet.responsable?.prenom }} {{ projet.responsable?.nom }}
        <br/>
        <button @click="ouvrirModalModification(projet)" class="btn btn-secondary btn-small">
          ✏️ Modifier
        </button>
        <button @click="supprimerProjet(projet.id)" class="btn btn-danger btn-small">
          🗑️ Supprimer
        </button>

        <div v-if="projet.taches?.length" class="taches-list">
          <h4>Tâches :</h4>
          <ul>
            <li v-for="tache in projet.taches" :key="tache.id">
              📌 {{ tache.titre }} – {{ tache.statut }} (priorité {{ tache.priorite }})
            </li>
          </ul>
        </div>
        <p v-else class="no-taches">Aucune tâche pour ce projet.</p>
      </li>
    </ul>
    <p v-else class="no-projets">Aucun projet pour l’instant.</p>

    <!-- Modal ajout / modification -->
    <div v-if="showModal" class="modal-overlay" @click="fermerModal">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h3>{{ modeEdition ? 'Modifier un projet' : 'Ajouter un projet' }}</h3>
          <button @click="fermerModal" class="btn-close">✕</button>
        </div>

        <form @submit.prevent="sauvegarderProjet" class="modal-body">
          <div class="form-group">
            <label for="titre">Titre *</label>
            <input id="titre" type="text" v-model="formData.titre" required class="form-input"/>
          </div>

          <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" v-model="formData.description" class="form-input"></textarea>
          </div>

          <div class="form-group">
            <label for="responsable">Responsable *</label>
            <select id="responsable" v-model="formData.responsable" required class="form-input">
              <option disabled value="">-- Sélectionner --</option>
              <option v-for="c in collaborateurs" :key="c.id" :value="c.id">
                {{ c.prenom }} {{ c.nom }}
              </option>
            </select>
          </div>

          <div class="form-group">
            <label for="date_debut">Date de début *</label>
            <input id="date_debut" type="date" v-model="formData.date_debut" required class="form-input"/>
          </div>

          <div class="form-group">
            <label for="date_fin_prevue">Date de fin prévue</label>
            <input id="date_fin_prevue" type="date" v-model="formData.date_fin_prevue" class="form-input"/>
          </div>

          <div class="form-group">
            <label for="priorite">Priorité *</label>
            <input id="priorite" type="number" min="1" v-model.number="formData.priorite" required class="form-input"/>
          </div>

          <div class="form-group">
            <label for="statut">Statut *</label>
            <select id="statut" v-model="formData.statut" required class="form-input">
              <option value="en_attente">En attente</option>
              <option value="en_cours">En cours</option>
              <option value="terminee">Terminée</option>
            </select>
          </div>

          <div class="modal-actions">
            <button type="button" @click="fermerModal" class="btn btn-secondary">
              Annuler
            </button>
            <button type="submit" class="btn btn-primary">
              {{ modeEdition ? 'Mettre à jour' : 'Ajouter' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const projets = ref([])
const collaborateurs = ref([])
const showModal = ref(false)
const modeEdition = ref(false)
const projetEnEdition = ref(null)

const formData = ref({
  titre: '',
  description: '',
  date_debut: '',
  date_fin_prevue: '',
  responsable: '',
  priorite: 1,
  statut: 'en_attente'
})

const fetchProjets = async () => {
  try {
    const token = localStorage.getItem('token')
    const res = await axios.get('http://127.0.0.1:8000/api/missions', {
      headers: { Authorization: `Bearer ${token}` }
    })
    const list = Array.isArray(res.data.data) ? res.data.data : res.data
    projets.value = list

    // charger les taches pour chaque projet
    await Promise.all(list.map(async p => {
      const tRes = await axios.get(`http://127.0.0.1:8000/api/taches/projet/${p.id}`, {
        headers: { Authorization: `Bearer ${token}` }
      })
      p.taches = tRes.data
    }))
  } catch (e) {
    console.error('Erreur chargement projets:', e)
    alert('Erreur lors du chargement des projets')
  }
}

const fetchCollaborateurs = async () => {
  try {
    const token = localStorage.getItem('token')
    const res = await axios.get('http://127.0.0.1:8000/api/collaborateurs', {
      headers: { Authorization: `Bearer ${token}` }
    })
    collaborateurs.value = res.data.data || res.data
  } catch (e) {
    console.error('Erreur chargement collaborateurs:', e)
  }
}

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
    const token = localStorage.getItem('token')
    const payload = {
      titre: formData.value.titre,
      description: formData.value.description,
      dateDebut: formData.value.date_debut,
      dateFinPrevue: formData.value.date_fin_prevue,
      responsable: formData.value.responsable,
      priorite: formData.value.priorite,
      statut: formData.value.statut
    }
    const config = { headers: { Authorization: `Bearer ${token}`, 'Content-Type':'application/json' }}

    // debug payload
    console.log('🚀 Payload Mission:', payload)

    if (modeEdition.value) {
      await axios.put(`http://127.0.0.1:8000/api/missions/${projetEnEdition.value.id}`, payload, config)
      alert('Projet mis à jour !')
    } else {
      await axios.post('http://127.0.0.1:8000/api/missions', payload, config)
      alert('Projet ajouté !')
    }

    fermerModal()
    fetchProjets()
  } catch (error) {
    console.error('Erreur sauvegarde projet :', error)
    alert('Erreur lors de la sauvegarde du projet')
  }
}

const supprimerProjet = async (id) => {
  if (!confirm('Supprimer ce projet ?')) return
  try {
    const token = localStorage.getItem('token')
    await axios.delete(`http://127.0.0.1:8000/api/missions/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    alert('Projet supprimé !')
    fetchProjets()
  } catch (error) {
    console.error('Erreur suppression projet :', error)
    alert('Erreur lors de la suppression du projet')
  }
}

onMounted(() => {
  fetchCollaborateurs()
  fetchProjets()
})
</script>

<style scoped>
.page-projets { padding: 1rem; }
.projets-list { list-style: none; padding: 0; }
.projet-item { margin-bottom: 1.5rem; border-bottom: 1px solid #ddd; padding-bottom: 1rem; }
.taches-list { margin-left: 1rem; }
.modal-overlay {
  position: fixed; top:0; left:0; width:100%; height:100%;
  background:rgba(0,0,0,0.4); display:flex; align-items:center; justify-content:center;
}
.modal { background:white; padding:1.5rem; border-radius:6px; width:400px; max-width:90%; }
.modal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; }
.form-group { margin-bottom:1rem; }
.form-input { width:100%; padding:0.5rem; border:1px solid #ccc; border-radius:4px; }
.modal-actions { display:flex; justify-content:flex-end; gap:0.5rem; }
.btn { cursor:pointer; }
.btn-primary { background:#3b82f6; color:white; padding:0.5rem 1rem; border:none; }
.btn-secondary { background:#6b7280; color:white; padding:0.5rem 1rem; border:none; }
.btn-danger { background:#ef4444; color:white; padding:0.5rem 1rem; border:none; }
.btn-small { font-size:0.85rem; padding:0.25rem 0.5rem; }
.btn-close { background:transparent; border:none; font-size:1.25rem; }
</style>
