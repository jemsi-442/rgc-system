import React from "react";
import { Modal, Button, Form } from "react-bootstrap";

interface Region {
  id: number;
  name: string;
}

interface DistrictFormProps {
  show: boolean;
  onHide: () => void;
  onSave: () => void;

  name: string;
  setName: (val: string) => void;

  selectedRegionId: number | "";
  setSelectedRegionId: (val: number | "") => void;

  regions: Region[];
  editingId: number | null;
}

export default function DistrictForm({
  show,
  onHide,
  onSave,
  name,
  setName,
  selectedRegionId,
  setSelectedRegionId,
  regions,
  editingId,
}: DistrictFormProps) {
  return (
    <Modal show={show} onHide={onHide} centered>
      <Modal.Header closeButton>
        <Modal.Title>{editingId ? "Edit District" : "Add District"}</Modal.Title>
      </Modal.Header>

      <Modal.Body>
        <Form>
          <Form.Group className="mb-3">
            <Form.Label>District Name</Form.Label>
            <Form.Control
              type="text"
              placeholder="Enter district name"
              value={name}
              onChange={(e) => setName(e.target.value)}
            />
          </Form.Group>

          <Form.Group>
            <Form.Label>Region</Form.Label>
            <Form.Select
              value={selectedRegionId}
              onChange={(e) => {
                const v = e.target.value;
                setSelectedRegionId(v === "" ? "" : Number(v));
              }}
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
        <Button variant="secondary" onClick={onHide}>
          Cancel
        </Button>
        <Button variant="primary" onClick={onSave}>
          Save
        </Button>
      </Modal.Footer>
    </Modal>
  );
}
