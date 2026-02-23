import React, { useEffect, useMemo, useState } from "react";
import { Link } from "react-router-dom";
import { SlidesAPI } from "../services/apiResources";
import "../styles/home.css";

type Slide = {
  id: number;
  title?: string;
  subtitle?: string;
  image_path: string;
};

const imageUrl = (path: string) => {
  const base = (process.env.REACT_APP_API_URL || "http://127.0.0.1:8000").replace(/\/+$/, "");
  return `${base}/storage/${path}`;
};

export default function Home() {
  const [slides, setSlides] = useState<Slide[]>([]);
  const [index, setIndex] = useState(0);

  useEffect(() => {
    const load = async () => {
      try {
        const res = await SlidesAPI.listPublic();
        setSlides(Array.isArray(res.data) ? res.data : []);
      } catch {
        setSlides([]);
      }
    };

    load();
  }, []);

  useEffect(() => {
    if (slides.length <= 1) return;

    const timer = window.setInterval(() => {
      setIndex((current) => (current + 1) % slides.length);
    }, 5000);

    return () => window.clearInterval(timer);
  }, [slides.length]);

  const activeSlide = useMemo(() => slides[index], [slides, index]);

  return (
    <div className="home-page">
      <nav className="home-nav">
        <div className="brand">RGC Church</div>
        <div className="nav-links">
          <a href="#about">About</a>
          <Link to="/login">Login</Link>
          <Link to="/register" className="nav-register">Register</Link>
        </div>
      </nav>

      <header className="hero">
        <div className="hero-overlay" />

        {activeSlide ? (
          <img className="hero-image hero-fade" src={imageUrl(activeSlide.image_path)} alt={activeSlide.title || "Church slide"} />
        ) : (
          <div className="hero-image hero-fallback" />
        )}

        <div className="hero-content">
          <h1>{activeSlide?.title || "Welcome to RGC Tanzania Church Management"}</h1>
          <p>
            {activeSlide?.subtitle ||
              "One secure platform for regions, districts, branches, offerings, attendance, and member collaboration."}
          </p>

          <div className="hero-actions">
            <Link to="/login" className="btn btn-login">Login</Link>
            <Link to="/register" className="btn btn-register">Register</Link>
          </div>
        </div>
      </header>

      <section id="about" className="home-section home-about">
        <h2>About The Ministry</h2>
        <p>
          This platform supports super admins, admins, and members across mainland Tanzania and Zanzibar with role-based access,
          branch operations, and secure records.
        </p>
      </section>

      <footer className="home-footer">
        <div>RGC Church Management System</div>
        <div>Serving regions, districts, and branches across Tanzania</div>
      </footer>
    </div>
  );
}
