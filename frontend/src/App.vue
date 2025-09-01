<template>
  <div>
    <div v-if="showSplash" class="splash-overlay">
      <div class="splash-content">
        <div class="splash-title">Taskforce</div>
        <div class="splash-subtitle">Chargement des données...</div>
        <div class="splash-spinner"></div>
      </div>
    </div>

    <PasswordChangeAlert />
    <Navbar />
    <main class="main-content">
      <RouterView />
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import Navbar from './components/Navbar.vue'
import PasswordChangeAlert from './components/PasswordChangeAlert.vue'

const showSplash = ref(false)
let splashTimeoutId = null

const triggerSplash = () => {
  if (splashTimeoutId) {
    clearTimeout(splashTimeoutId)
    splashTimeoutId = null
  }
  showSplash.value = true
  splashTimeoutId = setTimeout(() => {
    showSplash.value = false
    splashTimeoutId = null
  }, 5000)
}

const handleShowSplashEvent = () => {
  triggerSplash()
}

onMounted(() => {
  window.addEventListener('show-splash', handleShowSplashEvent)
})

onUnmounted(() => {
  window.removeEventListener('show-splash', handleShowSplashEvent)
  if (splashTimeoutId) clearTimeout(splashTimeoutId)
})
</script>

<style scoped>
.splash-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  color: white;
}

.splash-content {
  text-align: center;
}

.splash-title {
  font-size: 3rem;
  font-weight: bold;
  margin-bottom: 1rem;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.splash-subtitle {
  font-size: 1.2rem;
  margin-bottom: 2rem;
  opacity: 0.9;
}

.splash-spinner {
  width: 50px;
  height: 50px;
  border: 4px solid rgba(255, 255, 255, 0.3);
  border-top: 4px solid white;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
