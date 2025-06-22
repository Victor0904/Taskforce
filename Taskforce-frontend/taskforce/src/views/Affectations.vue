<template>
    <div>
        <h1>Liste des affectations</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Collaborateur</th>
                    <th>Mission</th>
                    <th>Rôle</th>
                    <th>Date début</th>
                    <th>Date fin</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="aff in affectations" :key="aff.id">
                    <td>{{ aff.id }}</td>
                    <td>{{ aff.collaborateur }}</td>
                    <td>{{ aff.mission }}</td>
                    <td>{{ aff.role }}</td>
                    <td>{{ aff.date_debut }}</td>
                    <td>{{ aff.date_fin }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { api } from '../api'

const affectations = ref([])

onMounted(async () => {
    try {
        const res = await api.get('/affectations')
        affectations.value = res.data
    } catch (err) {
        console.error('Erreur lors du chargement des affectations', err)
    }
})
</script>

<style scoped>
table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 1rem;
}

th,
td {
    padding: 0.5rem;
    border: 1px solid #ddd;
    text-align: left;
}
</style>
  
