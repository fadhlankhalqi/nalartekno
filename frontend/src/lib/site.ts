import data from "../data/site.json";

export type Category = { id: number; name: string; slug: string; count: number; description: string };
export type Post = {
  id: number;
  slug: string;
  title: string;
  excerpt: string;
  content: string;
  date: string;
  modified: string;
  author: string;
  categories: Pick<Category, "id" | "name" | "slug">[];
  featuredImage: string;
  featuredAlt: string;
  readingMinutes: number;
};

export const posts = data.posts as Post[];
export const categories = data.categories as Category[];
export const pages = data.pages as { id: number; slug: string; title: string; content: string }[];

export const visibleCategories = categories.filter((category) => category.slug !== "uncategorized");

export function formatDate(value: string) {
  return new Intl.DateTimeFormat("id-ID", { day: "numeric", month: "long", year: "numeric" }).format(new Date(value));
}

export function postCategory(post: Post) {
  return post.categories[0] || { id: 0, name: "NalarTekno", slug: "" };
}
