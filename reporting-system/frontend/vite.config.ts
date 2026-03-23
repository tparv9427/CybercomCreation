import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  server: {
    host: '0.0.0.0',
    port: 5173,
    watch: {
      usePolling: true,
    },
    proxy: {
      '/api': {
        target: 'http://app:80',
        changeOrigin: true,
        rewrite: (path) => path.replace(/^\/api/, '/api'), // actually keep /api
      }
    }
  },
  build: {
    outDir: 'dist',
  },
})