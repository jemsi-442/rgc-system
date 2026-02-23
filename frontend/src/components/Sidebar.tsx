import React, { useContext } from "react";
import { Nav } from "react-bootstrap";
import { Link, useLocation } from "react-router-dom";
import { AuthContext } from "../context/AuthContext";

export default function Sidebar() {
  const location = useLocation();
  const authContext = useContext(AuthContext);

  const links = [
    { path: "/dashboard", label: "Dashboard", roles: ["super_admin", "regional_admin", "district_admin", "branch_admin", "member", "admin", "user"] },
    { path: "/branches", label: "Branches", roles: ["super_admin", "regional_admin", "district_admin", "branch_admin", "admin"] },
    { path: "/users", label: "Users", roles: ["super_admin"] },
    { path: "/members", label: "Members", roles: ["super_admin", "regional_admin", "district_admin", "branch_admin", "admin"] },
    { path: "/attendance", label: "Attendance", roles: ["super_admin", "regional_admin", "district_admin", "branch_admin", "member", "admin", "user"] },
    { path: "/chat", label: "Branch Chat", roles: ["super_admin", "regional_admin", "district_admin", "branch_admin", "member", "admin", "user"] },
    { path: "/offerings", label: "Offerings", roles: ["super_admin", "regional_admin", "district_admin", "branch_admin", "member", "admin", "user"] },
    { path: "/expenses", label: "Expenses", roles: ["super_admin", "regional_admin", "district_admin", "branch_admin", "member", "admin", "user"] },
    { path: "/slides", label: "Slides", roles: ["super_admin"] },
    { path: "/logout", label: "Logout", roles: ["super_admin", "regional_admin", "district_admin", "branch_admin", "member", "admin", "user"] },
  ];

  return (
    <div className="sidebar bg-light vh-100 p-3">
      <h5 className="mb-4">Menu</h5>
      <Nav className="flex-column">
        {links
          .filter((link) => authContext?.hasRole(...link.roles) ?? false)
          .map((link) => (
            <Nav.Link as={Link} to={link.path} key={link.path} active={location.pathname === link.path}>
              {link.label}
            </Nav.Link>
          ))}
      </Nav>
    </div>
  );
}
