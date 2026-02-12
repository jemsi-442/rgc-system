import React, { useEffect, useState } from "react";
import apiResources from "../services/apiResources";
import {
  Button,
  Card,
  Modal,
  Form,
  Table,
  Row,
  Col,
  InputGroup,
} from "react-bootstrap";
import { toast } from "react-toastify";
import "../styles/branches.css";

interface Region {
  id: number;
  name: string;
}

interface District {
  id: number;
  name: string;
  region_id?: number;
}

interface Pastor {
  id: number;
  full_name: string;
}

interface Branch {
  id: number;
  name: string;
  address?: string;
  phone?: string;
  district_id?: number | null;
  pastor_id?: number | null;
  status?: "active" | "inactive";
  district?: District | null;
  pastor?: Pastor | null;
}

export default function Branches() {
  const [branches, setBranches] = useState<Branch[]>([]);
  const [regions, setRegions] = useState<Region[]>([]);
  const [districts, setDistricts] = useState<District[]>([]);
  const [pastors, setPastors] = useState<Pastor[]>([]);

  const [showModal, setShowModal] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);

  // form fields
  const [name, setName] = useState("");
  const [address, setAddress] = useState("");
  const [phone, setPhone] = useState("");
  const [regionId, setRegionId] = useState<number | "">("");
  const [districtId, setDistrictId] = useState<number | "">("");
  const [pastorId, setPastorId] = useState<number | "">("");
  const [status, setStatus] = useState<"active" | "inactive">("active");

  // UI controls
  const [search, setSearch] = useState("");
  const [viewMode, setViewMode] = useState<"table" | "cards">("cards");

  // load initial data
  const loadRegions = async () => {
    try {
      const res = await api.get("/regions");
      setRegions(res.data || []);
    } catch {
      toast.error("Failed to load regions");
    }
  };

  const loadDistricts = async (rId?: number | "") => {
    try {
      const url = rId ? `/districts?region_id=${rId}` : "/districts";
      const res = await api.get(url);
      setDistricts(res.data || []);
    } catch {
      toast.error("Failed to load districts");
    }
  };

  const loadPastors = async () => {
    try {
      const res = await api.get("/pastors");
      setPastors(res.data || []);
    } catch {
      // Not critical; show info in UI
      // toast.error("Failed to load pastors");
    }
  };

  const loadBranches = async () => {
    try {
      const res = await api.get("/churches");
      setBranches(res.data || []);
    } catch {
      toast.error("Failed to load branches");
    }
  };

  useEffect(() => {
    loadRegions();
    loadDistricts();
    loadPastors();
    loadBranches();
    // eslint-disable-next-line
  }, []);

  // when region changes, reload districts for that region
  useEffect(() => {
    if (regionId === "") {
      loadDistricts();
    } else {
      loadDistricts(regionId as number);
    }
  }, [regionId]);

  // open add modal
  const openAdd = () => {
    setEditingId(null);
    setName("");
    setAddress("");
    setPhone("");
    setRegionId("");
    setDistrictId("");
    setPastorId("");
    setStatus("active");
    setShowModal(true);
  };

  // open edit modal and populate fields
  const openEdit = (b: Branch) => {
    setEditingId(b.id);
    setName(b.name || "");
    setAddress(b.address || "");
    setPhone(b.phone || "");
    setRegionId(b.district?.region_id ?? ""); // may be undefined; district likely has region_id
    setDistrictId(b.district_id ?? "");
    setPastorId(b.pastor_id ?? "");
    setStatus(b.status ?? "active");
    setShowModal(true);
  };

  const handleSave = async () => {
    if (name.trim().length === 0) return toast.error("Branch name is required");
    if (!districtId) return toast.error("Select district");

    try {
      const payload = {
        name,
        address,
        phone,
        district_id: districtId,
        pastor_id: pastorId || null,
        status,
      };

      if (editingId === null) {
        await api.post("/churches", payload);
        toast.success("Branch created");
      } else {
        await api.put(`/churches/${editingId}`, payload);
        toast.success("Branch updated");
      }

      setShowModal(false);
      loadBranches();
    } catch (err) {
      toast.error("Error saving branch");
    }
  };

  const handleDelete = async (id: number) => {
    if (!window.confirm("Delete this branch?")) return;
    try {
      await api.delete(`/churches/${id}`);
      toast.success("Branch deleted");
      loadBranches();
    } catch {
      toast.error("Error deleting branch");
    }
  };

  // search & filtered results
  const filtered = branches.filter((b) => {
    const q = search.trim().toLowerCase();
    if (!q) return true;
    return (
      b.name?.toLowerCase().includes(q) ||
      (b.address || "").toLowerCase().includes(q) ||
      (b.district?.name || "").toLowerCase().includes(q) ||
      (b.pastor?.full_name || "").toLowerCase().includes(q)
    );
  });

  return (
    <div className="container mt-4 branches-page">
      <Row className="align-items-center mb-3">
        <Col>
          <h3 className="fw-bold">Branches (Churches)</h3>
          <div className="text-muted">Manage all church branches across the country</div>
        </Col>

        <Col className="text-end">
          <InputGroup className="d-inline-flex me-2" style={{ width: 320 }}>
            <Form.Control
              placeholder="Search branches, address, district, pastor..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
            />
            <Button variant="outline-secondary" onClick={() => setSearch("")}>
              Clear
            </Button>
          </InputGroup>

          <Button
            variant={viewMode === "cards" ? "secondary" : "outline-secondary"}
            className="me-2"
            onClick={() => setViewMode("cards")}
          >
            Cards
          </Button>
          <Button
            variant={viewMode === "table" ? "secondary" : "outline-secondary"}
            onClick={() => setViewMode("table")}
            className="me-2"
          >
            Table
          </Button>

          <Button variant="primary" onClick={openAdd}>
            + Add Branch
          </Button>
        </Col>
      </Row>

      <Card className="shadow-sm animated-card mb-3">
        <Card.Body>
          {viewMode === "table" ? (
            <Table striped hover responsive className="branch-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>District</th>
                  <th>Pastor</th>
                  <th>Phone</th>
                  <th>Status</th>
                  <th style={{ width: 160 }}>Actions</th>
                </tr>
              </thead>
              <tbody>
                {filtered.length === 0 && (
                  <tr>
                    <td colSpan={7} className="text-center text-muted py-4">
                      No branches found.
                    </td>
                  </tr>
                )}

                {filtered.map((b, idx) => (
                  <tr className="fade-in-row" key={b.id}>
                    <td>{idx + 1}</td>
                    <td>{b.name}</td>
                    <td>{b.district?.name ?? "—"}</td>
                    <td>{b.pastor?.full_name ?? "—"}</td>
                    <td>{b.phone ?? "—"}</td>
                    <td>
                      <span className={`badge ${b.status === "active" ? "bg-success" : "bg-secondary"}`}>
                        {b.status}
                      </span>
                    </td>
                    <td>
                      <Button size="sm" variant="warning" className="me-2" onClick={() => openEdit(b)}>
                        Edit
                      </Button>
                      <Button size="sm" variant="danger" onClick={() => handleDelete(b.id)}>
                        Delete
                      </Button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </Table>
          ) : (
            <Row xs={1} md={2} lg={3} className="g-3">
              {filtered.length === 0 && (
                <Col>
                  <div className="text-center text-muted py-4">No branches found.</div>
                </Col>
              )}

              {filtered.map((b) => (
                <Col key={b.id}>
                  <Card className="branch-card">
                    <Card.Body>
                      <div className="d-flex justify-content-between">
                        <div>
                          <h5 className="mb-1">{b.name}</h5>
                          <div className="text-muted small">{b.district?.name ?? "—"}</div>
                        </div>
                        <div>
                          <span className={`badge ${b.status === "active" ? "bg-success" : "bg-secondary"}`}>
                            {b.status}
                          </span>
                        </div>
                      </div>

                      <div className="mt-3">
                        <div><strong>Pastor:</strong> {b.pastor?.full_name ?? "—"}</div>
                        <div className="text-muted"><strong>Phone:</strong> {b.phone ?? "—"}</div>
                        <div className="text-muted small mt-2">{b.address ?? ""}</div>
                      </div>

                      <div className="mt-3 d-flex justify-content-end">
                        <Button size="sm" variant="warning" className="me-2" onClick={() => openEdit(b)}>
                          Edit
                        </Button>
                        <Button size="sm" variant="danger" onClick={() => handleDelete(b.id)}>
                          Delete
                        </Button>
                      </div>
                    </Card.Body>
                  </Card>
                </Col>
              ))}
            </Row>
          )}
        </Card.Body>
      </Card>

      {/* Modal */}
      <Modal show={showModal} onHide={() => setShowModal(false)} centered size="lg">
        <Modal.Header closeButton>
          <Modal.Title>{editingId ? "Edit Branch" : "Add Branch"}</Modal.Title>
        </Modal.Header>

        <Modal.Body>
          <Form>
            <Row>
              <Col md={8}>
                <Form.Group className="mb-3">
                  <Form.Label>Branch Name</Form.Label>
                  <Form.Control value={name} onChange={(e) => setName(e.target.value)} placeholder="Branch name" />
                </Form.Group>

                <Form.Group className="mb-3">
                  <Form.Label>Address</Form.Label>
                  <Form.Control value={address} onChange={(e) => setAddress(e.target.value)} placeholder="Address" />
                </Form.Group>

                <Form.Group className="mb-3">
                  <Form.Label>Phone</Form.Label>
                  <Form.Control value={phone} onChange={(e) => setPhone(e.target.value)} placeholder="+2557..." />
                </Form.Group>
              </Col>

              <Col md={4}>
                <Form.Group className="mb-3">
                  <Form.Label>Region</Form.Label>
                  <Form.Select value={regionId} onChange={(e) => setRegionId(e.target.value === "" ? "" : Number(e.target.value))}>
                    <option value="">Select region</option>
                    {regions.map((r) => (<option key={r.id} value={r.id}>{r.name}</option>))}
                  </Form.Select>
                </Form.Group>

                <Form.Group className="mb-3">
                  <Form.Label>District</Form.Label>
                  <Form.Select value={districtId} onChange={(e) => setDistrictId(e.target.value === "" ? "" : Number(e.target.value))}>
                    <option value="">Select district</option>
                    {districts.map((d) => (<option key={d.id} value={d.id}>{d.name}</option>))}
                  </Form.Select>
                </Form.Group>

                <Form.Group className="mb-3">
                  <Form.Label>Pastor</Form.Label>
                  <Form.Select value={pastorId} onChange={(e) => setPastorId(e.target.value === "" ? "" : Number(e.target.value))}>
                    <option value="">Select pastor (optional)</option>
                    {pastors.length === 0 && <option disabled>— No pastors available —</option>}
                    {pastors.map((p) => (<option key={p.id} value={p.id}>{p.full_name}</option>))}
                  </Form.Select>
                </Form.Group>

                <Form.Group className="mb-3">
                  <Form.Label>Status</Form.Label>
                  <Form.Select value={status} onChange={(e) => setStatus(e.target.value as "active" | "inactive")}>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                  </Form.Select>
                </Form.Group>
              </Col>
            </Row>
          </Form>
        </Modal.Body>

        <Modal.Footer>
          <Button variant="secondary" onClick={() => setShowModal(false)}>Cancel</Button>
          <Button variant="primary" onClick={handleSave}>Save</Button>
        </Modal.Footer>
      </Modal>
    </div>
  );
}
