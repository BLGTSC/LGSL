import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  // CRITICAL: This base path ensures the app works when hosted at csx16.ro/servers/
  // If you move it to the root later, change this to '/'
  base: '/servers/',
  build: {
    outDir: 'dist',
    assetsDir: 'assets',
    sourcemap: false
  }
})