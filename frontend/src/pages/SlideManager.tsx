import React, { useEffect, useState } from "react";
import { SlidesAPI } from "../services/apiResources";

type Slide = {
  id: number;
  title?: string;
  subtitle?: string;
  image_path: string;
  sort_order: number;
  is_active: boolean;
};

export default function SlideManager() {
  const [slides, setSlides] = useState<Slide[]>([]);
  const [title, setTitle] = useState("");
  const [subtitle, setSubtitle] = useState("");
  const [sortOrder, setSortOrder] = useState(0);
  const [isActive, setIsActive] = useState(true);
  const [file, setFile] = useState<File | null>(null);

  const loadSlides = async () => {
    const res = await SlidesAPI.list();
    setSlides(Array.isArray(res.data) ? res.data : []);
  };

  useEffect(() => {
    loadSlides();
  }, []);

  const createSlide = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!file) return;

    const form = new FormData();
    form.append("title", title);
    form.append("subtitle", subtitle);
    form.append("sort_order", String(sortOrder));
    form.append("is_active", isActive ? "1" : "0");
    form.append("image", file);

    await SlidesAPI.create(form);
    setTitle("");
    setSubtitle("");
    setSortOrder(0);
    setIsActive(true);
    setFile(null);
    await loadSlides();
  };

  const toggle = async (slide: Slide) => {
    const form = new FormData();
    form.append("title", slide.title || "");
    form.append("subtitle", slide.subtitle || "");
    form.append("sort_order", String(slide.sort_order));
    form.append("is_active", slide.is_active ? "0" : "1");

    await SlidesAPI.update(slide.id, form);
    await loadSlides();
  };

  const remove = async (id: number) => {
    await SlidesAPI.delete(id);
    await loadSlides();
  };

  return (
    <div className="container mt-4">
      <h2>Homepage Slides</h2>

      <form className="card p-3 mb-4" onSubmit={createSlide}>
        <input className="form-control mb-2" placeholder="Title" value={title} onChange={(e) => setTitle(e.target.value)} />
        <input className="form-control mb-2" placeholder="Subtitle" value={subtitle} onChange={(e) => setSubtitle(e.target.value)} />
        <input className="form-control mb-2" type="number" value={sortOrder} onChange={(e) => setSortOrder(Number(e.target.value))} />
        <div className="form-check mb-2">
          <input className="form-check-input" type="checkbox" checked={isActive} onChange={(e) => setIsActive(e.target.checked)} id="isActive" />
          <label className="form-check-label" htmlFor="isActive">Active</label>
        </div>
        <input className="form-control mb-3" type="file" accept="image/*" onChange={(e) => setFile(e.target.files?.[0] || null)} required />
        <button className="btn btn-dark" type="submit">Upload Slide</button>
      </form>

      <div className="card p-3">
        {slides.map((slide) => (
          <div key={slide.id} className="d-flex justify-content-between align-items-center border-bottom py-2">
            <div>
              <strong>{slide.title || "Untitled"}</strong>
              <div className="text-muted small">Order: {slide.sort_order} | {slide.is_active ? "Active" : "Inactive"}</div>
            </div>
            <div>
              <button className="btn btn-sm btn-warning me-2" onClick={() => toggle(slide)}>Toggle</button>
              <button className="btn btn-sm btn-danger" onClick={() => remove(slide.id)}>Delete</button>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
