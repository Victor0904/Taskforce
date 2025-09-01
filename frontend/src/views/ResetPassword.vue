<template>
  <div class="reset-wrap">
    <h1>Modifier le mot de passe</h1>
    <form @submit.prevent="submit">
      <div class="form-group">
        <label for="p1">Nouveau mot de passe</label>
        <input id="p1" type="password" v-model="p1" required class="form-input" />
      </div>
      <div class="form-group">
        <label for="p2">Confirmer le mot de passe</label>
        <input id="p2" type="password" v-model="p2" required class="form-input" />
      </div>
      <div class="actions">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'

const p1 = ref('')
const p2 = ref('')
const router = useRouter()

const submit = async () => {
  if (p1.value !== p2.value) {
    return
  }
  try {
    const token = localStorage.getItem('token')
    await axios.post('http://127.0.0.1:8000/api/users/reset-password', { password: p1.value }, {
      headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json' }
    })
    
    localStorage.removeItem('token')
    localStorage.removeItem('mustChangePassword')
    
    // Émettre un événement pour notifier la navbar
    window.dispatchEvent(new CustomEvent('token-changed'))
    
    router.push('/login')
  } catch (e) {
    alert('Erreur lors de la mise à jour du mot de passe')
  }
}
</script>

<style scoped>
.reset-wrap { max-width: 420px; margin: 2rem auto; padding: 1.5rem; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; }
.form-group { margin-bottom: 1rem; }
.form-input { width: 100%; padding: 0.5rem; border: 1px solid #cbd5e1; border-radius: 6px; }
.actions { display: flex; justify-content: flex-end; }
.btn-primary { background: #2563eb; color: #fff; border: none; padding: 0.5rem 1rem; border-radius: 6px; cursor: pointer; }
</style>
