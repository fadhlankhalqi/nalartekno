import { readFile } from "node:fs/promises";

const [input] = process.argv.slice(2);
if (!input) {
  console.error("Pakai: npm run ai:draft -- artikel.json");
  process.exit(1);
}

const wpBase = (process.env.WP_BASE_URL || "http://127.0.0.1/dgworld").replace(/\/$/, "");
const username = process.env.WP_AI_USERNAME;
const appPassword = process.env.WP_AI_APP_PASSWORD?.replace(/\s/g, "");

if (!username || !appPassword) {
  console.error("Isi WP_AI_USERNAME dan WP_AI_APP_PASSWORD di environment.");
  process.exit(1);
}

const article = JSON.parse(await readFile(input, "utf8"));
if (!article.title || !article.content) {
  console.error("JSON wajib memiliki title dan content.");
  process.exit(1);
}

const response = await fetch(`${wpBase}/wp-json/wp/v2/posts`, {
  method: "POST",
  headers: {
    Authorization: `Basic ${Buffer.from(`${username}:${appPassword}`).toString("base64")}`,
    "Content-Type": "application/json"
  },
  body: JSON.stringify({
    title: article.title,
    content: article.content,
    excerpt: article.excerpt || "",
    categories: article.categories || [],
    status: "draft",
    meta: article.meta || {}
  })
});

const result = await response.json();
if (!response.ok) {
  console.error(result);
  process.exit(1);
}

console.log(`Draft dibuat: ${wpBase}/wp-admin/post.php?post=${result.id}&action=edit`);
