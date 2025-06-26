import { createRouter, createWebHistory } from 'vue-router'
import Collaborateurs from '../views/Collaborateurs.vue'
import Affectations from '../views/Affectations.vue'
import Missions from '../views/Missions.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      redirect: '/dashboard', // redirige vers Dashboard
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('../views/Dashboard.vue'),
    },
    {
      path: '/collaborateurs',
      name: 'collaborateurs',
      component: Collaborateurs,
    },
    {
      path: '/affectations',
      name: 'affectations',
      component: Affectations,
    },
    {
      path: '/missions',
      name: 'missions',
      component: Missions,
    },
    
    
  ],
})

export default router
