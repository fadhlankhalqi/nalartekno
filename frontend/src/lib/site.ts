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

export const levels = [
  { slug: "tutorial-basic", label: "Basic", title: "Bangun fondasi digital", description: "Kenali internet, browser, email, keamanan dasar, dan cara kerja web.", className: "basic" },
  { slug: "tutorial-intermediate", label: "Intermediate", title: "Mulai membuat produk web", description: "Pelajari HTML, CSS, JavaScript, Git, dan cara menerbitkan proyek pertama.", className: "intermediate" },
  { slug: "tutorial-advanced", label: "Advanced", title: "Bangun aplikasi terhubung", description: "Dalami API, database, autentikasi, pengujian, dan arsitektur aplikasi.", className: "advanced" },
  { slug: "tutorial-expert", label: "Expert", title: "Rancang sistem berskala", description: "Pelajari skalabilitas, observabilitas, keamanan, cloud, dan keputusan arsitektur.", className: "expert" }
];

export function formatDate(value: string) {
  return new Intl.DateTimeFormat("id-ID", { day: "numeric", month: "long", year: "numeric" }).format(new Date(value));
}

export function postCategory(post: Post) {
  return post.categories[0] || { id: 0, name: "DGworld", slug: "" };
}
