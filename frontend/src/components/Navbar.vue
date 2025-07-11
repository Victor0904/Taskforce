<template>
  <header class="navbar">
    <div class="navbar-brand">
      <RouterLink to="/" class="brand-link">
        <span class="brand-text">Taskforce</span>
      </RouterLink>
    </div>
    
    <nav class="navbar-nav">
      <RouterLink to="/" class="nav-link">
        <i class="icon">🏠</i>
        Dashboard
      </RouterLink>

      <!-- Affiche le lien User uniquement si ROLE_USER et PAS admin -->
      <RouterLink
        to="/user"
        v-if="hasRoleUserOnly"
        class="nav-link"
      >
        <i class="icon">👤</i>
        User
      </RouterLink>

      <!-- Affiche Admin uniquement pour ROLE_ADMIN -->
      <RouterLink
        to="/admin"
        v-if="hasRole('ROLE_ADMIN').value"
        class="nav-link admin-link"
      >
        <i class="icon">⚙️</i>
        Admin
      </RouterLink>
    </nav>

    <div class="navbar-actions">
      <div v-if="isLoggedIn" :class="userRoleClass" class="user-info">
        <div class="user-avatar">
          {{ userEmail.charAt(0).toUpperCase() }}
        </div>
        <span class="user-email">{{ userEmail }}</span>
      </div>
      
      <RouterLink to="/login" v-if="!isLoggedIn" class="login-btn">
        <i class="icon">🔐</i>
        Login
      </RouterLink>
      <button v-if="isLoggedIn" @click="logout" class="logout-btn">
        <i class="icon">🚪</i>
        Déconnexion
      </button>
    </div>
  </header>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { jwtDecode } from 'jwt-decode'
import { computed } from 'vue'

const router = useRouter()

const isLoggedIn = computed(() => !!localStorage.getItem('token'))

const decodedToken = computed(() => {
  const token = localStorage.getItem('token')
  if (!token) return null
  try {
    return jwtDecode(token)
  } catch {
    return null
  }
})

const userRoles = computed(() => decodedToken.value?.roles || [])
const userEmail = computed(() => decodedToken.value?.username || 'Utilisateur inconnu')

function hasRole(role) {
  return computed(() => userRoles.value.includes(role))
}

// ✅ User uniquement s’il a ROLE_USER et pas ROLE_ADMIN
const hasRoleUserOnly = computed(() => {
  return userRoles.value.includes('ROLE_USER') && !userRoles.value.includes('ROLE_ADMIN')
})

const userRoleClass = computed(() => {
  if (hasRole('ROLE_ADMIN').value) return 'admin'
  if (hasRole('ROLE_USER').value) return 'user'
  return ''
})

const logout = () => {
  localStorage.removeItem('token')
  router.push('/login')
}
</script>


<style scoped>
.navbar {
  background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
  padding: 0.75rem 2rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  position: sticky;
  top: 0;
  z-index: 1000;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.navbar-brand .brand-link {
  text-decoration: none;
  color: white;
  font-size: 1.5rem;
  font-weight: 700;
  transition: all 0.3s ease;
}

.brand-text {
  background: linear-gradient(45deg, #ffffff, #e2e8f0);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  letter-spacing: -0.5px;
}

.navbar-nav {
  display: flex;
  gap: 0.25rem;
  align-items: center;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  text-decoration: none;
  color: #cbd5e1;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  transition: all 0.2s ease;
  font-weight: 500;
  font-size: 0.9rem;
  position: relative;
}

.nav-link:hover {
  background: rgba(255, 255, 255, 0.08);
  color: white;
  transform: translateY(-1px);
}

.nav-link.router-link-exact-active {
  background: rgba(59, 130, 246, 0.15);
  color: #60a5fa;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.admin-link.router-link-exact-active {
  background: rgba(239, 68, 68, 0.15);
  color: #f87171;
}

.navbar-actions {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.4rem 0.8rem;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.user-avatar {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  background: #3b82f6;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 0.85rem;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.user-email {
  color: #e2e8f0;
  font-weight: 500;
  font-size: 0.85rem;
}

.user .user-avatar {
  background: #059669;
}

.admin .user-avatar {
  background: #dc2626;
}

.admin .user-email {
  color: #fecaca;
}

.login-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  text-decoration: none;
  color: white;
  background: #3b82f6;
  padding: 0.5rem 1.25rem;
  border-radius: 6px;
  font-weight: 600;
  font-size: 0.85rem;
  transition: all 0.2s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.login-btn:hover {
  background: #2563eb;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.logout-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: #dc2626;
  border: none;
  color: white;
  padding: 0.5rem 1.25rem;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  font-size: 0.85rem;
  transition: all 0.2s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.logout-btn:hover {
  background: #b91c1c;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

.icon {
  font-size: 0.9rem;
  opacity: 0.9;
}

.hidden {
  display: none;
}

/* Responsive Design */
@media (max-width: 768px) {
  .navbar {
    padding: 0.5rem 1rem;
    flex-wrap: wrap;
    gap: 0.5rem;
  }
  
  .navbar-nav {
    order: 3;
    width: 100%;
    justify-content: center;
    margin-top: 0.5rem;
    background: rgba(0, 0, 0, 0.1);
    padding: 0.5rem;
    border-radius: 6px;
  }
  
  .nav-link {
    padding: 0.4rem 0.8rem;
    font-size: 0.8rem;
  }
  
  .user-info {
    padding: 0.3rem 0.6rem;
  }
  
  .user-email {
    display: none;
  }
  
  .brand-text {
    font-size: 1.2rem;
  }
  
  .login-btn, .logout-btn {
    padding: 0.4rem 1rem;
    font-size: 0.8rem;
  }
}

@media (max-width: 480px) {
  .navbar-nav {
    gap: 0.1rem;
  }
  
  .nav-link {
    padding: 0.3rem 0.6rem;
    font-size: 0.75rem;
  }
  
  .nav-link span:not(.icon) {
    display: none;
  }
  
  .icon {
    font-size: 1rem;
  }
}
</style>
