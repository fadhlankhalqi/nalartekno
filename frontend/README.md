# NalarTekno Frontend

Frontend publik SSR untuk NalarTekno. WordPress.com menjadi CMS dan sumber konten.

## Alur kerja

1. Tulis atau review artikel di `https://nalartekno.wordpress.com/wp-admin/`.
2. Terbitkan artikel di WordPress.
3. Frontend membaca perubahan melalui WordPress API. Cache HTML berlaku 10 detik.

## Menjalankan lokal

```bash
npm install
npm run dev
```

Gunakan `npm run sync` hanya untuk memperbarui cadangan `src/data/site.json`.

## AI agent membuat draft

Skrip draft dengan Application Password ditujukan untuk WordPress self-hosted. WordPress.com memerlukan OAuth2 dan akan dikonfigurasi terpisah sebelum AI agent diberi akses tulis.

Format input:

```json
{
  "title": "Judul artikel",
  "excerpt": "Ringkasan singkat",
  "content": "<p>Isi artikel dengan sumber yang sudah diperiksa.</p>",
  "categories": [3]
}
```

Jalankan:

```bash
npm run ai:draft -- artikel.json
```

Script selalu membuat status `draft`. Review dan terbitkan melalui WordPress.

## Deploy Vercel

Import folder `frontend` sebagai root directory proyek Vercel. Build command dan output directory sudah ditetapkan di `vercel.json`.

Set environment variable:

```text
PUBLIC_SITE_URL=https://domain-kamu.vercel.app
WP_BASE_URL=https://nalartekno.wordpress.com
```

Route artikel, kategori, halaman, pencarian, dan sitemap dirender saat diminta. Jika WordPress API gagal, frontend memakai data terakhir dari `src/data/site.json`.
