<template>
    <div class="p-6">
        <h1 class="text-3xl font-bold mb-6">Dashboard - TaskForce</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Projets récents -->
            <div class="bg-white p-4 rounded-2xl shadow">
                <h2 class="text-xl font-semibold mb-4">Missions en cours</h2>
                <ul>
                    <li v-for="mission in missions" :key="mission.id" class="mb-2">
                        <span class="font-medium">{{ mission.titre }}</span> - {{ mission.date_debut }} → {{
                        mission.date_fin }}
                    </li>
                </ul>
            </div>

            <!-- Collaborateurs actifs -->
            <div class="bg-white p-4 rounded-2xl shadow">
                <h2 class="text-xl font-semibold mb-4">Collaborateurs actifs</h2>
                <ul>
                    <li v-for="collab in collaborateurs" :key="collab.id" class="mb-2">
                        {{ collab.nom }} {{ collab.prenom }} - {{ collab.poste }}
                    </li>
                </ul>
            </div>

            <!-- Affectations récentes -->
            <div class="bg-white p-4 rounded-2xl shadow col-span-1 md:col-span-2">
                <h2 class="text-xl font-semibold mb-4">Affectations récentes</h2>
                <table class="w-full table-auto">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Collaborateur</th>
                            <th class="text-left py-2">Mission</th>
                            <th class="text-left py-2">Rôle</th>
                            <th class="text-left py-2">Période</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="aff in affectations" :key="aff.id" class="border-b">
                            <td class="py-2">{{ aff.collaborateur }}</td>
                            <td class="py-2">{{ aff.mission }}</td>
                            <td class="py-2">{{ aff.role }}</td>
                            <td class="py-2">{{ aff.date_debut }} → {{ aff.date_fin }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { api } from '../api';

const missions = ref([]);
const collaborateurs = ref([]);
const affectations = ref([]);

onMounted(async () => {
    const [resMissions, resCollab, resAffect] = await Promise.all([
        api.get('/missions'),
        api.get('/collaborateurs'),
        api.get('/affectations'),
    ]);

    missions.value = resMissions.data;
    collaborateurs.value = resCollab.data;
    affectations.value = resAffect.data;
});
</script>
  
