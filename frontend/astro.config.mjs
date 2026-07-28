import { defineConfig } from "astro/config";
import sitemap from "@astrojs/sitemap";

export default defineConfig({
  output: "static",
  site: process.env.PUBLIC_SITE_URL || "https://nalartekno.vercel.app",
  integrations: [sitemap()],
  build: { format: "directory" },
  vite: { server: { fs: { strict: true } } }
});
