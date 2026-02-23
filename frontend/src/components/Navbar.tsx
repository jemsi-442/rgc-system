import React, { useContext } from "react";
import { Container, Nav, Navbar as BSNavbar } from "react-bootstrap";
import { Link } from "react-router-dom";
import { AuthContext } from "../context/AuthContext";

export default function Navbar() {
  const authContext = useContext(AuthContext);

  return (
    <BSNavbar bg="dark" variant="dark" expand="lg" className="mb-4">
      <Container fluid>
        <BSNavbar.Brand as={Link} to="/dashboard">
          RGC System
        </BSNavbar.Brand>
        <BSNavbar.Toggle aria-controls="navbar-nav" />
        <BSNavbar.Collapse id="navbar-nav">
          <Nav className="ms-auto">
            <Nav.Link as={Link} to="/dashboard">Dashboard</Nav.Link>
            <Nav.Link as={Link} to="/profile">{authContext?.user?.name || "User"}</Nav.Link>
          </Nav>
        </BSNavbar.Collapse>
      </Container>
    </BSNavbar>
  );
}
