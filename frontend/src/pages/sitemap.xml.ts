import type { APIRoute } from "astro";
import { visibleCategories } from "../lib/site";
import { getSiteData } from "../lib/wordpress";

function escapeXml(value: string) {
  return value.replace(/[<>&'"]/g, (character) => ({
    "<": "&lt;",
    ">": "&gt;",
    "&": "&amp;",
    "'": "&apos;",
    '"': "&quot;"
  })[character] || character);
}

export const GET: APIRoute = async ({ site }) => {
  const base = site || new URL("https://nalartekno.vercel.app");
  const { posts, categories, pages } = await getSiteData();
  const paths = [
    "/",
    ...posts.map((post) => `/artikel/${post.slug}/`),
    ...visibleCategories(categories).map((category) => `/kategori/${category.slug}/`),
    ...pages.map((page) => `/${page.slug}/`)
  ];
  const urls = [...new Set(paths)].map((path) => `<url><loc>${escapeXml(new URL(path, base).href)}</loc></url>`).join("");

  return new Response(`<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">${urls}</urlset>`, {
    headers: {
      "Content-Type": "application/xml; charset=utf-8",
      "Cache-Control": "public, s-maxage=30, stale-while-revalidate=60"
    }
  });
};
