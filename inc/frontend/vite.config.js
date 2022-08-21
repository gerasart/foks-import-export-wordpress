import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { quasar, transformAssetUrls } from '@quasar/vite-plugin'
import { resolve } from "path";
import requireTransform from "vite-plugin-require-transform";

export default defineConfig({
  base: './',
  resolve: {
    alias: {
      "@": resolve(__dirname, "src"),
    },
  },
  plugins: [
    vue({
      template: { transformAssetUrls }
    }),
    quasar({
      autoImportComponentCase: "combined",
      sassVariables: "@/assets/styles/plugins/quasar-variables.sass",
    }),
    requireTransform({}),
  ],
  build: {
    rollupOptions: {
      output: {
        entryFileNames: 'app.js',
        assetFileNames: "[name].[ext]",
      }
    }
  }
});
