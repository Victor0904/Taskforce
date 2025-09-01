<template>
  <div v-if="showAlert" class="password-change-alert">
    <div class="alert-content">
      <div class="alert-icon">🔒</div>
      <div class="alert-text">
        <strong>Changement de mot de passe requis</strong>
        <p>Votre compte utilise encore le mot de passe par défaut "admin". 
          <a href="#" @click.prevent="goToResetPassword" class="alert-link">
            Cliquez ici pour changer votre mot de passe
          </a> 
          pour des raisons de sécurité.
        </p>
      </div>
      <button @click="goToResetPassword" class="btn btn-primary btn-small">
        Changer le mot de passe
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { jwtDecode } from 'jwt-decode'

const router = useRouter()
const showAlert = ref(false)

const goToResetPassword = () => {
  router.push('/reset-password')
}

const checkPasswordStatus = () => {
  const token = localStorage.getItem('token')
  const mustChangePassword = localStorage.getItem('mustChangePassword') === 'true'
  
  if (token && mustChangePassword) {
    showAlert.value = true
  } else {
    showAlert.value = false
  }
}

onMounted(() => {
  checkPasswordStatus()
})

// Écouter les changements de token (connexion/déconnexion)
watch(() => localStorage.getItem('token'), () => {
  checkPasswordStatus()
})

// Écouter les événements de changement de token
window.addEventListener('storage', checkPasswordStatus)
window.addEventListener('token-changed', checkPasswordStatus)
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
}

.alert-content {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.alert-icon {
  font-size: 1.5rem;
  flex-shrink: 0;
}

.alert-text {
  flex: 1;
}

.alert-text strong {
  display: block;
  font-size: 1.1rem;
  margin-bottom: 0.25rem;
}

.alert-text p {
  margin: 0;
  font-size: 0.9rem;
  opacity: 0.9;
}

.alert-link {
  color: #ffffff;
  text-decoration: underline;
  font-weight: 600;
  transition: all 0.3s ease;
}

.alert-link:hover {
  color: #fbbf24;
  text-decoration: none;
}

.btn-small {
  padding: 0.5rem 1rem;
  font-size: 0.9rem;
  white-space: nowrap;
}

@media (max-width: 768px) {
  .alert-content {
    flex-direction: column;
    text-align: center;
    padding: 1rem;
  }
  
  .alert-text {
    order: 2;
  }
  
  .btn-small {
    order: 3;
    width: 100%;
  }
}
</style>
