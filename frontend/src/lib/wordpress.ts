import fallbackData from "../data/site.json";
import type { Category, Page, Post, SiteData } from "./site";

const wpBase = (
  import.meta.env.WP_BASE_URL ||
  process.env.WP_BASE_URL ||
  "https://nalartekno.wordpress.com"
).replace(/\/$/, "");
const wpHostname = new URL(wpBase).hostname;
const wpApiBase = (
  import.meta.env.WP_API_URL ||
  process.env.WP_API_URL ||
  (wpHostname.endsWith(".wordpress.com")
    ? `https://public-api.wordpress.com/wp/v2/sites/${wpHostname}`
    : `${wpBase}/wp-json/wp/v2`)
).replace(/\/$/, "");

const fallback = fallbackData as SiteData;
let cached: { expires: number; promise: Promise<SiteData> } | undefined;

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

function cleanContent(html = "") {
  return html
    .replaceAll(`${wpBase}/`, "/")
    .replace(/<script[\s\S]*?<\/script>/gi, "");
}

async function getAll(endpoint: string) {
  const separator = endpoint.includes("?") ? "&" : "?";
  const response = await fetch(`${wpApiBase}/${endpoint}${separator}per_page=100&_embed=1`, {
    headers: { Accept: "application/json" }
  });
  if (!response.ok) throw new Error(`WordPress API ${response.status}: ${endpoint}`);
  return response.json();
}

async function loadSiteData(): Promise<SiteData> {
  const [rawPosts, rawCategories, rawPages] = await Promise.all([
    getAll("posts?status=publish"),
    getAll("categories?hide_empty=false"),
    getAll("pages?status=publish")
  ]);

  const categoryById = new Map<number, any>(rawCategories.map((category: any) => [category.id, category]));
  const posts: Post[] = rawPosts.map((post: any) => {
    const featured = post._embedded?.["wp:featuredmedia"]?.[0];
    const author = post._embedded?.author?.[0];
    return {
      id: post.id,
      slug: post.slug,
      title: text(post.title.rendered),
      excerpt: text(post.excerpt.rendered),
      content: cleanContent(post.content.rendered),
      date: post.date,
      modified: post.modified,
      author: author?.name || "Tim NalarTekno",
      categories: post.categories
        .map((id: number) => {
          const category = categoryById.get(id);
          return category ? { id, name: category.name, slug: category.slug } : null;
        })
        .filter(Boolean),
      featuredImage: featured?.source_url || "",
      featuredAlt: featured?.alt_text || text(post.title.rendered),
      readingMinutes: Math.max(1, Math.ceil(text(post.content.rendered).split(/\s+/).length / 200))
    };
  });

  const categories: Category[] = rawCategories.map(
    ({ id, name, slug, count, description }: Category) => ({ id, name, slug, count, description })
  );
  const pages: Page[] = rawPages.map((page: any) => ({
    id: page.id,
    slug: page.slug,
    title: text(page.title.rendered),
    content: cleanContent(page.content.rendered)
  }));

  return { posts, categories, pages };
}

export async function getSiteData(): Promise<SiteData> {
  const now = Date.now();
  if (cached && cached.expires > now) return cached.promise;

  const promise = loadSiteData().catch((error) => {
    console.error("WordPress runtime fetch failed; using the last synced content.", error);
    return fallback;
  });
  cached = { expires: now + 10_000, promise };
  return promise;
}

export function setContentCacheHeaders(response: Response) {
  response.headers.set("Cache-Control", "public, s-maxage=10, stale-while-revalidate=30");
}
