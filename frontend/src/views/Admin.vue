<template>
  <div class="admin-dashboard">
    <div class="header">
      <h2>Gestion des Collaborateurs</h2>
      <div class="header-actions">
        <button @click="fetchCollaborateurs" class="btn btn-refresh">
          <i class="icon">🔄</i>
          Actualiser
        </button>
        <button @click="ouvrirModalAjout" class="btn btn-primary">
          <i class="icon">➕</i>
          Ajouter un collaborateur
        </button>
      </div>
    </div>

    <div class="collaborateurs-grid" v-if="collaborateurs.length">
      <div v-for="collab in collaborateurs" :key="collab.id" class="collaborateur-card">
        <div class="card-header">
          <h3>{{ collab.prenom }} {{ collab.nom }}</h3>
          <div class="status-badge" :class="{ 'disponible': collab.disponible, 'indisponible': !collab.disponible }">
            {{ collab.disponible ? 'Disponible' : 'Indisponible' }}
          </div>
        </div>
        
        <div class="card-body">
          <div class="info-row">
            <span class="label">Email:</span>
            <span class="value">{{ collab.email }}</span>
          </div>
          <div class="info-row">
            <span class="label">Rôle:</span>
            <span class="value">{{ collab.role }}</span>
          </div>
          <div class="info-row">
            <span class="label">Charge actuelle:</span>
            <span class="value">{{ collab.charge_actuelle }}%</span>
          </div>
        </div>

        <div class="card-actions">
          <button @click="ouvrirModalModification(collab)" class="btn btn-secondary btn-small">
            <i class="icon">✏️</i>
            Modifier
          </button>
          <button @click="supprimerCollaborateur(collab.id)" class="btn btn-danger btn-small">
            <i class="icon">🗑️</i>
            Supprimer
          </button>
        </div>
      </div>
    </div>

    <div v-else class="empty-state">
      <div class="empty-icon">👥</div>
      <h3>Aucun collaborateur trouvé</h3>
      <p>Commencez par ajouter votre premier collaborateur</p>
      <button @click="ouvrirModalAjout" class="btn btn-primary">
        <i class="icon">➕</i>
        Ajouter un collaborateur
      </button>
    </div>

    <!-- Modal pour ajouter/modifier un collaborateur -->
    <div v-if="showModal" class="modal-overlay" @click="fermerModal">
      <div class="modal" @click.stop>
        <div class="modal-header">
          <h3>{{ modeEdition ? 'Modifier' : 'Ajouter' }} un collaborateur</h3>
          <button @click="fermerModal" class="btn-close">✕</button>
        </div>
        
        <form @submit.prevent="sauvegarderCollaborateur" class="modal-body">
          <div class="form-group">
            <label for="prenom">Prénom *</label>
            <input 
              type="text" 
              id="prenom" 
              v-model="formData.prenom" 
              required 
              class="form-input"
            >
          </div>
          
          <div class="form-group">
            <label for="nom">Nom *</label>
            <input 
              type="text" 
              id="nom" 
              v-model="formData.nom" 
              required 
              class="form-input"
            >
          </div>
          
          <div class="form-group">
            <label for="email">Email *</label>
            <input 
              type="email" 
              id="email" 
              v-model="formData.email" 
              required 
              class="form-input"
            >
          </div>
          
          <div class="form-group">
            <label for="role">Rôle *</label>
            <select id="role" v-model="formData.role" required class="form-input">
              <option value="">Sélectionner un rôle</option>
              <option value="admin">Administrateur</option>
              <option value="manager">Manager</option>
              <option value="developer">Développeur</option>
              <option value="designer">Designer</option>
              <option value="analyst">Analyste</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="disponible">Disponibilité</label>
            <div class="checkbox-wrapper">
              <input 
                type="checkbox" 
                id="disponible" 
                v-model="formData.disponible"
                class="form-checkbox"
              >
              <label for="disponible" class="checkbox-label">Disponible</label>
            </div>
          </div>
          
          <div class="form-group">
            <label for="charge">Charge actuelle (%)</label>
            <input 
              type="number" 
              id="charge" 
              v-model="formData.charge_actuelle" 
              min="0" 
              max="100"
              class="form-input"
            >
          </div>
          
          <div class="modal-actions">
            <button type="button" @click="fermerModal" class="btn btn-secondary">
              Annuler
            </button>
            <button type="submit" class="btn btn-primary">
              {{ modeEdition ? 'Modifier' : 'Ajouter' }}
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

const collaborateurs = ref([])
const showModal = ref(false)
const modeEdition = ref(false)
const collaborateurEnEdition = ref(null)

const formData = ref({
  prenom: '',
  nom: '',
  email: '',
  role: '',
  disponible: true,
  charge_actuelle: 0
})

const fetchCollaborateurs = async () => {
  try {
    const token = localStorage.getItem('token')
    const response = await axios.get('http://127.0.0.1:8000/api/collaborateurs', {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })
    collaborateurs.value = response.data.data
  } catch (error) {
    console.error("Erreur lors du chargement des collaborateurs :", error)
    alert("Erreur lors du chargement des collaborateurs")
  }
}

const ouvrirModalAjout = () => {
  modeEdition.value = false
  collaborateurEnEdition.value = null
  resetFormData()
  showModal.value = true
}

const ouvrirModalModification = (collaborateur) => {
  modeEdition.value = true
  collaborateurEnEdition.value = collaborateur
  formData.value = {
    prenom: collaborateur.prenom,
    nom: collaborateur.nom,
    email: collaborateur.email,
    role: collaborateur.role,
    disponible: collaborateur.disponible,
    charge_actuelle: collaborateur.charge_actuelle
  }
  showModal.value = true
}

const fermerModal = () => {
  showModal.value = false
  resetFormData()
}

const resetFormData = () => {
  formData.value = {
    prenom: '',
    nom: '',
    email: '',
    role: '',
    disponible: true,
    charge_actuelle: 0
  }
}

const sauvegarderCollaborateur = async () => {
  try {
    const token = localStorage.getItem('token')
    const config = {
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    }

    if (modeEdition.value) {
      // Modification
      await axios.put(
        `http://127.0.0.1:8000/api/collaborateurs/${collaborateurEnEdition.value.id}`,
        formData.value,
        config
      )
      alert("Collaborateur modifié avec succès !")
    } else {
      // Ajout
      await axios.post(
        'http://127.0.0.1:8000/api/collaborateurs',
        formData.value,
        config
      )
      alert("Collaborateur ajouté avec succès !")
    }

    fermerModal()
    fetchCollaborateurs()
  } catch (error) {
    console.error("Erreur lors de la sauvegarde :", error)
    alert("Erreur lors de la sauvegarde du collaborateur")
  }
}

const supprimerCollaborateur = async (id) => {
  if (!confirm("Êtes-vous sûr de vouloir supprimer ce collaborateur ?")) {
    return
  }

  try {
    const token = localStorage.getItem('token')
    await axios.delete(`http://127.0.0.1:8000/api/collaborateurs/${id}`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })
    alert("Collaborateur supprimé avec succès !")
    fetchCollaborateurs()
  } catch (error) {
    console.error("Erreur lors de la suppression :", error)
    alert("Erreur lors de la suppression du collaborateur")
  }
}

// Appel automatique au chargement du composant
onMounted(() => {
  fetchCollaborateurs()
})
</script>

<style scoped>
.admin-dashboard {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #e1e5e9;
}

.header h2 {
  color: #2c3e50;
  margin: 0;
  font-size: 2rem;
  font-weight: 600;
}

.header-actions {
  display: flex;
  gap: 1rem;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background: #5a6268;
  transform: translateY(-2px);
}

.btn-danger {
  background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
  color: white;
}

.btn-danger:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
}

.btn-refresh {
  background: #17a2b8;
  color: white;
}

.btn-refresh:hover {
  background: #138496;
  transform: translateY(-2px);
}

.btn-small {
  padding: 0.5rem 1rem;
  font-size: 0.8rem;
}

.icon {
  font-size: 1rem;
}

.collaborateurs-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.collaborateur-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.collaborateur-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.card-header {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  padding: 1.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card-header h3 {
  margin: 0;
  color: #2c3e50;
  font-size: 1.2rem;
  font-weight: 600;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 500;
}

.status-badge.disponible {
  background: #d4edda;
  color: #155724;
}

.status-badge.indisponible {
  background: #f8d7da;
  color: #721c24;
}

.card-body {
  padding: 1.5rem;
}

.info-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.75rem;
}

.label {
  font-weight: 500;
  color: #6c757d;
}

.value {
  color: #2c3e50;
  font-weight: 500;
}

.card-actions {
  padding: 1rem 1.5rem;
  background: #f8f9fa;
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
}

.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  color: #6c757d;
}

.empty-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

.empty-state h3 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  backdrop-filter: blur(5px);
}

.modal {
  background: white;
  border-radius: 12px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
  animation: modalAppear 0.3s ease-out;
}

@keyframes modalAppear {
  from {
    opacity: 0;
    transform: scale(0.9) translateY(-20px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #e9ecef;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.modal-header h3 {
  margin: 0;
  color: #2c3e50;
  font-size: 1.3rem;
  font-weight: 600;
}

.btn-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #6c757d;
  padding: 0.5rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 2rem;
  height: 2rem;
  transition: all 0.3s ease;
}

.btn-close:hover {
  background: #f8f9fa;
  color: #2c3e50;
}

.modal-body {
  padding: 2rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #2c3e50;
}

.form-input {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e9ecef;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
  box-sizing: border-box;
}

.form-input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-input:invalid {
  border-color: #dc3545;
}

.checkbox-wrapper {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.form-checkbox {
  width: 1.2rem;
  height: 1.2rem;
  cursor: pointer;
}

.checkbox-label {
  cursor: pointer;
  margin-bottom: 0 !important;
}

.modal-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  margin-top: 2rem;
  padding-top: 1rem;
  border-top: 1px solid #e9ecef;
}

/* Responsive Design */
@media (max-width: 768px) {
  .admin-dashboard {
    padding: 1rem;
  }
  
  .header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .header-actions {
    justify-content: center;
  }
  
  .collaborateurs-grid {
    grid-template-columns: 1fr;
  }
  
  .modal {
    width: 95%;
    margin: 1rem;
  }
  
  .modal-body {
    padding: 1.5rem;
  }
  
  .modal-actions {
    flex-direction: column;
  }
}

@media (max-width: 480px) {
  .header h2 {
    font-size: 1.5rem;
  }
  
  .btn {
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
  }
  
  .card-header {
    flex-direction: column;
    gap: 0.5rem;
    text-align: center;
  }
  
  .card-actions {
    flex-direction: column;
    gap: 0.5rem;
  }
}
</style>