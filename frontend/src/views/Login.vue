<template>
  <div>
    <h1>Connexion</h1>
    <form @submit.prevent="login">
      <label>
        Email :
        <input type="email" v-model="email" required />
      </label>
      <label>
        Mot de passe :
        <input type="password" v-model="password" required />
      </label>
      <button type="submit">Se connecter</button>
    </form>
    <p v-if="error" style="color: red;">{{ error }}</p>
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

    const token = response.data.token
    localStorage.setItem('token', token)
    error.value = ''

    const decoded = jwtDecode(token)
    console.log('Rôles décodés :', decoded.roles)

    if (decoded.roles.includes('ROLE_ADMIN')) {
      router.push('/admin')
    } else {
      router.push('/user')
    }
  } catch (err) {
    error.value = 'Identifiants invalides'
    console.error(err)
  }
}
</script>
