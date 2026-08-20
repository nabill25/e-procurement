import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'path'

export default defineConfig({
  plugins: [react()],
  root: '.',
  server: {
    host: '0.0.0.0',
    port: 5173,
    watch: {
      usePolling: true,
    },
    hmr: {
      host: '127.0.0.1',
      port: 5173,
      protocol: 'ws'
    },
    // Allow CORS for PHP backend
    cors: true,
    // Proxy API requests to PHP backend in dev mode
    proxy: {
      '/api': {
        target: 'http://app:80',
        changeOrigin: true,
      }
    }
  },
  build: {
    outDir: 'public/build',
    emptyOutDir: true,
    // Generate manifest.json for PHP to resolve hashed filenames
    manifest: true,
    rollupOptions: {
      input: 'resources/js/main.jsx',
      output: {
        entryFileNames: '[name].[hash].js',
        chunkFileNames: '[name].[hash].js',
        assetFileNames: '[name].[hash][extname]'
      }
    }
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './resources/js'),
      '@components': path.resolve(__dirname, './resources/js/components'),
      '@pages': path.resolve(__dirname, './resources/js/pages'),
      '@api': path.resolve(__dirname, './resources/js/api')
    }
  }
})
