# NalarTekno Frontend

Frontend publik statis untuk NalarTekno. WordPress menjadi CMS dan ruang editorial.

## Alur kerja

1. Jalankan Apache dan MySQL melalui XAMPP.
2. Tulis atau review artikel di `http://localhost/dgworld/wp-admin/`.
3. Dari folder `frontend`, jalankan:

```bash
npm run publish
```

Perintah tersebut mengambil artikel berstatus `publish`, menyimpan media secara lokal, lalu membangun situs statis ke folder `dist`.

## Menjalankan lokal

```bash
npm install
npm run sync
npm run dev
```

## AI agent membuat draft

Buat akun WordPress khusus dengan role `Contributor` atau `Author`, lalu buat Application Password. Salin `.env.example` menjadi `.env` dan isi kredensialnya.

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
```

Konten hasil sinkronisasi berada di `src/data/site.json`, sehingga Vercel tidak perlu mengakses WordPress lokal saat build.
