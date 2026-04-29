import { defineConfig } from 'vite';

export default defineConfig({
  build: {
    manifest: true,
    outDir: 'public/vendor/queue-monitor',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        app: 'resources/assets/js/app.js',
        style: 'resources/assets/scss/app.scss'
      },
      output: {
        entryFileNames: 'js/[name].js',
        chunkFileNames: 'js/[name].js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && assetInfo.name.endsWith('.css')) {
            return 'css/[name][extname]';
          }
          return 'assets/[name][extname]';
        }
      }
    }
  }
});
