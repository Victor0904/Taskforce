# Composant AlertComponent

## Description
Le composant `AlertComponent` est un système d'alerte réutilisable qui permet d'afficher des messages d'erreur, de succès, d'avertissement ou d'information de manière élégante et cohérente dans l'application.

## Utilisation

### Import du composant
```javascript
import AlertComponent from '../components/AlertComponent.vue'
```

### Variables réactives nécessaires
```javascript
const showAlert = ref(false)
const alertType = ref('error')
const alertTitle = ref('Erreur')
const alertMessage = ref('')
const alertDetails = ref('')
const alertSuggestion = ref('')
```

### Template
```vue
<AlertComponent
  :show="showAlert"
  :type="alertType"
  :title="alertTitle"
  :message="alertMessage"
  :details="alertDetails"
  :suggestion="alertSuggestion"
  @close="closeAlert"
/>
```

### Fonctions utilitaires
```javascript
// Fonction pour fermer l'alerte
const closeAlert = () => {
  showAlert.value = false
  alertMessage.value = ''
  alertDetails.value = ''
  alertSuggestion.value = ''
}

// Fonction utilitaire pour afficher des alertes
const showAlertMessage = (type, title, message, details = '', suggestion = '') => {
  showAlert.value = true
  alertType.value = type
  alertTitle.value = title
  alertMessage.value = message
  alertDetails.value = details
  alertSuggestion.value = suggestion
}
```

## Types d'alertes

### 1. Erreur (error)
```javascript
showAlertMessage('error', 'Erreur', 'Message d\'erreur', 'Détails techniques', 'Suggestion')
```

### 2. Succès (success)
```javascript
showAlertMessage('success', 'Succès', 'Opération réussie')
```

### 3. Avertissement (warning)
```javascript
showAlertMessage('warning', 'Attention', 'Message d\'avertissement')
```

### 4. Information (info)
```javascript
showAlertMessage('info', 'Information', 'Message informatif')
```

## Exemples d'utilisation

### Erreur d'assignation de tâche
```javascript
// Dans la gestion d'erreur d'assignation
if (error.response?.status === 422) {
  const errorData = error.response.data
  showAlertMessage(
    'error',
    'Impossible d\'assigner la tâche',
    errorData.message || 'Aucun collaborateur disponible pour cette tâche',
    errorData.details || '',
    errorData.suggestion || 'Vérifiez qu\'il y a des collaborateurs disponibles avec la compétence requise'
  )
}
```

### Succès de création
```javascript
// Après une création réussie
showAlertMessage(
  'success',
  'Succès',
  'Tâche créée et assignée avec succès'
)
```

### Avertissement de validation
```javascript
// Pour les erreurs de validation
showAlertMessage(
  'warning',
  'Données manquantes',
  'Veuillez remplir tous les champs obligatoires',
  '',
  'Vérifiez que tous les champs marqués d\'un astérisque sont remplis'
)
```

## Props du composant

| Prop | Type | Défaut | Description |
|------|------|--------|-------------|
| `show` | Boolean | false | Affiche ou masque l'alerte |
| `type` | String | 'info' | Type d'alerte: 'success', 'error', 'warning', 'info' |
| `title` | String | 'Information' | Titre de l'alerte |
| `message` | String | - | Message principal (obligatoire) |
| `details` | String | '' | Détails techniques (optionnel) |
| `suggestion` | String | '' | Suggestion d'action (optionnel) |
| `autoClose` | Boolean | false | Fermeture automatique |
| `autoCloseDelay` | Number | 5000 | Délai de fermeture automatique en ms |

## Événements

| Événement | Description |
|-----------|-------------|
| `@close` | Émis quand l'utilisateur ferme l'alerte |

## Styles

Le composant utilise des classes CSS personnalisées avec des couleurs cohérentes :
- **Success** : Vert (#065f46)
- **Error** : Rouge (#991b1b)
- **Warning** : Orange (#92400e)
- **Info** : Bleu (#1e3a8a)

## Responsive

Le composant est entièrement responsive et s'adapte aux écrans mobiles avec des ajustements de padding et de taille de police.

## Accessibilité

- Support du clavier (Echap pour fermer)
- Contraste de couleurs respecté
- Icônes expressives pour chaque type d'alerte
- Focus visible sur les boutons
