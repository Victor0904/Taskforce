<template>
  <div v-if="showAlert" class="password-change-alert">
    <span class="alert-icon">🔒</span>
    <h3 class="alert-title">Changement de mot de passe requis</h3>
    <p class="alert-message">Votre compte utilise encore le mot de passe par défaut "admin". 
      <a class="reset-link" @click.prevent="goToResetPassword">Aller à la page</a> 
      pour des raisons de sécurité.
    </p>
    <button class="btn-primary" @click="goToResetPassword">Changer le mot de passe</button>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const showAlert = ref(false)

const goToResetPassword = () => {
  router.push('/reset-password')
}

const checkPasswordStatus = () => {
  const token = localStorage.getItem('token')
  const mustChangePassword = localStorage.getItem('mustChangePassword') === 'true'
  
  // Condition exacte : token && mustChangePassword === 'true'
  if (token && mustChangePassword) {
    showAlert.value = true
  } else {
    showAlert.value = false
  }
}

const handleStorage = (event) => {
  if (event.key === 'token' || event.key === 'mustChangePassword') {
    checkPasswordStatus()
  }
}

const handleTokenChanged = () => {
  checkPasswordStatus()
}

onMounted(() => {
  checkPasswordStatus()
  window.addEventListener('storage', handleStorage)
  window.addEventListener('token-changed', handleTokenChanged)
})

onUnmounted(() => {
  window.removeEventListener('storage', handleStorage)
  window.removeEventListener('token-changed', handleTokenChanged)
})
</script>

<style scoped>
.password-change-alert {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  background: linear-gradient(135deg, #ff6b6b, #ee5a24);
  color: white;
  z-index: 1000;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  padding: 1rem 2rem;
  display: flex;
  align-items: center;
  gap: 1rem;
  max-width: 1200px;
  margin: 0 auto;
}

.alert-icon {
  font-size: 1.5rem;
  flex-shrink: 0;
}

.alert-title {
  font-size: 1.1rem;
  margin: 0 0 0.25rem 0;
  font-weight: bold;
}

.alert-message {
  margin: 0;
  font-size: 0.9rem;
  opacity: 0.9;
  flex: 1;
}

.reset-link {
  color: #ffffff;
  text-decoration: underline;
  font-weight: 600;
  transition: all 0.3s ease;
  cursor: pointer;
}

.reset-link:hover {
  color: #fbbf24;
  text-decoration: none;
}

.btn-primary {
  padding: 0.5rem 1rem;
  font-size: 0.9rem;
  white-space: nowrap;
  background: #ffffff;
  color: #ff6b6b;
  border: none;
  border-radius: 0.375rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-primary:hover {
  background: #f8f9fa;
  transform: translateY(-1px);
}

@media (max-width: 768px) {
  .password-change-alert {
    flex-direction: column;
    text-align: center;
    padding: 1rem;
  }
  
  .alert-message {
    order: 2;
  }
  
  .btn-primary {
    order: 3;
    width: 100%;
  }
}
</style>
