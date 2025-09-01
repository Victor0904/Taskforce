<template>
  <div class="login-container">
    <div class="login-card">
      <h1 class="login-title">Connexion</h1>
      <form @submit.prevent="login">
        <div class="form-group">
          <label class="form-label">
            Email :
            <input type="email" v-model="email" class="form-input" required />
          </label>
        </div>
        <div class="form-group">
          <label class="form-label">
            Mot de passe :
            <input type="password" v-model="password" class="form-input" required />
          </label>
        </div>
        <button type="submit" class="btn btn-primary btn-large">
          Se connecter
        </button>
      </form>
      <p v-if="error" class="alert alert-error">{{ error }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { jwtDecode } from 'jwt-decode'

const email = ref('')
const password = ref('')
const error = ref('')
const router = useRouter()

const login = async () => {
  try {
    const response = await axios.post('http://127.0.0.1:8000/api/login', {
      email: email.value,
      password: password.value
    })

    const serverToken = response.data.token
    const mustChangePassword = response.data.mustChangePassword === true
    
    const decoded = jwtDecode(serverToken)
    localStorage.setItem('token', serverToken)
    localStorage.setItem('userEmail', email.value)
    localStorage.setItem('mustChangePassword', mustChangePassword.toString())
    window.dispatchEvent(new CustomEvent('token-changed'))

    error.value = ''

    // Temporairement, on ne redirige jamais vers ResetPassword pour éviter l'erreur
    // if (mustChangePassword) {
    //   return router.push({ name: 'ResetPassword' })
    // }

    // Afficher le splash pendant 5s
    window.dispatchEvent(new CustomEvent('show-splash'))

    // Décaler la navigation pour laisser le splash s'afficher
    setTimeout(() => {
      if (decoded.roles.includes('ROLE_ADMIN') || decoded.roles.includes('ROLE_CHEF_PROJET')) {
        router.push('/admin')
      } else if (decoded.roles.includes('ROLE_MANAGER')) {
        router.push('/admin')
      } else {
        router.push('/dashboard')
      }
    }, 100) // Augmenter le délai pour éviter les conflits
  } catch (err) {
    error.value = 'Identifiants invalides'
  }
}
</script>
