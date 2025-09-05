import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from '../views/Dashboard.vue'
import Admin from '../views/Admin.vue'
import Taches from '@/views/Taches.vue'
import Login from '../views/Login.vue'
import Parametres from '../views/Parametres.vue'
import { jwtDecode } from 'jwt-decode'

const routes = [
  {
    path: '/',
    name: 'Home',
    redirect: '/dashboard'
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    meta: { requiresAuth: true, roles: ['ROLE_USER', 'ROLE_MANAGER', 'ROLE_ADMIN', 'ROLE_CHEF_PROJET'] }
  },
  {
    path: '/admin',
    name: 'Admin',
    component: Admin,
    meta: { requiresAuth: true, roles: ['ROLE_ADMIN', 'ROLE_MANAGER', 'ROLE_CHEF_PROJET'] }
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },

  {
    path: '/reset-password',
    name: 'ResetPassword',
    component: () => import('../views/ResetPassword.vue'),
    meta: { requiresAuth: true, roles: ['ROLE_USER', 'ROLE_MANAGER', 'ROLE_ADMIN', 'ROLE_CHEF_PROJET'] }
  },
  {
    path: '/projets',
    name: 'Projet',
    component: () => import('../views/Projet.vue'),
    meta: { requiresAuth: true, roles: ['ROLE_ADMIN', 'ROLE_MANAGER', 'ROLE_CHEF_PROJET', 'ROLE_USER'] }
  },
  {
    path: '/projets/:id/taches',
    name: 'TachesParProjet',
    component: Taches
  },
  {
    path: '/user',
    name: 'User',
    component: () => import('../views/User.vue'),
    meta: { requiresAuth: true, roles: ['ROLE_USER', 'ROLE_ADMIN'] }
  },
  {
    path: '/parametres',
    name: 'Parametres',
    component: Parametres,
    meta: { requiresAuth: true, roles: ['ROLE_USER', 'ROLE_MANAGER', 'ROLE_ADMIN', 'ROLE_CHEF_PROJET'] }
  },
  {
    path: '/donate',
    name: 'Donate',
    component: () => import('../views/Donate.vue'),
    meta: { requiresAuth: true, roles: ['ROLE_USER', 'ROLE_MANAGER', 'ROLE_ADMIN', 'ROLE_CHEF_PROJET'] }
  },
  {
    path: '/payment/success',
    name: 'PaymentSuccess',
    component: () => import('../views/PaymentSuccess.vue')
  },
  {
    path: '/payment/cancel',
    name: 'PaymentCancel',
    component: () => import('../views/PaymentCancel.vue')
  }

]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

// 🔐 Protection des routes selon rôle
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')

  if (to.meta.requiresAuth) {
    if (!token) {
      return next('/login')
    }

    try {
      const decoded = jwtDecode(token)
      const userRoles = decoded.roles || []

      const isAuthorized = to.meta.roles.some(role => userRoles.includes(role))

      if (!isAuthorized) {
        return next('/dashboard') // Rediriger vers dashboard au lieu de /
      }

      return next()
    } catch (err) {
      console.error('Token invalide :', err)
      return next('/login')
    }
  }

  return next()
})

export default router
