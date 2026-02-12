import React from "react";
import { Modal, Button, Form, Row, Col } from "react-bootstrap";

interface Region {
  id: number;
  name: string;
}

interface District {
  id: number;
  name: string;
  region_id: number;
}

interface ChurchFormProps {
  show: boolean;
  onHide: () => void;
  onSave: () => void;

  name: string;
  setName: (val: string) => void;

  regionId: number | "";
  setRegionId: (val: number | "") => void;

  districtId: number | "";
  setDistrictId: (val: number | "") => void;

  address: string;
  setAddress: (val: string) => void;

  phone: string;
  setPhone: (val: string) => void;

  pastor: string;
  setPastor: (val: string) => void;

  regions: Region[];
  districts: District[];
  editingId: number | null;
}

export default function ChurchForm({
  show,
  onHide,
  onSave,
  name,
  setName,
  regionId,
  setRegionId,
  districtId,
  setDistrictId,
  address,
  setAddress,
  phone,
  setPhone,
  pastor,
  setPastor,
  regions,
  districts,
  editingId,
}: ChurchFormProps) {
  const filteredDistricts = regionId
    ? districts.filter((d) => d.region_id === regionId)
    : [];

  return (
    <Modal show={show} onHide={onHide} centered size="lg">
      <Modal.Header closeButton>
        <Modal.Title>{editingId ? "Edit Church" : "Add Church"}</Modal.Title>
      </Modal.Header>

      <Modal.Body>
        <Form>
          <Row>
            <Col md={6}>
              <Form.Group className="mb-3">
                <Form.Label>Church Name</Form.Label>
                <Form.Control
                  type="text"
                  placeholder="Enter church name"
                  value={name}
                  onChange={(e) => setName(e.target.value)}
                />
              </Form.Group>
            </Col>

            <Col md={6}>
              <Form.Group className="mb-3">
                <Form.Label>Pastor in Charge</Form.Label>
                <Form.Control
                  type="text"
                  value={pastor}
                  placeholder="Pastor full name"
                  onChange={(e) => setPastor(e.target.value)}
                />
              </Form.Group>
            </Col>
          </Row>

          <Row>
            <Col md={6}>
              <Form.Group className="mb-3">
                <Form.Label>Region</Form.Label>
                <Form.Select
                  value={regionId}
                  onChange={(e) => {
                    const v = e.target.value;
                    setRegionId(v === "" ? "" : Number(v));
                    setDistrictId(""); // reset district
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
            </Col>

            <Col md={6}>
              <Form.Group className="mb-3">
                <Form.Label>District</Form.Label>
                <Form.Select
                  value={districtId}
                  onChange={(e) =>
                    setDistrictId(e.target.value === "" ? "" : Number(e.target.value))
                  }
                >
                  <option value="">Select district</option>
                  {filteredDistricts.map((d) => (
                    <option key={d.id} value={d.id}>
                      {d.name}
                    </option>
                  ))}
                </Form.Select>
              </Form.Group>
            </Col>
          </Row>

          <Form.Group className="mb-3">
            <Form.Label>Address</Form.Label>
            <Form.Control
              as="textarea"
              rows={2}
              placeholder="Explain location / description"
              value={address}
              onChange={(e) => setAddress(e.target.value)}
            />
          </Form.Group>

          <Form.Group className="mb-3">
            <Form.Label>Church Phone</Form.Label>
            <Form.Control
              type="text"
              placeholder="e.g +255 6XX XXX XXX"
              value={phone}
              onChange={(e) => setPhone(e.target.value)}
            />
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
