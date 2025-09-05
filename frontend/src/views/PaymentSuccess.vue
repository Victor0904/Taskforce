<template>
  <div class="payment-success">
    <div class="success-container">
      <div class="success-icon">✅</div>
      <h1>Paiement réussi !</h1>
      <p>Merci pour votre don à TaskForce !</p>
      
      <div v-if="paymentDetails" class="payment-details">
        <h3>Détails du paiement</h3>
        <div class="detail-item">
          <span class="label">Montant :</span>
          <span class="value">{{ paymentDetails.amount }}€</span>
        </div>
        <div class="detail-item">
          <span class="label">Date :</span>
          <span class="value">{{ formatDate(paymentDetails.created) }}</span>
        </div>
        <div class="detail-item">
          <span class="label">Email :</span>
          <span class="value">{{ paymentDetails.customerEmail || 'Non fourni' }}</span>
        </div>
      </div>

      <div class="actions">
        <button @click="goToDashboard" class="btn-primary">
          Retour au tableau de bord
        </button>
        <button @click="goToHome" class="btn-secondary">
          Accueil
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()
const paymentDetails = ref(null)

onMounted(async () => {
  const sessionId = route.query.session_id
  if (sessionId) {
    await fetchPaymentDetails(sessionId)
  }
})

const fetchPaymentDetails = async (sessionId) => {
  try {
    const response = await fetch(`/api/payments/session/${sessionId}`, {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      }
    })
    
    if (response.ok) {
      paymentDetails.value = await response.json()
    }
  } catch (error) {
    console.error('Erreur lors de la récupération des détails:', error)
  }
}

const formatDate = (timestamp) => {
  return new Date(timestamp * 1000).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const goToDashboard = () => {
  router.push('/dashboard')
}

const goToHome = () => {
  router.push('/')
}
</script>

<style scoped>
.payment-success {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem;
}

.success-container {
  background: white;
  padding: 3rem;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  text-align: center;
  max-width: 500px;
  width: 100%;
}

.success-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

h1 {
  color: #2c3e50;
  margin-bottom: 1rem;
  font-size: 2rem;
}

p {
  color: #7f8c8d;
  margin-bottom: 2rem;
  font-size: 1.1rem;
}

.payment-details {
  background: #f8f9fa;
  padding: 1.5rem;
  border-radius: 8px;
  margin-bottom: 2rem;
  text-align: left;
}

.payment-details h3 {
  margin-bottom: 1rem;
  color: #2c3e50;
  text-align: center;
}

.detail-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
  padding: 0.5rem 0;
  border-bottom: 1px solid #e9ecef;
}

.detail-item:last-child {
  border-bottom: none;
}

.label {
  font-weight: 500;
  color: #495057;
}

.value {
  color: #6c757d;
}

.actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
}

.btn-primary, .btn-secondary {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;
  text-decoration: none;
  display: inline-block;
}

.btn-primary {
  background: #667eea;
  color: white;
}

.btn-primary:hover {
  background: #5a6fd8;
  transform: translateY(-2px);
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background: #5a6268;
  transform: translateY(-2px);
}

@media (max-width: 600px) {
  .success-container {
    padding: 2rem;
  }
  
  .actions {
    flex-direction: column;
  }
  
  .btn-primary, .btn-secondary {
    width: 100%;
  }
}
</style>
