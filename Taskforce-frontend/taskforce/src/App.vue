<template>
  <div id="app">
    <!-- Navigation principale -->
    <nav class="navbar">
      <div class="nav-container">
        <div class="nav-content">
          <!-- Logo et titre -->
          <div class="nav-brand">
            <div class="logo">
              <span class="logo-text">T</span>
            </div>
            <h1 class="brand-title">TaskForce</h1>
          </div>
          
          <!-- Menu de navigation -->
          <div class="nav-menu" :class="{ 'nav-menu-open': showMobileMenu }">
            <router-link to="/dashboard" class="nav-link" @click="closeMobileMenu">
              <span class="nav-icon">📊</span>
              Dashboard
            </router-link>
            
            <router-link to="/missions" class="nav-link" @click="closeMobileMenu">
              <span class="nav-icon">📋</span>
              Missions
            </router-link>
            
            <router-link to="/collaborateurs" class="nav-link" @click="closeMobileMenu">
              <span class="nav-icon">👥</span>
              Collaborateurs
            </router-link>
            
            <router-link to="/affectations" class="nav-link" @click="closeMobileMenu">
              <span class="nav-icon">📊</span>
              Affectations
            </router-link>
          </div>

          <!-- Actions utilisateur -->
          <div class="nav-actions">
            <!-- Notifications -->
            <button class="notification-btn">
              <span class="notification-icon">🔔</span>
              <span class="notification-badge"></span>
            </button>

            <!-- Menu utilisateur -->
            <div class="user-menu">
              <button @click="showUserMenu = !showUserMenu" class="user-btn">
                <div class="user-avatar">AD</div>
                <span class="user-name">Admin</span>
                <span class="dropdown-arrow">▼</span>
              </button>

              <!-- Dropdown menu -->
              <div v-if="showUserMenu" class="user-dropdown">
                <a href="#" class="dropdown-item">Profil</a>
                <a href="#" class="dropdown-item">Paramètres</a>
                <hr class="dropdown-divider">
                <a href="#" class="dropdown-item logout">Déconnexion</a>
              </div>
            </div>

            <!-- Menu mobile toggle -->
            <button @click="showMobileMenu = !showMobileMenu" class="mobile-toggle">
              <span class="hamburger"></span>
              <span class="hamburger"></span>
              <span class="hamburger"></span>
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Contenu principal -->
    <main class="main-content">
      <router-view />
    </main>

    <!-- Footer -->
    <footer class="footer">
      <div class="footer-container">
        <div class="footer-content">
          <p class="footer-text">© 2024 TaskForce. Tous droits réservés.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'

const showUserMenu = ref(false)
const showMobileMenu = ref(false)

// Fermer les menus quand on clique ailleurs
const closeMenus = (event) => {
  if (!event.target.closest('.user-menu')) {
    showUserMenu.value = false
  }
}

const closeMobileMenu = () => {
  showMobileMenu.value = false
}

onMounted(() => {
  document.addEventListener('click', closeMenus)
})

onUnmounted(() => {
  document.removeEventListener('click', closeMenus)
})
</script>

<style scoped>
/* === RESET ET BASE === */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  line-height: 1.6;
  color: #333;
  background-color: #f8fafc;
}

#app {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* === NAVIGATION === */
.navbar {
  background: white;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  border-bottom: 1px solid #e2e8f0;
  position: sticky;
  top: 0;
  z-index: 1000;
}

.nav-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.nav-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 70px;
}

/* === LOGO === */
.nav-brand {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo-text {
  color: white;
  font-weight: bold;
  font-size: 1.2rem;
}

.brand-title {
  font-size: 1.5rem;
  font-weight: bold;
  color: #1f2937;
}

/* === MENU NAVIGATION === */
.nav-menu {
  display: flex;
  align-items: center;
  gap: 30px;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  text-decoration: none;
  color: #6b7280;
  font-weight: 500;
  border-radius: 8px;
  transition: all 0.3s ease;
  position: relative;
}

.nav-link:hover {
  color: #3b82f6;
  background: #eff6ff;
}

.nav-link.router-link-active {
  color: #3b82f6;
  background: #eff6ff;
}

.nav-link.router-link-active::after {
  content: '';
  position: absolute;
  bottom: -20px;
  left: 50%;
  transform: translateX(-50%);
  width: 4px;
  height: 4px;
  background: #3b82f6;
  border-radius: 50%;
}

.nav-icon {
  font-size: 1.1rem;
}

/* === ACTIONS UTILISATEUR === */
.nav-actions {
  display: flex;
  align-items: center;
  gap: 15px;
}

.notification-btn {
  position: relative;
  background: none;
  border: none;
  padding: 8px;
  border-radius: 50%;
  cursor: pointer;
  transition: background 0.3s ease;
}

.notification-btn:hover {
  background: #f3f4f6;
}

.notification-icon {
  font-size: 1.2rem;
}

.notification-badge {
  position: absolute;
  top: 5px;
  right: 5px;
  width: 8px;
  height: 8px;
  background: #ef4444;
  border-radius: 50%;
}

/* === MENU UTILISATEUR === */
.user-menu {
  position: relative;
}

.user-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px;
  background: none;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: background 0.3s ease;
}

.user-btn:hover {
  background: #f3f4f6;
}

.user-avatar {
  width: 35px;
  height: 35px;
  background: linear-gradient(135deg, #3b82f6, #8b5cf6);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: bold;
  font-size: 0.9rem;
}

.user-name {
  font-weight: 500;
  color: #374151;
}

.dropdown-arrow {
  font-size: 0.8rem;
  color: #9ca3af;
  transition: transform 0.3s ease;
}

.user-btn:hover .dropdown-arrow {
  transform: rotate(180deg);
}

.user-dropdown {
  position: absolute;
  top: 100%;
  right: 0;
  margin-top: 8px;
  width: 180px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  border: 1px solid #e5e7eb;
  overflow: hidden;
  animation: dropdownFade 0.2s ease;
}

@keyframes dropdownFade {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.dropdown-item {
  display: block;
  padding: 12px 16px;
  text-decoration: none;
  color: #374151;
  font-size: 0.9rem;
  transition: background 0.3s ease;
}

.dropdown-item:hover {
  background: #f9fafb;
}

.dropdown-item.logout {
  color: #ef4444;
}

.dropdown-item.logout:hover {
  background: #fef2f2;
}

.dropdown-divider {
  border: none;
  height: 1px;
  background: #e5e7eb;
  margin: 4px 0;
}

/* === MENU MOBILE === */
.mobile-toggle {
  display: none;
  flex-direction: column;
  gap: 4px;
  background: none;
  border: none;
  padding: 8px;
  cursor: pointer;
}

.hamburger {
  width: 20px;
  height: 2px;
  background: #374151;
  border-radius: 1px;
  transition: all 0.3s ease;
}

/* === CONTENU PRINCIPAL === */
.main-content {
  flex: 1;
  min-height: calc(100vh - 140px);
}

/* === FOOTER === */
.footer {
  background: white;
  border-top: 1px solid #e5e7eb;
  margin-top: auto;
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.footer-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 0;
}

.footer-text {
  color: #6b7280;
  font-size: 0.9rem;
}

.footer-links {
  display: flex;
  gap: 20px;
}

.footer-link {
  color: #6b7280;
  text-decoration: none;
  font-size: 0.9rem;
  transition: color 0.3s ease;
}

.footer-link:hover {
  color: #3b82f6;
}

/* === RESPONSIVE === */
@media (max-width: 768px) {
  .nav-content {
    height: 60px;
  }
  
  .brand-title {
    font-size: 1.3rem;
  }
  
  .nav-menu {
    position: fixed;
    top: 60px;
    left: 0;
    right: 0;
    background: white;
    flex-direction: column;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transform: translateY(-100%);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    gap: 15px;
  }
  
  .nav-menu-open {
    transform: translateY(0);
    opacity: 1;
    visibility: visible;
  }
  
  .nav-link {
    width: 100%;
    justify-content: flex-start;
    padding: 15px 20px;
    border-radius: 8px;
  }
  
  .nav-link.router-link-active::after {
    display: none;
  }
  
  .mobile-toggle {
    display: flex;
  }
  
  .user-name {
    display: none;
  }
  
  .footer-content {
    flex-direction: column;
    gap: 15px;
    text-align: center;
  }
  
  .footer-links {
    gap: 15px;
  }
}

@media (max-width: 480px) {
  .nav-container {
    padding: 0 15px;
  }
  
  .nav-brand {
    gap: 8px;
  }
  
  .logo {
    width: 35px;
    height: 35px;
  }
  
  .brand-title {
    font-size: 1.2rem;
  }
  
  .nav-actions {
    gap: 10px;
  }
  
  .user-dropdown {
    width: 160px;
  }
}

/* === ANIMATIONS === */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.main-content {
  animation: fadeIn 0.5s ease;
}

/* === UTILITAIRES === */
.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  white-space: nowrap;
  border: 0;
}
</style>
