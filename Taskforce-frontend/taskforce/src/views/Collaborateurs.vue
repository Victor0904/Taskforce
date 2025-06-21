<template>
    <div>
        <h1>Liste des collaborateurs</h1>
        <ul v-if="collaborateurs.length">
            <li v-for="collab in collaborateurs" :key="collab.id">
                {{ collab.nom }} – {{ collab.competences.join(', ') }}
            </li>
        </ul>
        <p v-else>Chargement en cours...</p>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { api } from '@/api'

const collaborateurs = ref([])

onMounted(async () => {
    try {
        const res = await api.get('/collaborateurs')
        collaborateurs.value = res.data
    } catch (err) {
        console.error('Erreur API :', err)
    }
})
</script>
  
