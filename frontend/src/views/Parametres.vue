<template>
  <div class="container">
    <div class="admin-header">
      <h1 class="admin-title">⚙️ Paramètres du Compte</h1>
      <p class="admin-subtitle">Gérez vos informations personnelles et votre mot de passe</p>
    </div>

    <div class="settings-grid">
      <!-- Informations du compte -->
      <div class="settings-card">
        <div class="card-header">
          <h2 class="card-title">📧 Informations du Compte</h2>
        </div>
        <div class="card-body">
          <div class="info-group">
            <label class="info-label">Adresse email :</label>
            <div class="info-value">{{ userInfo.email }}</div>
          </div>
     
          <div class="info-group">
            <label class="info-label">Rôle :</label>
            <div class="info-value">{{ userInfo.role }}</div>
          </div>
        </div>
      </div>

      <!-- Changement de mot de passe -->
      <div class="settings-card">
        <div class="card-header">
          <h2 class="card-title">🔒 Changer le Mot de Passe</h2>
        </div>
        <div class="card-body">
          <form @submit.prevent="changePassword" class="password-form">
            <div class="form-group">
              <label class="form-label">Mot de passe actuel :</label>
              <input 
                v-model="passwordForm.currentPassword" 
                type="password" 
                class="form-input" 
                required
                placeholder="Votre mot de passe actuel"
              />
            </div>
            
            <div class="form-group">
              <label class="form-label">Nouveau mot de passe :</label>
              <input 
                v-model="passwordForm.newPassword" 
                type="password" 
                class="form-input" 
                required 
                minlength="8"
                placeholder="Minimum 8 caractères"
              />
            </div>
            
            <div class="form-group">
              <label class="form-label">Confirmer le nouveau mot de passe :</label>
              <input 
                v-model="passwordForm.confirmPassword" 
                type="password" 
                class="form-input" 
                required 
                placeholder="Retapez le nouveau mot de passe"
              />
            </div>
            
            <div v-if="passwordError" class="alert alert-error">
              {{ passwordError }}
            </div>
            
            <div v-if="passwordSuccess" class="alert alert-success">
              {{ passwordSuccess }}
            </div>
            
            <div class="button-group">
              <button 
                type="submit" 
                class="btn btn-primary"
                :disabled="!canSubmitPassword || loading"
              >
                <span v-if="loading">⏳ Changement en cours...</span>
                <span v-else>Changer le mot de passe</span>
              </button>
              
              <button 
                type="button" 
                class="btn btn-secondary"
                @click="resetPasswordForm"
                :disabled="loading"
              >
                🔄 Réinitialiser
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Informations de sécurité -->
      <div class="settings-card">
        <div class="card-header">
          <h2 class="card-title">🛡️ Informations de Sécurité</h2>
        </div>
        <div class="card-body">
          <div class="info-group">
            <label class="info-label">Compte créé le :</label>
            <div class="info-value">{{ formatDate(userInfo.createdAt) }}</div>
          </div>
          <div class="info-group">
            <label class="info-label">Statut du compte :</label>
            <div class="info-value">
              <span :class="userInfo.isActive ? 'status-active' : 'status-inactive'">
                {{ userInfo.isActive ? '✅ Actif' : '❌ Inactif' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { jwtDecode } from 'jwt-decode'

const router = useRouter()
const loading = ref(false)
const passwordError = ref('')
const passwordSuccess = ref('')

const userInfo = ref({
  email: '',
  nomComplet: '',
  role: '',
  createdAt: null,
  isActive: true
})

const passwordForm = ref({
  currentPassword: '',
  newPassword: '',
  confirmPassword: ''
})

const canSubmitPassword = computed(() => {
  return passwordForm.value.currentPassword.length > 0 &&
         passwordForm.value.newPassword.length >= 8 &&
         passwordForm.value.newPassword === passwordForm.value.confirmPassword &&
         !loading.value
})

const loadUserInfo = async () => {
  try {
    const token = localStorage.getItem('token')
    if (!token) {
      router.push('/login')
      return
    }

    // Décoder le token pour obtenir les informations de base
    const decoded = jwtDecode(token)
    userInfo.value.email = decoded.username || decoded.email || 'N/A'
    
    // ✅ Utiliser le rôle du token JWT en priorité
    const jwtRole = decoded.roles && decoded.roles.length > 0 ? decoded.roles[0] : null
    userInfo.value.role = formatRole(jwtRole)
    
    // Récupérer les informations complètes depuis l'API
    try {
      const response = await axios.get('http://127.0.0.1:8000/api/users/profile', {
        headers: { Authorization: `Bearer ${token}` }
      })
      
      if (response.data.data) {
        const data = response.data.data
        userInfo.value = {
          ...userInfo.value,
          nomComplet: data.nomComplet || 'N/A',
          // ✅ Garder le rôle du JWT qui est plus fiable
          role: formatRole(jwtRole || data.role),
          createdAt: data.createdAt ? new Date(data.createdAt) : null,
          isActive: data.isActive !== false
        }
      }
    } catch (apiError) {
      // Si l'API échoue, on garde les informations du token JWT
    }
  } catch (error) {
    // Erreur silencieuse
  }
}

const changePassword = async () => {
  if (!canSubmitPassword.value) {
    passwordError.value = 'Veuillez remplir tous les champs correctement'
    return
  }
  
  try {
    loading.value = true
    passwordError.value = ''
    passwordSuccess.value = ''
    
    const token = localStorage.getItem('token')
    const response = await axios.post('http://127.0.0.1:8000/api/users/change-password', {
      currentPassword: passwordForm.value.currentPassword,
      newPassword: passwordForm.value.newPassword
    }, {
      headers: { Authorization: `Bearer ${token}` }
    })
    
    
    
    // Réinitialiser le formulaire
    passwordForm.value = {
      currentPassword: '',
      newPassword: '',
      confirmPassword: ''
    }
    
    // Afficher le message de succès
    passwordSuccess.value = 'Mot de passe changé avec succès !'
    
  } catch (err) {
    passwordError.value = err.response?.data?.message || 'Erreur lors du changement de mot de passe'
  } finally {
    loading.value = false
  }
}

const formatRole = (role) => {
  if (!role) return 'Collaborateur'
  
  // ✅ Mapping complet des rôles JWT vers les rôles métier
  const roleMap = {
    'ROLE_MANAGER': 'Manager',
    'ROLE_CHEF_PROJET': 'Chef de projet',
    'ROLE_ADMIN': 'Administrateur',
    'ROLE_USER': 'Collaborateur',
    // Gestion des rôles métier directs (au cas où)
    'Manager': 'Manager',
    'Chef de projet': 'Chef de projet',
    'Administrateur': 'Administrateur',
    'Collaborateur': 'Collaborateur'
  }
  
  return roleMap[role] || 'Collaborateur'
}

const formatDate = (date) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const resetPasswordForm = () => {
  passwordForm.value = {
    currentPassword: '',
    newPassword: '',
    confirmPassword: ''
  }
  passwordError.value = ''
  passwordSuccess.value = ''
}

onMounted(() => {
  loadUserInfo()
})
</script>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.admin-header {
  text-align: center;
  margin-bottom: 3rem;
}

.admin-title {
  font-size: 2.5rem;
  color: var(--primary-600);
  margin-bottom: 0.5rem;
}

.admin-subtitle {
  font-size: 1.1rem;
  color: var(--gray-600);
  margin: 0;
}

.settings-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
  gap: 2rem;
}

.settings-card {
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  border: 1px solid var(--gray-200);
}

.card-header {
  background: var(--primary-500);
  color: white;
  padding: 1.5rem;
}

.card-title {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
}

.card-body {
  padding: 2rem;
}

.info-group {
  margin-bottom: 1.5rem;
}

.info-label {
  display: block;
  font-weight: 600;
  color: var(--gray-700);
  margin-bottom: 0.5rem;
}

.info-value {
  color: var(--gray-900);
  font-size: 1.1rem;
  padding: 0.5rem;
  background: var(--gray-50);
  border-radius: 6px;
  border: 1px solid var(--gray-200);
}

.status-active {
  color: var(--success-600);
  font-weight: 600;
}

.status-inactive {
  color: var(--error-600);
  font-weight: 600;
}

.password-form {
  margin-top: 1rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  font-weight: 600;
  color: var(--gray-700);
  margin-bottom: 0.5rem;
}

.form-input {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid var(--gray-300);
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.3s ease;
}

.form-input:focus {
  outline: none;
  border-color: var(--primary-500);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.alert {
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
}

.alert-error {
  background: var(--error-50);
  color: var(--error-700);
  border: 1px solid var(--error-200);
}

.alert-success {
  background: var(--success-50);
  color: var(--success-700);
  border: 1px solid var(--success-200);
}

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-primary {
  background: #3b82f6;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #2563eb;
  transform: translateY(-1px);
}

.btn-primary:disabled {
  background: #9ca3af;
  cursor: not-allowed;
  transform: none;
}

.btn-secondary {
  background: #6b7280;
  color: white;
}

.btn-secondary:hover:not(:disabled) {
  background: #4b5563;
  transform: translateY(-1px);
}

.button-group {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.button-group .btn {
  flex: 1;
  min-width: 200px;
}

@media (max-width: 768px) {
  .container {
    padding: 1rem;
  }
  
  .settings-grid {
    grid-template-columns: 1fr;
  }
  
  .admin-title {
    font-size: 2rem;
  }
  
  .card-body {
    padding: 1.5rem;
  }
  
  .button-group {
    flex-direction: column;
  }
  
  .button-group .btn {
    min-width: auto;
  }
}
</style>
