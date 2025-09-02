# Taskforce

Application de gestion de tâches avec backend Symfony et frontend Vue.js.

## Structure du projet

- `backend/` - API Symfony avec Doctrine ORM
- `frontend/` - Interface utilisateur Vue.js
- `Vaskforce/` - Collection de tests API Bruno

## Installation

### Backend (Symfony)

```bash
cd backend
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### Frontend (Vue.js)

```bash
cd frontend
npm install
npm run dev
```

## Tests

### Backend

```bash
cd backend
php bin/phpunit
```

### Frontend

```bash
cd frontend
npm run lint
npm run build
```

## CI

![CI](https://github.com/VOTRE_USERNAME/VOTRE_REPO/actions/workflows/ci.yml/badge.svg)

Le workflow CI exécute automatiquement :

- Tests PHPUnit pour le backend Symfony
- Lint et build pour le frontend Vue.js
- Tests sur les branches main, master, develop et les pull requests
