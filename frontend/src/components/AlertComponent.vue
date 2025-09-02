<template>
  <div v-if="show" class="alert-overlay" @click="closeAlert">
    <div class="alert-container" @click.stop>
      <div class="alert-header">
        <div class="alert-icon" :class="alertType">
          <span v-if="alertType === 'success'">✅</span>
          <span v-else-if="alertType === 'error'">❌</span>
          <span v-else-if="alertType === 'warning'">⚠️</span>
          <span v-else>ℹ️</span>
        </div>
        <h3 class="alert-title">{{ title }}</h3>
        <button @click="closeAlert" class="alert-close">&times;</button>
      </div>
      
      <div class="alert-body">
        <p class="alert-message">{{ message }}</p>
        <div v-if="details" class="alert-details">
          <h4>Détails :</h4>
          <pre class="alert-details-text">{{ details }}</pre>
        </div>
        <div v-if="suggestion" class="alert-suggestion">
          <h4>Suggestion :</h4>
          <p>{{ suggestion }}</p>
        </div>
      </div>
      
      <div class="alert-footer">
        <button @click="closeAlert" class="btn btn-primary">
          Compris
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  title: {
    type: String,
    default: 'Information'
  },
  message: {
    type: String,
    required: true
  },
  details: {
    type: String,
    default: ''
  },
  suggestion: {
    type: String,
    default: ''
  },
  autoClose: {
    type: Boolean,
    default: false
  },
  autoCloseDelay: {
    type: Number,
    default: 5000
  }
})

const emit = defineEmits(['close'])

const alertType = ref(props.type)

// Mettre à jour le type d'alerte quand les props changent
watch(() => props.type, (newType) => {
  alertType.value = newType
})

// Auto-fermeture si activée
watch(() => props.show, (isShowing) => {
  if (isShowing && props.autoClose) {
    setTimeout(() => {
      closeAlert()
    }, props.autoCloseDelay)
  }
})

const closeAlert = () => {
  emit('close')
}
</script>

<style scoped>
.alert-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  padding: 1rem;
}

.alert-container {
  background: white;
  border-radius: 12px;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(-20px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.alert-header {
  display: flex;
  align-items: center;
  padding: 1.5rem 1.5rem 0 1.5rem;
  gap: 1rem;
}

.alert-icon {
  width: 3rem;
  height: 3rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  flex-shrink: 0;
}

.alert-icon.success {
  background: #d1fae5;
  color: #065f46;
}

.alert-icon.error {
  background: #fee2e2;
  color: #991b1b;
}

.alert-icon.warning {
  background: #fef3c7;
  color: #92400e;
}

.alert-icon.info {
  background: #dbeafe;
  color: #1e3a8a;
}

.alert-title {
  flex: 1;
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: #1f2937;
}

.alert-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #6b7280;
  cursor: pointer;
  padding: 0;
  width: 2rem;
  height: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: all 0.2s ease;
}

.alert-close:hover {
  background: #f3f4f6;
  color: #374151;
}

.alert-body {
  padding: 1.5rem;
}

.alert-message {
  margin: 0 0 1rem 0;
  color: #374151;
  line-height: 1.6;
  font-size: 1rem;
}

.alert-details {
  margin: 1rem 0;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 8px;
  border-left: 4px solid #e5e7eb;
}

.alert-details h4 {
  margin: 0 0 0.5rem 0;
  color: #374151;
  font-size: 0.875rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.alert-details-text {
  margin: 0;
  color: #6b7280;
  font-size: 0.875rem;
  line-height: 1.5;
  white-space: pre-wrap;
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
}

.alert-suggestion {
  margin: 1rem 0;
  padding: 1rem;
  background: #eff6ff;
  border-radius: 8px;
  border-left: 4px solid #3b82f6;
}

.alert-suggestion h4 {
  margin: 0 0 0.5rem 0;
  color: #1e40af;
  font-size: 0.875rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.alert-suggestion p {
  margin: 0;
  color: #1e40af;
  font-size: 0.875rem;
  line-height: 1.5;
}

.alert-footer {
  padding: 0 1.5rem 1.5rem 1.5rem;
  display: flex;
  justify-content: flex-end;
}

.btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  line-height: 1;
}

.btn-primary {
  background: #3b82f6;
  color: white;
}

.btn-primary:hover {
  background: #2563eb;
  transform: translateY(-1px);
}

.btn-primary:active {
  transform: translateY(0);
}

/* Responsive */
@media (max-width: 640px) {
  .alert-overlay {
    padding: 0.5rem;
  }
  
  .alert-container {
    max-height: 95vh;
  }
  
  .alert-header {
    padding: 1rem 1rem 0 1rem;
  }
  
  .alert-body {
    padding: 1rem;
  }
  
  .alert-footer {
    padding: 0 1rem 1rem 1rem;
  }
  
  .alert-title {
    font-size: 1.125rem;
  }
}
</style>
