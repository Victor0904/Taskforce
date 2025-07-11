<template>
  <div>
    <Navbar />
    <main>
      <RouterView />
    </main>
  </div>
</template>

<script setup>
import Navbar from './components/Navbar.vue'
</script>
