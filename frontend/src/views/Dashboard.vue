<template>
  <div class="dashboard">
    <!-- Header avec stats principales -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-content">
          <h3>{{ totalCollaborators }}</h3>
          <p>Collaborateurs</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">⏳</div>
        <div class="stat-content">
          <h3>{{ tasksInProgress }}</h3>
          <p>Tâches en cours</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-content">
          <h3>{{ completedTasks }}</h3>
          <p>Tâches terminées</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon">🔔</div>
        <div class="stat-content">
          <h3>{{ totalNotifications }}</h3>
          <p>Notifications</p>
        </div>
      </div>
    </div>

    <!-- Contenu principal -->
    <div class="main-content">
      <!-- Section gauche -->
      <div class="left-section">
        <!-- Bloc Collaborateurs -->
        <div class="collaborators-card">
          <div class="section-header">
            <h3>Collaborateurs</h3>
            <button class="view-all-btn">Voir tout</button>
          </div>
          <div class="collaborators-list">
            <div class="collaborator-item" v-for="collaborator in collaborators" :key="collaborator.id">
              <div class="collaborator-avatar">
                {{ collaborator.name.charAt(0).toUpperCase() }}
              </div>
              <div class="collaborator-content">
                <h4>{{ collaborator.name }}</h4>
                <p>{{ collaborator.role }}</p>
                <div class="collaborator-status" :class="collaborator.status">
                  {{ collaborator.status === 'online' ? 'En ligne' : 'Hors ligne' }}
                </div>
              </div>
              <div class="collaborator-tasks">
                <span class="task-count">{{ collaborator.activeTasks }}</span>
                <small>tâches</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Bloc Tâches en cours -->
        <div class="tasks-progress-card">
          <div class="section-header">
            <h3>Tâches en cours</h3>
            <button class="view-all-btn">Voir tout</button>
          </div>
          <div class="tasks-list">
            <div class="task-item" v-for="task in tasksInProgressList" :key="task.id">
              <div class="task-priority" :class="task.priority"></div>
              <div class="task-content">
                <h4>{{ task.title }}</h4>
                <p>{{ task.description }}</p>
                <div class="task-meta">
                  <span class="task-assignee">{{ task.assignee }}</span>
                  <span class="task-date">{{ formatDate(task.startDate) }}</span>
                </div>
              </div>
              <div class="task-progress">
                <div class="progress-circle" :style="{ '--progress': task.progress + '%' }">
                  <span>{{ task.progress }}%</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Section droite -->
      <div class="right-section">
        <!-- Bloc Notifications -->
        <div class="notifications-card">
          <div class="section-header">
            <h3>Notifications</h3>
            <button class="view-all-btn">Tout marquer lu</button>
          </div>
          <div class="notifications-list">
            <div class="notification-item" v-for="notification in notifications" :key="notification.id" :class="notification.type">
              <div class="notification-icon">{{ notification.icon }}</div>
              <div class="notification-content">
                <p>{{ notification.message }}</p>
                <span class="notification-time">{{ notification.time }}</span>
              </div>
              <div class="notification-status" v-if="!notification.read"></div>
            </div>
          </div>
        </div>

        <!-- Bloc Tâches terminées par collaborateur -->
        <div class="completed-tasks-card">
          <div class="section-header">
            <h3>Tâches terminées</h3>
            <button class="view-all-btn">Voir tout</button>
          </div>
          <div class="completed-tasks-list">
            <div class="completed-task-item" v-for="task in completedTasksList" :key="task.id">
              <div class="task-status completed"></div>
              <div class="task-content">
                <h4>{{ task.title }}</h4>
                <p>{{ task.description }}</p>
                <div class="task-meta">
                  <span class="task-completed-by">Terminé par {{ task.completedBy }}</span>
                  <span class="task-completed-date">{{ formatDate(task.completedDate) }}</span>
                </div>
              </div>
              <div class="task-duration">
                <span class="duration-badge">{{ task.duration }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

// Données réactives - À connecter avec votre BDD
const totalCollaborators = ref(0)
const tasksInProgress = ref(0)
const completedTasks = ref(0)
const totalNotifications = ref(0)

// Listes - À remplir avec vos données BDD
const collaborators = ref([])
const tasksInProgressList = ref([])
const notifications = ref([])
const completedTasksList = ref([])

// Méthodes utilitaires
const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('fr-FR', { 
    month: 'short', 
    day: 'numeric' 
  })
}

// Méthodes pour les actions (à implémenter selon votre API)
const loadCollaborators = async () => {
  // TODO: Charger les collaborateurs depuis votre BDD
  console.log('Loading collaborators...')
}

const loadTasksInProgress = async () => {
  // TODO: Charger les tâches en cours depuis votre BDD
  console.log('Loading tasks in progress...')
}

const loadNotifications = async () => {
  // TODO: Charger les notifications depuis votre BDD
  console.log('Loading notifications...')
}

const loadCompletedTasks = async () => {
  // TODO: Charger les tâches terminées depuis votre BDD
  console.log('Loading completed tasks...')
}

const loadDashboardData = async () => {
  // TODO: Charger toutes les données du dashboard
  await Promise.all([
    loadCollaborators(),
    loadTasksInProgress(),
    loadNotifications(),
    loadCompletedTasks()
  ])
}

onMounted(() => {
  loadDashboardData()
})
</script>

<style scoped>
.dashboard {
  padding: 1.5rem;
  background: #f8fafc;
  min-height: calc(100vh - 60px);
  max-height: calc(100vh - 60px);
  overflow: hidden;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.stat-card {
  background: white;
  padding: 1rem;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  display: flex;
  align-items: center;
  gap: 0.75rem;
  border-left: 4px solid #3b82f6;
}

.stat-icon {
  font-size: 1.5rem;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #eff6ff;
  border-radius: 6px;
}

.stat-content h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}

.stat-content p {
  font-size: 0.8rem;
  color: #64748b;
  margin: 0;
}

/* Main Content */
.main-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
  height: calc(100vh - 200px);
}

.left-section, .right-section {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

/* Cards communes */
.collaborators-card,
.tasks-progress-card,
.notifications-card,
.completed-tasks-card {
  background: white;
  padding: 1.25rem;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  flex: 1;
  overflow: hidden;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.section-header h3 {
  margin: 0;
  color: #1e293b;
  font-size: 1rem;
  font-weight: 600;
}

.view-all-btn {
  background: none;
  border: none;
  color: #3b82f6;
  font-size: 0.8rem;
  cursor: pointer;
  font-weight: 500;
}

/* Collaborateurs */
.collaborators-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  max-height: 400px;
  overflow-y: auto;
}

.collaborator-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem;
  background: #f8fafc;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.collaborator-item:hover {
  transform: translateX(2px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.collaborator-avatar {
  width: 40px;
  height: 40px;
  border-radius: 6px;
  background: #3b82f6;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 600;
  font-size: 0.9rem;
}

.collaborator-content {
  flex: 1;
  min-width: 0;
}

.collaborator-content h4 {
  margin: 0 0 0.25rem 0;
  font-size: 0.85rem;
  font-weight: 600;
  color: #1e293b;
}

.collaborator-content p {
  margin: 0 0 0.25rem 0;
  font-size: 0.75rem;
  color: #64748b;
}

.collaborator-status {
  font-size: 0.7rem;
  padding: 0.125rem 0.5rem;
  border-radius: 12px;
  font-weight: 500;
}

.collaborator-status.online {
  background: #dcfce7;
  color: #166534;
}

.collaborator-status.offline {
  background: #f1f5f9;
  color: #475569;
}

.collaborator-tasks {
  text-align: center;
}

.task-count {
  display: block;
  font-size: 1rem;
  font-weight: 600;
  color: #3b82f6;
}

.collaborator-tasks small {
  font-size: 0.7rem;
  color: #64748b;
}

/* Tâches en cours */
.tasks-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  max-height: 400px;
  overflow-y: auto;
}

.task-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem;
  background: #f8fafc;
  border-radius: 6px;
  border-left: 3px solid #e2e8f0;
  transition: all 0.2s ease;
}

.task-item:hover {
  transform: translateX(2px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.task-priority {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  flex-shrink: 0;
}

.task-priority.high {
  background: #f59e0b;
}

.task-priority.urgent {
  background: #dc2626;
}

.task-priority.medium {
  background: #3b82f6;
}

.task-priority.low {
  background: #10b981;
}

.task-content {
  flex: 1;
  min-width: 0;
}

.task-content h4 {
  margin: 0 0 0.25rem 0;
  font-size: 0.85rem;
  font-weight: 600;
  color: #1e293b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.task-content p {
  margin: 0 0 0.5rem 0;
  font-size: 0.75rem;
  color: #64748b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.task-meta {
  display: flex;
  gap: 0.75rem;
  font-size: 0.7rem;
  color: #94a3b8;
}

.task-progress {
  flex-shrink: 0;
}

.progress-circle {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: conic-gradient(#3b82f6 var(--progress), #e2e8f0 0);
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.progress-circle::before {
  content: '';
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: white;
  position: absolute;
}

.progress-circle span {
  font-size: 0.7rem;
  font-weight: 600;
  color: #1e293b;
  z-index: 1;
}

/* Notifications */
.notifications-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  max-height: 400px;
  overflow-y: auto;
}

.notification-item {
  display: flex;
  align-items: start;
  gap: 0.75rem;
  padding: 0.75rem;
  border-radius: 6px;
  border-left: 3px solid #e2e8f0;
  position: relative;
  transition: all 0.2s ease;
}

.notification-item:hover {
  transform: translateX(2px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.notification-item.success {
  background: #f0fdf4;
  border-left-color: #10b981;
}

.notification-item.warning {
  background: #fffbeb;
  border-left-color: #f59e0b;
}

.notification-item.info {
  background: #eff6ff;
  border-left-color: #3b82f6;
}

.notification-item.error {
  background: #fef2f2;
  border-left-color: #dc2626;
}

.notification-icon {
  font-size: 1rem;
  flex-shrink: 0;
  margin-top: 0.125rem;
}

.notification-content {
  flex: 1;
  min-width: 0;
}

.notification-content p {
  margin: 0 0 0.25rem 0;
  font-size: 0.8rem;
  color: #1e293b;
  line-height: 1.4;
}

.notification-time {
  font-size: 0.7rem;
  color: #94a3b8;
}

.notification-status {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: #3b82f6;
  flex-shrink: 0;
  margin-top: 0.25rem;
}

/* Tâches terminées */
.completed-tasks-list {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  max-height: 400px;
  overflow-y: auto;
}

.completed-task-item {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem;
  background: #f8fafc;
  border-radius: 6px;
  border-left: 3px solid #10b981;
  transition: all 0.2s ease;
}

.completed-task-item:hover {
  transform: translateX(2px);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.task-status {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  flex-shrink: 0;
}

.task-status.completed {
  background: #10b981;
}

.task-duration {
  flex-shrink: 0;
}

.duration-badge {
  background: #dcfce7;
  color: #166534;
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 0.7rem;
  font-weight: 500;
}

.task-completed-by {
  color: #10b981;
  font-weight: 500;
}

.task-completed-date {
  color: #94a3b8;
}

/* Scrollbar personnalisé */
.collaborators-list::-webkit-scrollbar,
.tasks-list::-webkit-scrollbar,
.notifications-list::-webkit-scrollbar,
.completed-tasks-list::-webkit-scrollbar {
  width: 4px;
}

.collaborators-list::-webkit-scrollbar-track,
.tasks-list::-webkit-scrollbar-track,
.notifications-list::-webkit-scrollbar-track,
.completed-tasks-list::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 2px;
}

.collaborators-list::-webkit-scrollbar-thumb,
.tasks-list::-webkit-scrollbar-thumb,
.notifications-list::-webkit-scrollbar-thumb,
.completed-tasks-list::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 2px;
}

.collaborators-list::-webkit-scrollbar-thumb:hover,
.tasks-list::-webkit-scrollbar-thumb:hover,
.notifications-list::-webkit-scrollbar-thumb:hover,
.completed-tasks-list::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
  
  .main-content {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
  
  .dashboard {
    padding: 1rem;
  }
}

@media (max-width: 768px) {
  .dashboard {
    padding: 0.75rem;
    min-height: calc(100vh - 120px);
    max-height: calc(100vh - 120px);
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
    gap: 0.75rem;
    margin-bottom: 1rem;
  }
  
  .stat-card {
    padding: 0.75rem;
  }
  
  .stat-icon {
    width: 32px;
    height: 32px;
    font-size: 1.2rem;
  }
  
  .stat-content h3 {
    font-size: 1.25rem;
  }
  
  .main-content {
    height: calc(100vh - 180px);
    gap: 0.75rem;
  }
  
  .left-section, .right-section {
    gap: 1rem;
  }
  
  .collaborators-card,
  .tasks-progress-card,
  .notifications-card,
  .completed-tasks-card {
    padding: 1rem;
  }
  
  .collaborators-list,
  .tasks-list,
  .notifications-list,
  .completed-tasks-list {
    max-height: 300px;
  }
}

@media (max-width: 480px) {
  .dashboard {
    padding: 0.5rem;
  }
  
  .stats-grid {
    gap: 0.5rem;
  }
  
  .stat-card {
    padding: 0.5rem;
    gap: 0.5rem;
  }
  
  .stat-icon {
    width: 28px;
    height: 28px;
    font-size: 1rem;
  }
  
  .stat-content h3 {
    font-size: 1.1rem;
  }
  
  .stat-content p {
    font-size: 0.75rem;
  }
  
  .main-content {
    gap: 0.5rem;
  }
  
  .left-section, .right-section {
    gap: 0.75rem;
  }
  
  .collaborators-card,
  .tasks-progress-card,
  .notifications-card,
  .completed-tasks-card {
    padding: 0.75rem;
  }
  
  .section-header h3 {
    font-size: 0.9rem;
  }
  
  .collaborator-avatar {
    width: 32px;
    height: 32px;
    font-size: 0.8rem;
  }
  
  .progress-circle {
    width: 32px;
    height: 32px;
  }
  
  .progress-circle::before {
    width: 22px;
    height: 22px;
  }
  
  .progress-circle span {
    font-size: 0.6rem;
  }
  
  .collaborators-list,
  .tasks-list,
  .notifications-list,
  .completed-tasks-list {
    max-height: 250px;
  }
}

/* Animations */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.stat-card,
.collaborators-card,
.tasks-progress-card,
.notifications-card,
.completed-tasks-card {
  animation: fadeIn 0.3s ease-out;
}

.collaborator-item,
.task-item,
.notification-item,
.completed-task-item {
  transition: all 0.2s ease;
}
</style>
