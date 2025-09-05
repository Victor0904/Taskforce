<template>
  <div class="payment-cancel">
    <div class="cancel-container">
      <div class="cancel-icon">❌</div>
      <h1>Paiement annulé</h1>
      <p>Votre paiement a été annulé. Aucun montant n'a été débité.</p>
      
      <div class="info-box">
        <h3>Que s'est-il passé ?</h3>
        <ul>
          <li>Vous avez fermé la fenêtre de paiement</li>
          <li>Vous avez cliqué sur "Retour"</li>
          <li>Une erreur technique s'est produite</li>
        </ul>
      </div>

      <div class="actions">
        <button @click="retryPayment" class="btn-primary">
          Réessayer le paiement
        </button>
        <button @click="goToDashboard" class="btn-secondary">
          Retour au tableau de bord
        </button>
        <button @click="goToHome" class="btn-outline">
          Accueil
        </button>
      </div>

      <div class="help">
        <h4>Besoin d'aide ?</h4>
        <p>Si vous rencontrez des difficultés, n'hésitez pas à nous contacter.</p>
        <a href="mailto:support@taskforce.com" class="contact-link">
          support@taskforce.com
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()

const retryPayment = () => {
  // Retourner à la page de paiement
  router.push('/donate')
}

const goToDashboard = () => {
  router.push('/dashboard')
}

const goToHome = () => {
  router.push('/')
}
</script>

<style scoped>
.payment-cancel {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
  padding: 2rem;
}

.cancel-container {
  background: white;
  padding: 3rem;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
  text-align: center;
  max-width: 500px;
  width: 100%;
}

.cancel-icon {
  font-size: 4rem;
  margin-bottom: 1rem;
}

h1 {
  color: #e74c3c;
  margin-bottom: 1rem;
  font-size: 2rem;
}

p {
  color: #7f8c8d;
  margin-bottom: 2rem;
  font-size: 1.1rem;
}

.info-box {
  background: #f8f9fa;
  padding: 1.5rem;
  border-radius: 8px;
  margin-bottom: 2rem;
  text-align: left;
}

.info-box h3 {
  margin-bottom: 1rem;
  color: #2c3e50;
  text-align: center;
}

.info-box ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.info-box li {
  padding: 0.5rem 0;
  border-bottom: 1px solid #e9ecef;
  position: relative;
  padding-left: 1.5rem;
}

.info-box li:last-child {
  border-bottom: none;
}

.info-box li::before {
  content: "•";
  color: #e74c3c;
  font-weight: bold;
  position: absolute;
  left: 0;
}

.actions {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-bottom: 2rem;
}

.btn-primary, .btn-secondary, .btn-outline {
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

.btn-outline {
  background: transparent;
  color: #667eea;
  border: 2px solid #667eea;
}

.btn-outline:hover {
  background: #667eea;
  color: white;
  transform: translateY(-2px);
}

.help {
  border-top: 1px solid #e9ecef;
  padding-top: 1.5rem;
}

.help h4 {
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.help p {
  color: #6c757d;
  font-size: 0.9rem;
  margin-bottom: 0.5rem;
}

.contact-link {
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
}

.contact-link:hover {
  text-decoration: underline;
}

@media (min-width: 600px) {
  .actions {
    flex-direction: row;
    justify-content: center;
  }
}
</style>
