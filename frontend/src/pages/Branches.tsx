import React, { useContext, useEffect, useMemo, useState } from "react";
import api from "../services/api";
import { Button, Card, Col, Form, InputGroup, Modal, Row, Table } from "react-bootstrap";
import { toast } from "react-toastify";
import { AuthContext } from "../context/AuthContext";
import "../styles/branches.css";

type BranchType = "headquarters" | "regional" | "district" | "local";

interface Region {
  id: number;
  name: string;
  districts?: District[];
}

interface District {
  id: number;
  name: string;
  region_id?: number;
}

interface Branch {
  id: number;
  name: string;
  type: BranchType;
  region_id?: number | null;
  district_id?: number | null;
  address?: string;
  phone?: string;
  email?: string;
  status?: "active" | "inactive";
  district?: District | null;
}

interface UserOption {
  id: number;
  name: string;
  role?: string;
}

export default function Branches() {
  const authContext = useContext(AuthContext);

  const canCreate = authContext?.hasRole("super_admin") ?? false;
  const canDelete = authContext?.hasRole("super_admin") ?? false;
  const canEdit = authContext?.hasRole("super_admin", "regional_admin", "district_admin", "branch_admin", "admin") ?? false;

  const [branches, setBranches] = useState<Branch[]>([]);
  const [regions, setRegions] = useState<Region[]>([]);
  const [districts, setDistricts] = useState<District[]>([]);
  const [branchAdmins, setBranchAdmins] = useState<UserOption[]>([]);

  const [showModal, setShowModal] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);

  const [name, setName] = useState("");
  const [type, setType] = useState<BranchType>("local");
  const [address, setAddress] = useState("");
  const [phone, setPhone] = useState("");
  const [email, setEmail] = useState("");
  const [regionId, setRegionId] = useState<number | "">("");
  const [districtId, setDistrictId] = useState<number | "">("");
  const [assignedBranchAdminId, setAssignedBranchAdminId] = useState<number | "">("");
  const [status, setStatus] = useState<"active" | "inactive">("active");

  const [search, setSearch] = useState("");
  const [viewMode, setViewMode] = useState<"table" | "cards">("cards");

  const loadRegions = async () => {
    try {
      const res = await api.get("/regions/hierarchy");
      setRegions(Array.isArray(res.data) ? res.data : []);
    } catch {
      toast.error("Failed to load regions");
    }
  };

  const loadBranchAdmins = async () => {
    if (!canCreate) return;

    try {
      const res = await api.get("/users");
      const users = Array.isArray(res.data) ? res.data : [];
      setBranchAdmins(users.filter((u: UserOption) => !u.role || ["branch_admin", "member", "user"].includes(u.role)));
    } catch {
      toast.error("Failed to load users");
    }
  };

  const loadBranches = async () => {
    try {
      const res = await api.get("/churches");
      setBranches(Array.isArray(res.data) ? res.data : []);
    } catch {
      toast.error("Failed to load branches");
    }
  };

  useEffect(() => {
    loadRegions();
    loadBranches();
    loadBranchAdmins();
  }, [canCreate]);

  useEffect(() => {
    if (!regionId) {
      setDistricts([]);
      setDistrictId("");
      return;
    }

    const selectedRegion = regions.find((region) => region.id === regionId);
    setDistricts(selectedRegion?.districts || []);

    if (districtId && !(selectedRegion?.districts || []).some((district) => district.id === districtId)) {
      setDistrictId("");
    }
  }, [districtId, regionId, regions]);

  const openAdd = () => {
    setEditingId(null);
    setName("");
    setType("local");
    setAddress("");
    setPhone("");
    setEmail("");
    setRegionId("");
    setDistrictId("");
    setAssignedBranchAdminId("");
    setStatus("active");
    setShowModal(true);
  };

  const openEdit = (branch: Branch) => {
    setEditingId(branch.id);
    setName(branch.name || "");
    setType(branch.type || "local");
    setAddress(branch.address || "");
    setPhone(branch.phone || "");
    setEmail(branch.email || "");
    setRegionId(branch.region_id ?? branch.district?.region_id ?? "");
    setDistrictId(branch.district_id ?? "");
    setAssignedBranchAdminId("");
    setStatus(branch.status ?? "active");
    setShowModal(true);
  };

  const handleSave = async () => {
    if (!canEdit) {
      toast.error("Unauthorized action");
      return;
    }

    if (!name.trim()) return toast.error("Branch name is required");
    if (!regionId) return toast.error("Select region");
    if (!districtId) return toast.error("Select district");

    if (!canCreate && editingId === null) {
      toast.error("Only Super Admin can create branches");
      return;
    }

    try {
      const payload: Record<string, unknown> = {
        name: name.trim(),
        type,
        address: address.trim() || null,
        phone: phone.trim() || null,
        email: email.trim() || null,
        region_id: regionId,
        district_id: districtId,
        status,
      };

      if (canCreate && assignedBranchAdminId) {
        payload.assigned_branch_admin_id = assignedBranchAdminId;
      }

      if (editingId === null) {
        await api.post("/churches", payload);
        toast.success("Branch created");
      } else {
        await api.put(`/churches/${editingId}`, payload);
        toast.success("Branch updated");
      }

      setShowModal(false);
      loadBranches();
    } catch {
      toast.error("Error saving branch");
    }
  };

  const handleDelete = async (id: number) => {
    if (!canDelete) {
      toast.error("Only Super Admin can delete branches");
      return;
    }

    if (!window.confirm("Delete this branch?")) return;

    try {
      await api.delete(`/churches/${id}`);
      toast.success("Branch deleted");
      loadBranches();
    } catch {
      toast.error("Error deleting branch");
    }
  };

  const filtered = useMemo(() => {
    const query = search.trim().toLowerCase();
    if (!query) return branches;

    return branches.filter((branch) => {
      return (
        branch.name?.toLowerCase().includes(query) ||
        (branch.address || "").toLowerCase().includes(query) ||
        (branch.district?.name || "").toLowerCase().includes(query) ||
        (branch.type || "").toLowerCase().includes(query)
      );
    });
  }, [branches, search]);

  return (
    <div className="container mt-4 branches-page">
      <Row className="align-items-center mb-3">
        <Col>
          <h3 className="fw-bold">Branches</h3>
          <div className="text-muted">Super Admin creates branches with Region and District selection.</div>
        </Col>

        <Col className="text-end">
          <InputGroup className="d-inline-flex me-2" style={{ width: 320 }}>
            <Form.Control
              placeholder="Search branches, address, district, type..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
            />
            <Button variant="outline-secondary" onClick={() => setSearch("")}>Clear</Button>
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
            className="me-2"
            onClick={() => setViewMode("table")}
          >
            Table
          </Button>

          {canCreate && (
            <Button variant="primary" onClick={openAdd}>
              + Add Branch
            </Button>
          )}
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
                  <th>Type</th>
                  <th>District</th>
                  <th>Phone</th>
                  <th>Status</th>
                  {canEdit && <th style={{ width: 160 }}>Actions</th>}
                </tr>
              </thead>
              <tbody>
                {filtered.length === 0 && (
                  <tr>
                    <td colSpan={canEdit ? 7 : 6} className="text-center text-muted py-4">No branches found.</td>
                  </tr>
                )}

                {filtered.map((branch, idx) => (
                  <tr className="fade-in-row" key={branch.id}>
                    <td>{idx + 1}</td>
                    <td>{branch.name}</td>
                    <td className="text-capitalize">{branch.type}</td>
                    <td>{branch.district?.name ?? "-"}</td>
                    <td>{branch.phone ?? "-"}</td>
                    <td>
                      <span className={`badge ${branch.status === "active" ? "bg-success" : "bg-secondary"}`}>
                        {branch.status}
                      </span>
                    </td>
                    {canEdit && (
                      <td>
                        <Button size="sm" variant="warning" className="me-2" onClick={() => openEdit(branch)}>
                          Edit
                        </Button>
                        {canDelete && (
                          <Button size="sm" variant="danger" onClick={() => handleDelete(branch.id)}>
                            Delete
                          </Button>
                        )}
                      </td>
                    )}
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

              {filtered.map((branch) => (
                <Col key={branch.id}>
                  <Card className="branch-card">
                    <Card.Body>
                      <div className="d-flex justify-content-between">
                        <div>
                          <h5 className="mb-1">{branch.name}</h5>
                          <div className="text-muted small">{branch.district?.name ?? "-"}</div>
                        </div>
                        <div>
                          <span className={`badge ${branch.status === "active" ? "bg-success" : "bg-secondary"}`}>
                            {branch.status}
                          </span>
                        </div>
                      </div>

                      <div className="mt-3">
                        <div className="text-capitalize"><strong>Type:</strong> {branch.type}</div>
                        <div className="text-muted"><strong>Phone:</strong> {branch.phone ?? "-"}</div>
                        <div className="text-muted small mt-2">{branch.address ?? ""}</div>
                      </div>

                      {canEdit && (
                        <div className="mt-3 d-flex justify-content-end">
                          <Button size="sm" variant="warning" className="me-2" onClick={() => openEdit(branch)}>
                            Edit
                          </Button>
                          {canDelete && (
                            <Button size="sm" variant="danger" onClick={() => handleDelete(branch.id)}>
                              Delete
                            </Button>
                          )}
                        </div>
                      )}
                    </Card.Body>
                  </Card>
                </Col>
              ))}
            </Row>
          )}
        </Card.Body>
      </Card>

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

                <Form.Group className="mb-3">
                  <Form.Label>Email</Form.Label>
                  <Form.Control value={email} onChange={(e) => setEmail(e.target.value)} type="email" placeholder="branch@example.org" />
                </Form.Group>
              </Col>

              <Col md={4}>
                <Form.Group className="mb-3">
                  <Form.Label>Region</Form.Label>
                  <Form.Select value={regionId} onChange={(e) => setRegionId(e.target.value === "" ? "" : Number(e.target.value))}>
                    <option value="">Select region</option>
                    {regions.map((region) => (
                      <option key={region.id} value={region.id}>{region.name}</option>
                    ))}
                  </Form.Select>
                </Form.Group>

                <Form.Group className="mb-3">
                  <Form.Label>District</Form.Label>
                  <Form.Select
                    value={districtId}
                    onChange={(e) => setDistrictId(e.target.value === "" ? "" : Number(e.target.value))}
                    disabled={!regionId}
                  >
                    <option value="">Select district</option>
                    {districts.map((district) => (
                      <option key={district.id} value={district.id}>{district.name}</option>
                    ))}
                  </Form.Select>
                </Form.Group>

                <Form.Group className="mb-3">
                  <Form.Label>Branch Type</Form.Label>
                  <Form.Select value={type} onChange={(e) => setType(e.target.value as BranchType)}>
                    <option value="headquarters">Headquarters</option>
                    <option value="regional">Regional</option>
                    <option value="district">District</option>
                    <option value="local">Local</option>
                  </Form.Select>
                </Form.Group>

                {canCreate && (
                  <Form.Group className="mb-3">
                    <Form.Label>Assigned Branch Admin (optional)</Form.Label>
                    <Form.Select
                      value={assignedBranchAdminId}
                      onChange={(e) => setAssignedBranchAdminId(e.target.value === "" ? "" : Number(e.target.value))}
                    >
                      <option value="">Select user</option>
                      {branchAdmins.map((user) => (
                        <option key={user.id} value={user.id}>{user.name}</option>
                      ))}
                    </Form.Select>
                  </Form.Group>
                )}

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
