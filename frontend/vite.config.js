import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    },
  },
  // Configuration pour la compression et l'optimisation
  build: {
    // Activer la compression gzip
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: true,
        drop_debugger: true,
      },
    },
    // Optimiser les chunks
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ['vue', 'vue-router', 'pinia'],
          axios: ['axios'],
          jwt: ['jwt-decode']
        }
      }
    },
    // Activer la compression des assets
    assetsInlineLimit: 4096,
    // Optimiser les CSS
    cssCodeSplit: true,
  },
  // Configuration du serveur de développement
  server: {
    // Activer la compression gzip en développement
    middlewareMode: false,
    hmr: {
      overlay: false
    }
  },
  // Optimisations pour la production
  esbuild: {
    drop: ['console', 'debugger'],
  },
  test: {
    globals: true,
    environment: 'jsdom',
    setupFiles: ['./src/test/setup.js']
  }
})