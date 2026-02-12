import React from "react";
import { Navbar as BSNavbar, Container, Nav } from "react-bootstrap";
import { Link } from "react-router-dom";

export default function Navbar() {
  return (
    <BSNavbar bg="dark" variant="dark" expand="lg" className="mb-4">
      <Container fluid>
        <BSNavbar.Brand as={Link} to="/">
          RGC System
        </BSNavbar.Brand>
        <BSNavbar.Toggle aria-controls="navbar-nav" />
        <BSNavbar.Collapse id="navbar-nav">
          <Nav className="ms-auto">
            <Nav.Link as={Link} to="/dashboard">
              Dashboard
            </Nav.Link>
            <Nav.Link as={Link} to="/offerings">
              Offerings
            </Nav.Link>
            <Nav.Link as={Link} to="/members">
              Members
            </Nav.Link>
            <Nav.Link as={Link} to="/branches">
              Branches
            </Nav.Link>
          </Nav>
        </BSNavbar.Collapse>
      </Container>
    </BSNavbar>
  );
}
