<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Liste des collaborateurs</h1>

        <ul v-if="collaborateurs.length">
            <li v-for="collab in collaborateurs" :key="collab.id" class="mb-2 p-2 border rounded-lg">
                <strong>{{ collab.nom }} {{ collab.prenom }}</strong> — {{ collab.poste }}
                <div class="text-sm text-gray-600">
                    Compétences : {{ (collab.competences || []).join(', ') || 'Non renseignées' }}
                </div>
            </li>
        </ul>

        <p v-else-if="erreur">❌ Erreur lors du chargement des collaborateurs.</p>
        <p v-else>Chargement...</p>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { api } from '@/api'

const collaborateurs = ref([])
const erreur = ref(false)

onMounted(async () => {
    try {
        const res = await api.get('/collaborateurs')
        console.log('Réponse API collaborateurs :', res.data)
        collaborateurs.value = res.data
    } catch (err) {
        console.error('Erreur API :', err)
        erreur.value = true
    }
})
</script>
  
