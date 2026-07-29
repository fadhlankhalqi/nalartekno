import { mkdir, writeFile } from "node:fs/promises";
import { basename, extname } from "node:path";

const wpBase = (process.env.WP_BASE_URL || "http://127.0.0.1/dgworld").replace(/\/$/, "");
const wpHostname = new URL(wpBase).hostname;
const wpApiBase = (
  process.env.WP_API_URL ||
  (wpHostname.endsWith(".wordpress.com")
    ? `https://public-api.wordpress.com/wp/v2/sites/${wpHostname}`
    : `${wpBase}/wp-json/wp/v2`)
).replace(/\/$/, "");
const root = new URL("../", import.meta.url);
const mediaDir = new URL("public/media/", root);
const dataFile = new URL("src/data/site.json", root);

await mkdir(mediaDir, { recursive: true });

async function getAll(endpoint) {
  const response = await fetch(`${wpApiBase}/${endpoint}${endpoint.includes("?") ? "&" : "?"}per_page=100&_embed=1`);
  if (!response.ok) throw new Error(`WordPress API ${response.status}: ${endpoint}`);
  return response.json();
}

function text(html = "") {
  return html
    .replace(/<[^>]*>/g, " ")
    .replace(/&#x([0-9a-f]+);/gi, (_, value) => String.fromCodePoint(Number.parseInt(value, 16)))
    .replace(/&#(\d+);/g, (_, value) => String.fromCodePoint(Number.parseInt(value, 10)))
    .replace(/&nbsp;/g, " ")
    .replace(/&amp;/g, "&")
    .replace(/&hellip;/g, "…")
    .replace(/&#8211;|&ndash;/g, "–")
    .replace(/&#8217;|&rsquo;/g, "’")
    .replace(/&quot;/g, '"')
    .replace(/\s+/g, " ")
    .trim();
}

function safeFilename(url, index) {
  const parsed = new URL(url);
  const extension = extname(parsed.pathname).toLowerCase() || ".jpg";
  const stem = basename(parsed.pathname, extension)
    .toLowerCase()
    .replace(/[^a-z0-9-]+/g, "-")
    .replace(/^-|-$/g, "")
    .slice(0, 70);
  return `${stem || `image-${index}`}${extension}`;
}

const mediaCache = new Map();
async function localizeImage(url, index) {
  if (!url || !url.startsWith(wpBase)) return url;
  if (mediaCache.has(url)) return mediaCache.get(url);

  const filename = safeFilename(url, index);
  const destination = new URL(filename, mediaDir);
  const response = await fetch(url);
  if (!response.ok) return "";
  await writeFile(destination, Buffer.from(await response.arrayBuffer()));
  const publicUrl = `/media/${filename}`;
  mediaCache.set(url, publicUrl);
  return publicUrl;
}

async function localizeContent(html, postIndex) {
  const urls = [...html.matchAll(/<img[^>]+src=["']([^"']+)["']/gi)].map((match) => match[1]);
  let output = html;
  for (let i = 0; i < urls.length; i += 1) {
    const local = await localizeImage(urls[i], `${postIndex}-${i}`);
    if (local) output = output.split(urls[i]).join(local);
  }
  return output
    .replaceAll(`${wpBase}/`, "/")
    .replace(/<script[\s\S]*?<\/script>/gi, "");
}

const [rawPosts, rawCategories, rawPages] = await Promise.all([
  getAll("posts?status=publish"),
  getAll("categories?hide_empty=false"),
  getAll("pages?status=publish")
]);

const categoryById = new Map(rawCategories.map((category) => [category.id, category]));
const posts = [];

for (let index = 0; index < rawPosts.length; index += 1) {
  const post = rawPosts[index];
  const featured = post._embedded?.["wp:featuredmedia"]?.[0];
  const author = post._embedded?.author?.[0];
  const featuredUrl = await localizeImage(featured?.source_url || "", index);
  posts.push({
    id: post.id,
    slug: post.slug,
    title: text(post.title.rendered),
    excerpt: text(post.excerpt.rendered),
    content: await localizeContent(post.content.rendered, index),
    date: post.date,
    modified: post.modified,
    author: author?.name || "Tim NalarTekno",
    categories: post.categories.map((id) => {
      const category = categoryById.get(id);
      return category ? { id, name: category.name, slug: category.slug } : null;
    }).filter(Boolean),
    featuredImage: featuredUrl,
    featuredAlt: featured?.alt_text || text(post.title.rendered),
    readingMinutes: Math.max(1, Math.ceil(text(post.content.rendered).split(/\s+/).length / 200))
  });
}

const data = {
  generatedAt: [...posts.map((post) => post.modified), ...rawPages.map((page) => page.modified)]
    .filter(Boolean)
    .sort()
    .at(-1) || "1970-01-01T00:00:00.000Z",
  posts,
  categories: rawCategories.map(({ id, name, slug, count, description }) => ({ id, name, slug, count, description })),
  pages: await Promise.all(rawPages.map(async (page, index) => ({
    id: page.id,
    slug: page.slug,
    title: text(page.title.rendered),
    content: await localizeContent(page.content.rendered, `page-${index}`)
  })))
};

await writeFile(dataFile, `${JSON.stringify(data, null, 2)}\n`);
console.log(`Tersinkron: ${posts.length} artikel, ${data.categories.length} kategori, ${data.pages.length} halaman.`);
