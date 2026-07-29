if (process.env.WP_BASE_URL) {
  console.log(`Sinkronisasi konten dari ${process.env.WP_BASE_URL}`);
  await import("./sync-wordpress.mjs");
} else {
  console.log("WP_BASE_URL belum diatur; memakai data konten yang tersimpan.");
}
