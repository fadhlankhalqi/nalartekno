# NalarTekno

NalarTekno adalah platform belajar teknologi berbahasa Indonesia dengan jalur Basic sampai Expert dan berita digital sebagai konten pendukung.

## Arsitektur

- `wp-content/themes/dgworld-theme`: tema WordPress untuk CMS editorial lokal.
- `frontend`: frontend publik Astro yang dibangun menjadi situs statis untuk Vercel.
- WordPress lokal: admin, review artikel, dan tujuan draft dari AI agent.
- Vercel: situs publik untuk pembaca, tanpa PHP atau database.

## Menjalankan frontend

```bash
cd frontend
npm install
npm run sync
npm run dev
```

WordPress lokal harus aktif ketika menjalankan `npm run sync`. Setelah sinkronisasi, build Vercel tidak membutuhkan akses ke WordPress.

Dokumentasi lengkap tersedia di [`frontend/README.md`](frontend/README.md).
