<template>
  <header>

    <div class="wrapper">
      <h1>TaskForce - Gestion des missions</h1>

      <nav>
        <RouterLink to="/dashboard">🏠 Tableau de bord</RouterLink>
        <RouterLink to="/collaborateurs">Collaborateurs</RouterLink>
        <RouterLink to="/affectations">📋 Affectations</RouterLink>
      </nav>
    </div>
  </header>

  <RouterView />
</template>
