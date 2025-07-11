import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from '../views/Dashboard.vue'
import Admin from '../views/Admin.vue'
import User from '../views/User.vue'
import Login from '../views/Login.vue'
import { jwtDecode } from 'jwt-decode'

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: Dashboard
  },
  {
    path: '/admin',
    name: 'Admin',
    component: Admin,
    meta: { requiresAuth: true, roles: ['ROLE_ADMIN'] }
  },
  {
    path: '/user',
    name: 'User',
    component: User,
    meta: { requiresAuth: true, roles: ['ROLE_USER', 'ROLE_ADMIN'] }
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
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
        return next('/') // ou rediriger vers une page 403 personnalisée
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
