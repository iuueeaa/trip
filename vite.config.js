import { defineConfig } from "vite";
import path from "node:path";
import vue from "@vitejs/plugin-vue";
import ViteLiveReload from "vite-plugin-live-reload";

export default defineConfig(({ mode }) => {
  const isDev = mode === "development";

  return {
    publicDir: false,
    plugins: [vue(), ViteLiveReload(["_public/**/*.php", "_public/assets/inc/**/*.php"])],
    resolve: {
      alias: {
        "@icon": path.resolve(__dirname, "_src/icon"),
        "@image": path.resolve(__dirname, "_src/image"),
        "@js": path.resolve(__dirname, "_src/js"),
        "@scss": path.resolve(__dirname, "_src/scss"),
      },
    },
    server: {
      host: "0.0.0.0",
      port: parseInt(process.env.VITE_PORT) || 5173,
      strictPort: false, // ポートが使用中なら自動で別ポートを使用
      watch: {
        ignored: ["**/_public/image/**"], // 画像差分処理の対象外
      },
    },
    css: {
      preprocessorOptions: {
        scss: {
          api: "modern-compiler",
        },
      },
    },
    build: {
      outDir: "_public/assets",
      emptyOutDir: false,
      assetsDir: "",
      manifest: true,
      rollupOptions: {
        input: "_src/main.js",
        output: {
          entryFileNames: "js/index.js",
          assetFileNames: (assetInfo) => (assetInfo.name?.endsWith(".css") ? "css/style.css" : "[name][extname]"),
        },
      },
    },
  };
});
