# NalarTekno

NalarTekno adalah platform berita dan pembelajaran teknologi berbahasa Indonesia.

## Arsitektur

- `wp-content/themes/dgworld-theme`: tema WordPress lama untuk pengembangan lokal.
- `frontend`: frontend Astro SSR yang berjalan sebagai Vercel Function.
- WordPress.com: CMS, admin, dan sumber konten publik.
- Vercel: membaca WordPress ketika halaman diminta, dengan cache pendek dan cadangan data terakhir.

## Menjalankan frontend

```bash
cd frontend
npm install
npm run dev
```

Atur `WP_BASE_URL=https://nalartekno.wordpress.com`. Perintah `npm run sync` tetap tersedia untuk memperbarui data cadangan secara manual.

Dokumentasi lengkap tersedia di [`frontend/README.md`](frontend/README.md).
