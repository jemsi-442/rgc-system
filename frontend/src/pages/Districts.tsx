import React, { useEffect, useState } from "react";
import api from "../services/apiAuth";
import { Button, Card, Modal, Form, Table, Row, Col } from "react-bootstrap";
import { toast } from "react-toastify";
import "../styles/districts.css";

interface Region {
  id: number;
  name: string;
}

interface District {
  id: number;
  name: string;
  region_id?: number | null;
  region?: Region | null;
}

export default function Districts() {
  const [districts, setDistricts] = useState<District[]>([]);
  const [regions, setRegions] = useState<Region[]>([]);
  const [showModal, setShowModal] = useState(false);

  const [name, setName] = useState("");
  const [selectedRegionId, setSelectedRegionId] = useState<number | "">("");
  const [editingId, setEditingId] = useState<number | null>(null);

  const [filterRegionId, setFilterRegionId] = useState<number | "">("");

  // RESET FORM
  const resetForm = () => {
    setName("");
    setSelectedRegionId("");
    setEditingId(null);
  };

  const closeModal = () => {
    resetForm();
    setShowModal(false);
  };

  // LOAD REGIONS
  const loadRegions = async () => {
    try {
      const res = await api.get("/regions");
      const data = res.data?.data ?? [];
      setRegions(data);
    } catch (err) {
      toast.error("Could not load regions");
    }
  };

  // LOAD DISTRICTS
  const loadDistricts = async () => {
    try {
      const url = filterRegionId
        ? `/districts?region_id=${filterRegionId}`
        : "/districts";

      const res = await api.get(url);
      const data = res.data?.data ?? [];

      setDistricts(data);
    } catch (err) {
      toast.error("Could not load districts");
    }
  };

  useEffect(() => {
    loadRegions();
    loadDistricts();
  }, []);

  useEffect(() => {
    loadDistricts();
  }, [filterRegionId]);

  // OPEN ADD
  const openAdd = () => {
    resetForm();
    setShowModal(true);
  };

  // SAVE DISTRICT
  const handleSave = async () => {
    if (!name.trim()) return toast.error("District name is required");
    if (!selectedRegionId) return toast.error("Select a region");

    try {
      if (editingId === null) {
        await api.post("/districts", {
          name,
          region_id: selectedRegionId,
        });
        toast.success("District added");
      } else {
        await api.put(`/districts/${editingId}`, {
          name,
          region_id: selectedRegionId,
        });
        toast.success("District updated");
      }

      closeModal();
      loadDistricts();
    } catch (err: any) {
      toast.error("Error saving district");
    }
  };

  // EDIT
  const handleEdit = (d: District) => {
    setEditingId(d.id);
    setName(d.name);
    setSelectedRegionId(d.region_id ?? "");
    setShowModal(true);
  };

  // DELETE
  const handleDelete = async (id: number) => {
    if (!window.confirm("Delete this district?")) return;

    try {
      await api.delete(`/districts/${id}`);
      toast.success("District deleted");
      loadDistricts();
    } catch (err) {
      toast.error("Error deleting district");
    }
  };

  return (
    <div className="container mt-4 districts-page">
      <Row className="align-items-center mb-3">
        <Col>
          <h3 className="fw-bold fade-in">Districts Management</h3>
          <div className="text-muted">Manage districts and their regions</div>
        </Col>

        <Col className="text-end">
          <Button
            variant="outline-primary"
            className="me-2"
            onClick={() => setFilterRegionId("")}
          >
            All
          </Button>

          <Button variant="primary" onClick={openAdd}>
            + Add District
          </Button>
        </Col>
      </Row>

      {/* FILTER & TABLE */}
      <Card className="shadow-sm animated-card mb-3">
        <Card.Body>
          <Row className="mb-3">
            <Col md={4}>
              <Form.Group>
                <Form.Label>Filter by Region</Form.Label>
                <Form.Select
                  value={filterRegionId}
                  onChange={(e) =>
                    setFilterRegionId(
                      e.target.value === "" ? "" : Number(e.target.value)
                    )
                  }
                >
                  <option value="">All Regions</option>
                  {regions.map((r) => (
                    <option key={r.id} value={r.id}>
                      {r.name}
                    </option>
                  ))}
                </Form.Select>
              </Form.Group>
            </Col>
          </Row>

          <Table striped hover responsive className="district-table">
            <thead>
              <tr>
                <th>#</th>
                <th>District Name</th>
                <th>Region</th>
                <th style={{ width: "170px" }}>Actions</th>
              </tr>
            </thead>

            <tbody>
              {districts.length === 0 ? (
                <tr>
                  <td colSpan={4} className="text-center text-muted py-4">
                    No districts found.
                  </td>
                </tr>
              ) : (
                districts.map((d, idx) => (
                  <tr className="fade-in-row" key={d.id}>
                    <td>{idx + 1}</td>
                    <td>{d.name}</td>
                    <td>{d.region?.name ?? "—"}</td>

                    <td>
                      <Button
                        size="sm"
                        variant="warning"
                        className="me-2"
                        onClick={() => handleEdit(d)}
                      >
                        Edit
                      </Button>

                      <Button
                        size="sm"
                        variant="danger"
                        onClick={() => handleDelete(d.id)}
                      >
                        Delete
                      </Button>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </Table>
        </Card.Body>
      </Card>

      {/* MODAL */}
      <Modal show={showModal} onHide={closeModal} centered>
        <Modal.Header closeButton>
          <Modal.Title>
            {editingId ? "Edit District" : "Add District"}
          </Modal.Title>
        </Modal.Header>

        <Modal.Body>
          <Form>
            {/* NAME */}
            <Form.Group className="mb-3">
              <Form.Label>District Name</Form.Label>
              <Form.Control
                type="text"
                placeholder="Enter district name"
                value={name}
                onChange={(e) => setName(e.target.value)}
              />
            </Form.Group>

            {/* REGION */}
            <Form.Group>
              <Form.Label>Region</Form.Label>
              <Form.Select
                value={selectedRegionId}
                onChange={(e) =>
                  setSelectedRegionId(
                    e.target.value === "" ? "" : Number(e.target.value)
                  )
                }
              >
                <option value="">Select region</option>
                {regions.map((r) => (
                  <option key={r.id} value={r.id}>
                    {r.name}
                  </option>
                ))}
              </Form.Select>
            </Form.Group>
          </Form>
        </Modal.Body>

        <Modal.Footer>
          <Button variant="secondary" onClick={closeModal}>
            Cancel
          </Button>
          <Button variant="primary" onClick={handleSave}>
            Save
          </Button>
        </Modal.Footer>
      </Modal>
    </div>
  );
}
