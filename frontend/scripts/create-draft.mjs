import { access, readFile } from "node:fs/promises";
import { basename, dirname, isAbsolute, resolve } from "node:path";
import { fileURLToPath } from "node:url";

const scriptDir = dirname(fileURLToPath(import.meta.url));
const frontendDir = resolve(scriptDir, "..");
const projectDir = resolve(frontendDir, "..");

async function loadEnv() {
  for (const envPath of [resolve(projectDir, ".env"), resolve(frontendDir, ".env")]) {
    try {
      const source = await readFile(envPath, "utf8");
      for (const rawLine of source.split(/\r?\n/)) {
        const line = rawLine.trim();
        if (!line || line.startsWith("#")) continue;
        const match = line.match(/^(?:export\s+)?([A-Za-z_][A-Za-z0-9_]*)\s*=\s*(.*)$/);
        if (!match) continue;
        let value = match[2].trim();
        if (
          (value.startsWith('"') && value.endsWith('"')) ||
          (value.startsWith("'") && value.endsWith("'"))
        ) {
          value = value.slice(1, -1);
        }
        if (!(match[1] in process.env)) process.env[match[1]] = value;
      }
      return envPath;
    } catch (error) {
      if (error.code !== "ENOENT") throw error;
    }
  }
  return null;
}

const envPath = await loadEnv();
const [input] = process.argv.slice(2);
if (!input) {
  console.error("Pakai: npm run ai:draft -- ../content/drafts/artikel.json");
  process.exit(1);
}

const wpBase = (process.env.WP_BASE_URL || "https://nalartekno.wordpress.com").replace(/\/$/, "");
const site = process.env.WPCOM_SITE || new URL(wpBase).host;
const username = process.env.WPCOM_USERNAME || process.env.WP_AI_USERNAME;
const appPassword = (
  process.env.WPCOM_APP_PASSWORD ||
  process.env.WP_AI_APP_PASSWORD ||
  ""
).replace(/\s/g, "");
const clientId = process.env.WPCOM_CLIENT_ID;
const clientSecret = process.env.WPCOM_CLIENT_SECRET;
let accessToken = process.env.WPCOM_ACCESS_TOKEN;

if (!accessToken && (!username || !appPassword || !clientId || !clientSecret)) {
  console.error(
    `Konfigurasi belum lengkap${envPath ? ` di ${envPath}` : ""}.\n` +
      "Isi WPCOM_USERNAME, WPCOM_APP_PASSWORD, WPCOM_CLIENT_ID, dan WPCOM_CLIENT_SECRET,\n" +
      "atau cukup WPCOM_ACCESS_TOKEN bila kamu sudah memiliki token OAuth."
  );
  process.exit(1);
}

const articlePath = resolve(process.cwd(), input);
const article = JSON.parse(await readFile(articlePath, "utf8"));
if (!article.title || !article.content) {
  console.error("JSON wajib memiliki title dan content.");
  process.exit(1);
}

async function parseResponse(response) {
  const text = await response.text();
  let result;
  try {
    result = JSON.parse(text);
  } catch {
    result = { message: text || response.statusText };
  }
  if (!response.ok) {
    const detail = result.message || result.error_description || result.error || JSON.stringify(result);
    throw new Error(`WordPress API ${response.status}: ${detail}`);
  }
  return result;
}

if (!accessToken) {
  const tokenBody = new URLSearchParams({
    client_id: clientId,
    client_secret: clientSecret,
    grant_type: "password",
    username,
    password: appPassword
  });
  const tokenResponse = await fetch("https://public-api.wordpress.com/oauth2/token", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: tokenBody
  });
  const tokenResult = await parseResponse(tokenResponse);
  accessToken = tokenResult.access_token;
}

const apiBase =
  process.env.WPCOM_API_URL ||
  `https://public-api.wordpress.com/wp/v2/sites/${encodeURIComponent(site)}`;
const authHeaders = { Authorization: `Bearer ${accessToken}` };

async function ensureTags(names = []) {
  const ids = [];
  for (const name of names) {
    const searchResponse = await fetch(
      `${apiBase}/tags?search=${encodeURIComponent(name)}&per_page=100`,
      { headers: authHeaders }
    );
    const matches = await parseResponse(searchResponse);
    const exact = matches.find((tag) => tag.name.toLocaleLowerCase() === name.toLocaleLowerCase());
    if (exact) {
      ids.push(exact.id);
      continue;
    }
    const createResponse = await fetch(`${apiBase}/tags`, {
      method: "POST",
      headers: { ...authHeaders, "Content-Type": "application/json" },
      body: JSON.stringify({ name })
    });
    ids.push((await parseResponse(createResponse)).id);
  }
  return ids;
}

async function uploadFeaturedImage(relativePath, altText = "") {
  if (!relativePath) return undefined;
  const candidates = [
    isAbsolute(relativePath) ? relativePath : resolve(projectDir, relativePath),
    resolve(dirname(articlePath), relativePath),
    resolve(frontendDir, relativePath)
  ];
  let imagePath;
  for (const candidate of candidates) {
    try {
      await access(candidate);
      imagePath = candidate;
      break;
    } catch {
      // Try the next sensible base directory.
    }
  }
  if (!imagePath) throw new Error(`Gambar tidak ditemukan: ${relativePath}`);

  const bytes = await readFile(imagePath);
  const extension = imagePath.split(".").pop()?.toLowerCase();
  const mimeTypes = {
    jpg: "image/jpeg",
    jpeg: "image/jpeg",
    png: "image/png",
    webp: "image/webp",
    gif: "image/gif"
  };
  const form = new FormData();
  form.append("file", new Blob([bytes], { type: mimeTypes[extension] || "application/octet-stream" }), basename(imagePath));
  if (altText) form.append("alt_text", altText);

  const uploadResponse = await fetch(`${apiBase}/media`, {
    method: "POST",
    headers: authHeaders,
    body: form
  });
  return (await parseResponse(uploadResponse)).id;
}

try {
  console.log("Menyiapkan tag...");
  const tagIds = await ensureTags(article.tags);
  console.log("Mengunggah featured image...");
  const featuredMedia = await uploadFeaturedImage(
    article.featured_image,
    article.featured_image_alt
  );
  console.log("Membuat draft WordPress...");
  const response = await fetch(`${apiBase}/posts`, {
    method: "POST",
    headers: { ...authHeaders, "Content-Type": "application/json" },
    body: JSON.stringify({
      title: article.title,
      slug: article.slug,
      content: article.content,
      excerpt: article.excerpt || "",
      categories: article.categories || [],
      tags: tagIds,
      featured_media: featuredMedia,
      status: "draft"
    })
  });
  const result = await parseResponse(response);
  console.log(`Draft berhasil dibuat: ${wpBase}/wp-admin/post.php?post=${result.id}&action=edit`);
} catch (error) {
  console.error(error.message);
  process.exit(1);
}
