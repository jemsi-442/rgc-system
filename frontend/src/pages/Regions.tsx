import React, { useEffect, useState } from "react";
import api from "../services/apiAuth";
import { Button, Card, Modal, Form, Table, Spinner } from "react-bootstrap";
import { toast } from "react-toastify";
import "../styles/regions.css";

interface Region {
  id: number;
  name: string;
  code?: string;
}

export default function Regions() {
  const [regions, setRegions] = useState<Region[]>([]);
  const [loading, setLoading] = useState<boolean>(true);

  const [showModal, setShowModal] = useState(false);
  const [regionName, setRegionName] = useState("");
  const [regionCode, setRegionCode] = useState("");
  const [editingId, setEditingId] = useState<number | null>(null);

  // Reset form
  const resetForm = () => {
    setRegionName("");
    setRegionCode("");
    setEditingId(null);
  };

  const handleCloseModal = () => {
    resetForm();
    setShowModal(false);
  };

  // Fetch regions
  const loadRegions = async () => {
    try {
      setLoading(true);
      const res = await api.get("/regions");

      if (Array.isArray(res.data?.data)) {
        setRegions(res.data.data);
      } else {
        toast.error("Invalid data format received");
      }
    } catch (err) {
      toast.error("Failed to load regions");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadRegions();
  }, []);

  // Save region
  const handleSave = async () => {
    if (!regionName.trim()) {
      return toast.error("Region name is required");
    }

    try {
      if (editingId === null) {
        await api.post("/regions", {
          name: regionName,
          code: regionCode || null,
        });
        toast.success("Region added successfully");
      } else {
        await api.put(`/regions/${editingId}`, {
          name: regionName,
          code: regionCode || null,
        });
        toast.success("Region updated successfully");
      }

      handleCloseModal();
      loadRegions();
    } catch (err: any) {
      if (err.response?.data?.errors) {
        const messages = Object.values(err.response.data.errors).flat();
        messages.forEach((m: any) => toast.error(m));
      } else {
        toast.error("Error saving region");
      }
    }
  };

  // Edit region
  const handleEdit = (region: Region) => {
    setEditingId(region.id);
    setRegionName(region.name);
    setRegionCode(region.code || "");
    setShowModal(true);
  };

  // Delete region
  const handleDelete = async (id: number) => {
    if (!window.confirm("Delete this region?")) return;

    try {
      await api.delete(`/regions/${id}`);
      toast.success("Region deleted");
      loadRegions();
    } catch {
      toast.error("Error deleting region");
    }
  };

  return (
    <div className="container mt-4 regions-page">
      {/* Page Header */}
      <div className="d-flex justify-content-between align-items-center mb-4">
        <h3 className="fw-bold fade-in">Regions Management</h3>
        <Button className="btn btn-primary" onClick={() => setShowModal(true)}>
          + Add Region
        </Button>
      </div>

      {/* Regions Table */}
      <Card className="shadow-sm region-card animated-card">
        <Card.Body>
          <h5 className="fw-semibold mb-3">Regions List</h5>

          {loading ? (
            <div className="text-center py-4">
              <Spinner animation="border" />
            </div>
          ) : (
            <Table striped hover responsive className="region-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Region Name</th>
                  <th>Code</th>
                  <th style={{ width: "160px" }}>Actions</th>
                </tr>
              </thead>

              <tbody>
                {regions.map((region, index) => (
                  <tr key={region.id} className="fade-in-row">
                    <td>{index + 1}</td>
                    <td>{region.name}</td>
                    <td>{region.code || "-"}</td>
                    <td>
                      <Button
                        size="sm"
                        variant="warning"
                        className="me-2"
                        onClick={() => handleEdit(region)}
                      >
                        Edit
                      </Button>
                      <Button
                        size="sm"
                        variant="danger"
                        onClick={() => handleDelete(region.id)}
                      >
                        Delete
                      </Button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </Table>
          )}
        </Card.Body>
      </Card>

      {/* Modal */}
      <Modal show={showModal} onHide={handleCloseModal} centered>
        <Modal.Header closeButton>
          <Modal.Title>{editingId ? "Edit Region" : "Add Region"}</Modal.Title>
        </Modal.Header>

        <Modal.Body>
          <Form>
            <Form.Group className="mb-3">
              <Form.Label>Region Name</Form.Label>
              <Form.Control
                type="text"
                placeholder="Enter region name"
                value={regionName}
                onChange={(e) => setRegionName(e.target.value)}
              />
            </Form.Group>

            <Form.Group>
              <Form.Label>Region Code (optional)</Form.Label>
              <Form.Control
                type="text"
                placeholder="e.g. DSM, MBY, ARU"
                value={regionCode}
                onChange={(e) =>
                  setRegionCode(e.target.value.toUpperCase())
                }
              />
            </Form.Group>
          </Form>
        </Modal.Body>

        <Modal.Footer>
          <Button variant="secondary" onClick={handleCloseModal}>
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
