import React from "react";
import { Nav } from "react-bootstrap";
import { Link, useLocation } from "react-router-dom";

export default function Sidebar() {
  const location = useLocation();

  const links = [
    { path: "/dashboard", label: "Dashboard" },
    { path: "/offerings", label: "Offerings" },
    { path: "/members", label: "Members" },
    { path: "/branches", label: "Branches" },
  ];

  return (
    <div className="sidebar bg-light vh-100 p-3">
      <h5 className="mb-4">Menu</h5>
      <Nav className="flex-column">
        {links.map((link) => (
          <Nav.Link
            as={Link}
            to={link.path}
            key={link.path}
            active={location.pathname === link.path}
          >
            {link.label}
          </Nav.Link>
        ))}
      </Nav>
    </div>
  );
}
