<template>
  <div class="stripe-payment">
    <div v-if="loading" class="loading">
      <div class="spinner"></div>
      <p>Préparation du paiement...</p>
    </div>

    <div v-else-if="error" class="error">
      <p>{{ error }}</p>
      <button @click="resetPayment" class="btn-retry">Réessayer</button>
    </div>

    <div v-else class="payment-form">
      <h3>Faire un don à TaskForce</h3>
      
      <div class="amount-selection">
        <label>Montant du don :</label>
        <div class="amount-buttons">
          <button 
            v-for="amount in suggestedAmounts" 
            :key="amount"
            @click="selectAmount(amount)"
            :class="{ active: selectedAmount === amount }"
            class="amount-btn"
          >
            {{ amount }}€
          </button>
        </div>
        
        <div class="custom-amount">
          <input 
            v-model="customAmount"
            type="number"
            placeholder="Montant personnalisé"
            min="1"
            step="0.01"
            @input="selectCustomAmount"
          />
          <span>€</span>
        </div>
      </div>

      <div class="donation-type" style="display: none;">
        <label>Type de don :</label>
        <select v-model="donationType">
          <option value="general">Général</option>
          <option value="development">Développement</option>
          <option value="maintenance">Maintenance</option>
          <option value="features">Nouvelles fonctionnalités</option>
        </select>
      </div>

      <button 
        @click="createCheckoutSession"
        :disabled="!canProceed"
        class="btn-pay"
      >
        Payer {{ finalAmount }}€
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

// État réactif
const loading = ref(false)
const error = ref(null)
const selectedAmount = ref(null)
const customAmount = ref('')
const donationType = ref('general')

// Montants suggérés
const suggestedAmounts = [5, 10, 25, 50, 100]

// Montant final calculé
const finalAmount = computed(() => {
  return selectedAmount.value || parseFloat(customAmount.value) || 0
})

// Peut procéder au paiement
const canProceed = computed(() => {
  return finalAmount.value > 0
})

// Sélectionner un montant suggéré
const selectAmount = (amount) => {
  selectedAmount.value = amount
  customAmount.value = ''
}

// Sélectionner un montant personnalisé
const selectCustomAmount = () => {
  selectedAmount.value = null
}

// Créer une session de paiement
const createCheckoutSession = async () => {
  if (!canProceed.value) return

  loading.value = true
  error.value = null

  try {
    const apiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
    const response = await fetch(`${apiUrl}/payments/create-checkout-session`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${localStorage.getItem('token')}`
      },
      body: JSON.stringify({
        amount: finalAmount.value,
        type: donationType.value
      })
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.error || 'Erreur lors de la création du paiement')
    }

    // Rediriger vers Stripe Checkout
    window.location.href = data.url

  } catch (err) {
    error.value = err.message
    loading.value = false
  }
}

// Réinitialiser le formulaire
const resetPayment = () => {
  loading.value = false
  error.value = null
  selectedAmount.value = null
  customAmount.value = ''
}
</script>

<style scoped>
.stripe-payment {
  max-width: 500px;
  margin: 0 auto;
  padding: 2rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.loading {
  text-align: center;
  padding: 2rem;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error {
  text-align: center;
  color: #e74c3c;
  padding: 1rem;
}

.btn-retry {
  background: #667eea;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  cursor: pointer;
  margin-top: 1rem;
}

.payment-form h3 {
  text-align: center;
  margin-bottom: 2rem;
  color: #333;
}

.amount-selection {
  margin-bottom: 1.5rem;
}

.amount-selection label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.amount-buttons {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.amount-btn {
  padding: 0.75rem;
  border: 2px solid #e1e5e9;
  background: white;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.2s;
}

.amount-btn:hover {
  border-color: #667eea;
}

.amount-btn.active {
  background: #667eea;
  color: white;
  border-color: #667eea;
}

.custom-amount {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.custom-amount input {
  flex: 1;
  padding: 0.75rem;
  border: 2px solid #e1e5e9;
  border-radius: 4px;
  font-size: 1rem;
}

.donation-type {
  margin-bottom: 2rem;
}

.donation-type label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
}

.donation-type select {
  width: 100%;
  padding: 0.75rem;
  border: 2px solid #e1e5e9;
  border-radius: 4px;
  font-size: 1rem;
}

.btn-pay {
  width: 100%;
  padding: 1rem;
  background: #667eea;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1.1rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
}

.btn-pay:hover:not(:disabled) {
  background: #5a6fd8;
}

.btn-pay:disabled {
  background: #ccc;
  cursor: not-allowed;
}
</style>
