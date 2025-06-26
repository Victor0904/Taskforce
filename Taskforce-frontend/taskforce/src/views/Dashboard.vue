<template>
    <div class="dashboard">
        <!-- Header -->
        <div class="dashboard-header">
            <h1 class="dashboard-title">Bonjour !</h1>
            <p class="dashboard-subtitle">Voici un aperçu de votre TaskForce aujourd'hui</p>
        </div>

        <!-- Actions rapides - Déplacées en haut -->
        <div class="content-card actions-card actions-card-top">
            <h2 class="card-title">⚡ Actions rapides</h2>
            
            <div class="actions-grid actions-grid-compact">
                <button class="action-btn action-btn-compact action-btn-blue">
                    <div class="action-icon action-icon-compact">➕</div>
                    <span>Nouvelle Mission</span>
                </button>
                
                <button class="action-btn action-btn-compact action-btn-green">
                    <div class="action-icon action-icon-compact">👤</div>
                    <span>Ajouter Collaborateur</span>
                </button>
                
                <button class="action-btn action-btn-compact action-btn-purple">
                    <div class="action-icon action-icon-compact">🎯</div>
                    <span>Gérer Compétences</span>
                </button>
                
                <button class="action-btn action-btn-compact action-btn-orange">
                    <div class="action-icon action-icon-compact">📊</div>
                    <span>Voir Rapports</span>
                </button>
            </div>
        </div>

        <!-- Cartes de statistiques -->
        <div class="stats-grid">
            <div class="stat-card stat-card-blue">
                <div class="stat-content">
                    <div class="stat-info">
                        <p class="stat-label">Total Missions</p>
                        <p class="stat-value">{{ missions.length }}</p>
                    </div>
                    <div class="stat-icon stat-icon-blue">
                        📋
                    </div>
                </div>
                <div class="stat-footer">
                    <span class="stat-badge stat-badge-green">{{ missionsActives }} en cours</span>
                </div>
            </div>

            <div class="stat-card stat-card-green">
                <div class="stat-content">
                    <div class="stat-info">
                        <p class="stat-label">Collaborateurs</p>
                        <p class="stat-value">{{ collaborateurs.length }}</p>
                    </div>
                    <div class="stat-icon stat-icon-green">
                        👥
                    </div>
                </div>
                <div class="stat-footer">
                    <span class="stat-badge stat-badge-blue">{{ collaborateursActifs }} actifs</span>
                </div>
            </div>

            <div class="stat-card stat-card-purple">
                <div class="stat-content">
                    <div class="stat-info">
                        <p class="stat-label">Compétences</p>
                        <p class="stat-value">{{ competences.length }}</p>
                    </div>
                    <div class="stat-icon stat-icon-purple">
                        🎯
                    </div>
                </div>
                <div class="stat-footer">
                    <span class="stat-badge stat-badge-orange">Disponibles</span>
                </div>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="main-content">
            <!-- Missions en cours -->
            <div class="content-card missions-card">
                <div class="card-header">
                    <h2 class="card-title">🚀 Missions en cours</h2>
                    <button class="btn btn-primary">Voir tout</button>
                </div>
                
                <div class="missions-list">
                    <div v-for="mission in missions.slice(0, 5)" :key="mission.id" class="mission-item">
                        <div class="mission-info">
                            <h3 class="mission-title">{{ mission.titre }}</h3>
                            <p class="mission-dates">
                                📅 {{ formatDate(mission.date_debut) }} → {{ formatDate(mission.date_fin) }}
                            </p>
                        </div>
                        <div class="mission-status">
                            <span class="status-badge status-active">En cours</span>
                            <div class="progress-mini">
                                <div class="progress-mini-fill"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Collaborateurs -->
            <div class="content-card collaborators-card">
                <h2 class="card-title">👥 Collaborateurs</h2>
                
                <div class="collaborators-list">
                    <div v-for="collab in collaborateurs.slice(0, 5)" :key="collab.id" class="collaborator-item">
                        <div class="collaborator-avatar">
                            {{ getInitials(collab.nom, collab.prenom) }}
                        </div>
                        <div class="collaborator-info">
                            <p class="collaborator-name">{{ collab.nom }} {{ collab.prenom }}</p>
                            <p class="collaborator-role">{{ collab.poste }}</p>
                        </div>
                        <div class="collaborator-status">
                            <span class="status-badge status-available">Disponible</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import { api } from '../api';

const missions = ref([]);
const collaborateurs = ref([]);
const competences = ref([]);

// Données calculées
const missionsActives = computed(() => {
    return missions.value.filter(mission => {
        const now = new Date();
        const debut = new Date(mission.date_debut);
        const fin = new Date(mission.date_fin);
        return debut <= now && now <= fin;
    }).length;
});

const collaborateursActifs = computed(() => {
    return Math.floor(collaborateurs.value.length * 0.8);
});

const tauxOccupation = computed(() => {
    if (collaborateurs.value.length === 0) return 0;
    return Math.floor(Math.random() * 40 + 60); // Simulation d'un taux entre 60-100%
});

// Fonctions utilitaires
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

const getInitials = (nom, prenom) => {
    if (!nom || !prenom) return '??';
    return `${nom.charAt(0)}${prenom.charAt(0)}`.toUpperCase();
};
</script>

<style scoped>
/* === STYLES GÉNÉRAUX === */
.dashboard {
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* === HEADER === */
.dashboard-header {
    margin-bottom: 30px;
}

.dashboard-title {
    font-size: 2.5rem;
    font-weight: bold;
    color: white;
    margin: 0 0 10px 0;
}

.dashboard-subtitle {
    color: rgba(255, 255, 255, 0.8);
    font-size: 1.1rem;
    margin: 0;
}

/* === ACTIONS RAPIDES EN HAUT === */
.actions-card-top {
    margin-bottom: 30px;
}

.actions-card-top .card-title {
    font-size: 1.2rem;
    margin-bottom: 15px;
}

.actions-grid-compact {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 10px;
}

.action-btn-compact {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 12px 8px;
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    background: transparent;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: inherit;
}

.action-btn-compact:hover {
    transform: translateY(-1px);
}

.action-btn-blue:hover {
    border-color: #3b82f6;
    background: #eff6ff;
}

.action-btn-green:hover {
    border-color: #10b981;
    background: #ecfdf5;
}

.action-btn-purple:hover {
    border-color: #8b5cf6;
    background: #f5f3ff;
}

.action-btn-orange:hover {
    border-color: #f59e0b;
    background: #fffbeb;
}

.action-icon-compact {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    margin-bottom: 8px;
    transition: all 0.3s ease;
}

.action-btn-blue .action-icon-compact {
    background: #dbeafe;
}

.action-btn-green .action-icon-compact {
    background: #d1fae5;
}

.action-btn-purple .action-icon-compact {
    background: #ede9fe;
}

.action-btn-orange .action-icon-compact {
    background: #fef3c7;
}

.action-btn-compact:hover .action-icon-compact {
    transform: scale(1.1);
}

.action-btn-compact span {
    font-size: 0.8rem;
    font-weight: 500;
    color: #374151;
    text-align: center;
}

/* === GRILLE DES STATISTIQUES === */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border-left: 4px solid;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.stat-card-blue { border-left-color: #3b82f6; }
.stat-card-green { border-left-color: #10b981; }
.stat-card-purple { border-left-color: #8b5cf6; }
.stat-card-orange { border-left-color: #f59e0b; }

.stat-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.stat-label {
    font-size: 0.9rem;
    color: #6b7280;
    margin: 0 0 5px 0;
}

.stat-value {
    font-size: 2rem;
    font-weight: bold;
    color: #1f2937;
    margin: 0;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-icon-blue { background: #dbeafe; }
.stat-icon-green { background: #d1fae5; }
.stat-icon-purple { background: #ede9fe; }
.stat-icon-orange { background: #fef3c7; }

.stat-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.stat-badge-green { background: #d1fae5; color: #065f46; }
.stat-badge-blue { background: #dbeafe; color: #1e40af; }
.stat-badge-orange { background: #fef3c7; color: #92400e; }

.progress-bar {
    width: 100%;
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: #f59e0b;
    border-radius: 3px;
    transition: width 0.5s ease;
}

/* === CONTENU PRINCIPAL === */
.main-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}

.content-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.card-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: #1f2937;
    margin: 0;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #3b82f6;
    color: white;
}

.btn-primary:hover {
    background: #2563eb;
    transform: translateY(-1px);
}

/* === MISSIONS === */
.missions-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.mission-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.mission-item:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transform: translateY(-1px);
}

.mission-title {
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 5px 0;
}

.mission-dates {
    font-size: 0.9rem;
    color: #6b7280;
    margin: 0;
}

.mission-status {
    display: flex;
    align-items: center;
    gap: 10px;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-available {
    background: #dbeafe;
    color: #1e40af;
}

.progress-mini {
    width: 40px;
    height: 4px;
    background: #e5e7eb;
    border-radius: 2px;
    overflow: hidden;
}

.progress-mini-fill {
    width: 70%;
    height: 100%;
    background: #3b82f6;
    border-radius: 2px;
}

/* === COLLABORATEURS === */
.collaborators-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.collaborator-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 12px;
    border-radius: 8px;
    transition: background 0.3s ease;
}

.collaborator-item:hover {
    background: #f9fafb;
}

.collaborator-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #3b82f6, #8b5cf6);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 0.9rem;
}

.collaborator-name {
    font-weight: 500;
    color: #1f2937;
    margin: 0 0 4px 0;
}

.collaborator-role {
    font-size: 0.9rem;
    color: #6b7280;
    margin: 0;
}

.collaborator-status {
    margin-left: auto;
}

/* === RESPONSIVE === */
@media (max-width: 1024px) {
    .main-content {
        grid-template-columns: 1fr;
    }
    
    .actions-grid-compact {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .dashboard {
        padding: 15px;
    }
    
    .dashboard-title {
        font-size: 2rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .actions-grid-compact {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
    
    .action-btn-compact {
        padding: 10px 6px;
    }
    
    .action-btn-compact span {
        font-size: 0.75rem;
    }
    
    .card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .mission-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .mission-status {
        align-self: flex-end;
    }
}

@media (max-width: 480px) {
    .actions-grid-compact {
        grid-template-columns: 1fr;
    }
    
    .collaborator-item {
        flex-wrap: wrap;
    }
}
</style>

