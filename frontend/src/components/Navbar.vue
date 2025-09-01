<template>
  <nav class="navbar">
    <div class="navbar-container">
      <router-link to="/" class="navbar-brand">
        <i class="icon">🚀</i>
        Taskforce
      </router-link>

      <ul class="navbar-nav" v-if="isLoggedIn">
        <li>
          <router-link to="/dashboard" class="nav-link" :class="{ active: $route.name === 'Dashboard' }">
            <i class="icon">📊</i>
            Dashboard
          </router-link>
        </li>
        <li>
          <router-link to="/projets" class="nav-link" :class="{ active: $route.name === 'Projet' }">
            <i class="icon">📋</i>
            Projets
          </router-link>
        </li>

        <li>
          <router-link to="/admin" class="nav-link" :class="{ active: $route.name === 'Admin' }" v-if="hasRole('ROLE_ADMIN').value || hasRole('ROLE_MANAGER').value || hasRole('ROLE_CHEF_PROJET').value">
            <i class="icon">⚙️</i>
            Admin
          </router-link>
        </li>

      
        <li class="user-info">
          <router-link to="/parametres" class="user-email-link" :class="userRoleClass">
            <span class="user-email">{{ userEmail }}</span>
            <i class="icon">⚙️</i>
          </router-link>
        </li>
        <li>
          <button @click="logout" class="btn btn-secondary btn-small">
            <i class="icon">🚪</i>
            Déconnexion
          </button>
        </li>
      </ul>

      <div v-else class="navbar-nav">
        <router-link to="/login" class="btn btn-primary">
          <i class="icon">🔑</i>
          Connexion
        </router-link>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { jwtDecode } from 'jwt-decode'
import axios from 'axios'

const router = useRouter()

// Utiliser un ref pour forcer la réactivité
const tokenRef = ref(localStorage.getItem('token'))


const isLoggedIn = computed(() => !!tokenRef.value)

const decodedToken = computed(() => {
  if (!tokenRef.value) return null
  try {
    return jwtDecode(tokenRef.value)
  } catch {
    return null
  }
})

const userRoles = computed(() => decodedToken.value?.roles || [])
const userEmail = computed(() => decodedToken.value?.username || 'Utilisateur inconnu')

function hasRole(role) {
  return computed(() => userRoles.value.includes(role))
}

// ✅ User uniquement s'il a ROLE_USER et pas ROLE_ADMIN
const hasRoleUserOnly = computed(() => {
  return userRoles.value.includes('ROLE_USER') && !userRoles.value.includes('ROLE_ADMIN')
})

const userRoleClass = computed(() => {
  if (hasRole('ROLE_ADMIN').value) return 'admin'
  if (hasRole('ROLE_MANAGER').value) return 'manager'
  if (hasRole('ROLE_CHEF_PROJET').value) return 'chef-projet'
  if (hasRole('ROLE_USER').value) return 'user'
  return ''
})



// Fonction pour récupérer le rôle de l'utilisateur
const getUserRole = () => {
  const token = localStorage.getItem('token')
  if (!token) return null
  
  try {
    const decoded = JSON.parse(atob(token.split('.')[1]))
    return decoded.roles?.[0] || null
  } catch (e) {
    return null
  }
}

// Fonction pour vérifier automatiquement les surcharges




// Écouter les changements de localStorage
const handleStorageChange = (event) => {
  if (event.key === 'token') {
    const newToken = event.newValue
    if (newToken !== tokenRef.value) {
      tokenRef.value = newToken
      if (newToken) {
        // Démarrage des vérifications automatiques
      } else {
        // Arrêt des vérifications automatiques
      }
    }
  }
}

// Écouter les événements personnalisés de l'application
const handleTokenChange = () => {
  const newToken = localStorage.getItem('token')
  if (newToken !== tokenRef.value) {
    tokenRef.value = newToken
          if (newToken) {
        // Démarrage des vérifications automatiques
      } else {
        // Arrêt des vérifications automatiques
      }
  }
}

onMounted(() => {
  window.addEventListener('storage', handleStorageChange)
  window.addEventListener('token-changed', handleTokenChange)
})

onUnmounted(() => {
  window.removeEventListener('storage', handleStorageChange)
  window.removeEventListener('token-changed', handleTokenChange)
})

const logout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('mustChangePassword')
  tokenRef.value = null


  router.push('/login')
}


</script>

<style scoped>
.user-info {
  display: flex;
  align-items: center;
  margin-right: 1rem;
}

.user-email-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  text-decoration: none;
  color: #ffffff;
  font-weight: 600;
  font-size: 0.9rem;
  padding: 0.5rem 1rem;
  border-radius: 0.375rem;
  border: 1px solid rgba(255, 255, 255, 0.2);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

.user-email-link:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.user-email {
  color: inherit;
}

.user-email-link .icon {
  font-size: 0.8rem;
  opacity: 0.8;
}

/* Couleurs selon le rôle */
.user-email-link.admin {
  background-color: #dc2626; /* Rouge pour admin */
}

.user-email-link.manager {
  background-color: #2563eb; /* Bleu pour manager */
}

.user-email-link.chef-projet {
  background-color: #16a34a; /* Vert pour chef de projet */
}

.user-email-link.user {
  background-color: #6b7280; /* Gris pour collaborateur */
}
</style>
