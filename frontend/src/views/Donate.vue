<template>
  <div class="donate-page">
    <div class="container">
      <header class="donate-header">
        <h1>Soutenir TaskForce</h1>
        <p class="subtitle">
          Aidez-nous à développer et améliorer TaskForce pour une meilleure gestion de projet
        </p>
      </header>

      <div class="donate-content">
        <div class="donation-info">
          <h2>Pourquoi faire un don ?</h2>
          <div class="benefits">
            <div class="benefit-item">
              <div class="benefit-icon">🚀</div>
              <h3>Développement continu</h3>
              <p>Financez de nouvelles fonctionnalités et améliorations</p>
            </div>
            <div class="benefit-item">
              <div class="benefit-icon">🔧</div>
              <h3>Maintenance</h3>
              <p>Assurez la stabilité et la sécurité de la plateforme</p>
            </div>
            <div class="benefit-item">
              <div class="benefit-icon">💡</div>
              <h3>Innovation</h3>
              <p>Explorez de nouvelles technologies et approches</p>
            </div>
          </div>
        </div>

        <div class="payment-section">
          <StripePayment />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import StripePayment from '@/components/StripePayment.vue'

const stats = ref(null)

onMounted(async () => {
  await fetchDonationStats()
})

const fetchDonationStats = async () => {
  try {
    const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
    const response = await fetch(`${apiUrl}/payments/stats`, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      }
    })
    
    if (response.ok) {
      stats.value = await response.json()
    }
  } catch (error) {
    console.error('Erreur lors de la récupération des statistiques:', error)
  }
}
</script>

<style scoped>
.donate-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

.donate-header {
  text-align: center;
  color: white;
  margin-bottom: 3rem;
}

.donate-header h1 {
  font-size: 3rem;
  margin-bottom: 1rem;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.subtitle {
  font-size: 1.2rem;
  opacity: 0.9;
  max-width: 600px;
  margin: 0 auto;
}

.donate-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  margin-bottom: 3rem;
}

.donation-info h2 {
  color: white;
  margin-bottom: 2rem;
  font-size: 2rem;
}

.benefits {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.benefit-item {
  background: rgba(255, 255, 255, 0.1);
  padding: 1.5rem;
  border-radius: 12px;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.benefit-icon {
  font-size: 2rem;
  margin-bottom: 1rem;
}

.benefit-item h3 {
  color: white;
  margin-bottom: 0.5rem;
  font-size: 1.2rem;
}

.benefit-item p {
  color: rgba(255, 255, 255, 0.8);
  margin: 0;
  line-height: 1.5;
}

.payment-section {
  background: white;
  padding: 2rem;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.donation-stats {
  background: rgba(255, 255, 255, 0.1);
  padding: 2rem;
  border-radius: 12px;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  text-align: center;
}

.donation-stats h2 {
  color: white;
  margin-bottom: 2rem;
  font-size: 2rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 2rem;
}

.stat-item {
  text-align: center;
}

.stat-number {
  font-size: 2.5rem;
  font-weight: bold;
  color: white;
  margin-bottom: 0.5rem;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.stat-label {
  color: rgba(255, 255, 255, 0.8);
  font-size: 1rem;
  text-transform: uppercase;
  letter-spacing: 1px;
}

@media (max-width: 768px) {
  .donate-content {
    grid-template-columns: 1fr;
    gap: 2rem;
  }
  
  .donate-header h1 {
    font-size: 2rem;
  }
  
  .subtitle {
    font-size: 1rem;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
}
</style>
