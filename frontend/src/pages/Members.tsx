import React, { useEffect, useMemo, useState } from "react";
import {
  Button,
  Card,
  Col,
  Form,
  InputGroup,
  Modal,
  Row,
  Table,
  Pagination,
} from "react-bootstrap";
import api from "../services/apiAuth";
import { toast } from "react-toastify";
import Papa from "papaparse";
import "../styles/members.css";

interface Branch {
  id: number;
  name: string;
}

interface Member {
  id: number;
  church_id: number;
  first_name: string;
  last_name: string;
  dob?: string | null;
  phone?: string | null;
  email?: string | null;
  notes?: string | null;
  church?: Branch | null;
}

const PAGE_SIZE = 10;

export default function Members() {
  const [members, setMembers] = useState<Member[]>([]);
  const [branches, setBranches] = useState<Branch[]>([]);
  const [loading, setLoading] = useState(false);

  // search + view mode
  const [q, setQ] = useState("");
  const [viewMode, setViewMode] = useState<"table" | "cards">("table");

  // pagination
  const [page, setPage] = useState(1);

  // modal for single add/edit
  const [showModal, setShowModal] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [form, setForm] = useState({
    church_id: "" as number | "",
    first_name: "",
    last_name: "",
    dob: "",
    phone: "",
    email: "",
    notes: "",
  });

  // CSV bulk import
  const [showCsvModal, setShowCsvModal] = useState(false);
  const [csvFile, setCsvFile] = useState<File | null>(null);
  const [csvPreview, setCsvPreview] = useState<Member[]>([]);
  const [csvUploading, setCsvUploading] = useState(false);

  // load data
  const loadBranches = async () => {
    try {
      const res = await api.get("/churches");
      setBranches(res.data || []);
    } catch {
      toast.error("Could not load branches");
    }
  };

  const loadMembers = async () => {
    setLoading(true);
    try {
      const res = await api.get("/members");
      setMembers(res.data || []);
    } catch {
      toast.error("Failed to load members");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadBranches();
    loadMembers();
    // eslint-disable-next-line
  }, []);

  // filtered + paginated list
  const filtered = useMemo(() => {
    const query = q.trim().toLowerCase();
    if (!query) return members;
    return members.filter((m) => {
      return (
        `${m.first_name} ${m.last_name}`.toLowerCase().includes(query) ||
        (m.phone || "").toLowerCase().includes(query) ||
        (m.email || "").toLowerCase().includes(query) ||
        (m.church?.name || "").toLowerCase().includes(query)
      );
    });
  }, [members, q]);

  const pages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
  const visible = filtered.slice((page - 1) * PAGE_SIZE, page * PAGE_SIZE);

  useEffect(() => {
    if (page > pages) setPage(1);
    // eslint-disable-next-line
  }, [pages]);

  // open add modal
  const openAdd = () => {
    setEditingId(null);
    setForm({
      church_id: branches.length ? branches[0].id : "",
      first_name: "",
      last_name: "",
      dob: "",
      phone: "",
      email: "",
      notes: "",
    });
    setShowModal(true);
  };

  // open edit
  const openEdit = (m: Member) => {
    setEditingId(m.id);
    setForm({
      church_id: m.church_id,
      first_name: m.first_name,
      last_name: m.last_name,
      dob: m.dob ?? "",
      phone: m.phone ?? "",
      email: m.email ?? "",
      notes: m.notes ?? "",
    });
    setShowModal(true);
  };

  // save member
  const saveMember = async () => {
    if (!form.first_name.trim() || !form.last_name.trim()) {
      return toast.error("First name and last name are required");
    }
    if (!form.church_id) return toast.error("Assign a branch");

    try {
      if (editingId) {
        await api.put(`/members/${editingId}`, form);
        toast.success("Member updated");
      } else {
        await api.post("/members", form);
        toast.success("Member added");
      }
      setShowModal(false);
      loadMembers();
    } catch {
      toast.error("Error saving member");
    }
  };

  const handleDelete = async (id: number) => {
    if (!window.confirm("Delete this member?")) return;
    try {
      await api.delete(`/members/${id}`);
      toast.success("Member deleted");
      loadMembers();
    } catch {
      toast.error("Failed to delete member");
    }
  };

  // CSV parsing (client preview)
  const handleCsvFile = (file: File | null) => {
    setCsvFile(file);
    if (!file) {
      setCsvPreview([]);
      return;
    }

    Papa.parse(file, {
      header: true,
      skipEmptyLines: true,
      complete: (results: Papa.ParseResult<any>) => {
        // map fields: expect columns (first_name,last_name,dob,phone,email,church_id,notes)
        const data: Member[] = (results.data || []).map((row: any, idx: number) => ({
          id: -(idx + 1), // temporary negative id for preview
          church_id: row.church_id ? Number(row.church_id) : (branches[0]?.id ?? 0),
          first_name: row.first_name ?? "",
          last_name: row.last_name ?? "",
          dob: row.dob ?? "",
          phone: row.phone ?? "",
          email: row.email ?? "",
          notes: row.notes ?? "",
          church: branches.find((b) => b.id === Number(row.church_id)) ?? null,
        }));
        setCsvPreview(data);
      },
      error: (err) => {
        toast.error("CSV parse error");
      },
    });
  };

  // upload CSV to backend (backend should accept array)
  const uploadCsv = async () => {
    if (!csvFile) return toast.error("Select CSV file first");
    if (csvPreview.length === 0) return toast.error("No data to upload");

    setCsvUploading(true);
    try {
      // try posting parsed data (backend expected endpoint /members/bulk)
      const payload = csvPreview.map((m) => ({
        church_id: m.church_id,
        first_name: m.first_name,
        last_name: m.last_name,
        dob: m.dob,
        phone: m.phone,
        email: m.email,
        notes: m.notes,
      }));

      await api.post("/members/bulk", { members: payload });
      toast.success("CSV imported successfully");
      setShowCsvModal(false);
      setCsvFile(null);
      setCsvPreview([]);
      loadMembers();
    } catch (err) {
      toast.error("CSV upload failed");
    } finally {
      setCsvUploading(false);
    }
  };

  return (
    <div className="container mt-4 members-page">
      <Row className="align-items-center mb-3">
        <Col>
          <h3 className="fw-bold">Members</h3>
          <div className="text-muted">Manage church members — add, edit, import</div>
        </Col>

        <Col className="text-end">
          <InputGroup className="d-inline-flex me-2" style={{ width: 380 }}>
            <Form.Control
              placeholder="Search name, phone, email, branch..."
              value={q}
              onChange={(e) => setQ(e.target.value)}
            />
            <Button variant="outline-secondary" onClick={() => setQ("")}>Clear</Button>
          </InputGroup>

          <Button variant="outline-secondary" className="me-2" onClick={() => setViewMode(viewMode === "table" ? "cards" : "table")}>
            Switch View
          </Button>

          <Button variant="success" className="me-2" onClick={() => setShowCsvModal(true)}>
            Import CSV
          </Button>

          <Button variant="primary" onClick={openAdd}>+ Add Member</Button>
        </Col>
      </Row>

      <Card className="shadow-sm animated-card">
        <Card.Body>
          {viewMode === "table" ? (
            <>
              <Table striped hover responsive className="member-table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Branch</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>DOB</th>
                    <th style={{ width: 140 }}>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {visible.length === 0 && (
                    <tr>
                      <td colSpan={7} className="text-center text-muted py-4">
                        No members found.
                      </td>
                    </tr>
                  )}

                  {visible.map((m, idx) => (
                    <tr className="fade-in-row" key={m.id}>
                      <td>{(page - 1) * PAGE_SIZE + idx + 1}</td>
                      <td>{m.first_name} {m.last_name}</td>
                      <td>{m.church?.name ?? "—"}</td>
                      <td>{m.phone ?? "—"}</td>
                      <td>{m.email ?? "—"}</td>
                      <td>{m.dob ?? "—"}</td>
                      <td>
                        <Button size="sm" variant="warning" className="me-2" onClick={() => openEdit(m)}>Edit</Button>
                        <Button size="sm" variant="danger" onClick={() => handleDelete(m.id)}>Delete</Button>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </Table>

              {/* pagination */}
              <div className="d-flex justify-content-end mt-2">
                <Pagination>
                  <Pagination.First onClick={() => setPage(1)} disabled={page === 1} />
                  <Pagination.Prev onClick={() => setPage(Math.max(1, page - 1))} disabled={page === 1} />
                  {Array.from({ length: pages }).map((_, i) => (
                    <Pagination.Item key={i} active={i + 1 === page} onClick={() => setPage(i + 1)}>
                      {i + 1}
                    </Pagination.Item>
                  ))}
                  <Pagination.Next onClick={() => setPage(Math.min(pages, page + 1))} disabled={page === pages} />
                  <Pagination.Last onClick={() => setPage(pages)} disabled={page === pages} />
                </Pagination>
              </div>
            </>
          ) : (
            <Row xs={1} md={2} lg={3} className="g-3">
              {visible.map((m) => (
                <Col key={m.id}>
                  <Card className="member-card">
                    <Card.Body>
                      <div className="d-flex justify-content-between">
                        <div>
                          <h5 className="mb-1">{m.first_name} {m.last_name}</h5>
                          <div className="text-muted small">{m.church?.name ?? "—"}</div>
                        </div>
                      </div>

                      <div className="mt-2 small text-muted">
                        <div><strong>Phone:</strong> {m.phone ?? "—"}</div>
                        <div><strong>Email:</strong> {m.email ?? "—"}</div>
                        <div><strong>DOB:</strong> {m.dob ?? "—"}</div>
                      </div>

                      <div className="mt-3 d-flex justify-content-end">
                        <Button size="sm" variant="warning" className="me-2" onClick={() => openEdit(m)}>Edit</Button>
                        <Button size="sm" variant="danger" onClick={() => handleDelete(m.id)}>Delete</Button>
                      </div>
                    </Card.Body>
                  </Card>
                </Col>
              ))}
            </Row>
          )}
        </Card.Body>
      </Card>

      {/* Member Modal */}
      <Modal show={showModal} onHide={() => setShowModal(false)} centered>
        <Modal.Header closeButton>
          <Modal.Title>{editingId ? "Edit Member" : "Add Member"}</Modal.Title>
        </Modal.Header>
        <Modal.Body>
          <Form>
            <Row>
              <Col md={6}>
                <Form.Group className="mb-2">
                  <Form.Label>First name</Form.Label>
                  <Form.Control value={form.first_name} onChange={(e) => setForm({ ...form, first_name: e.target.value })} />
                </Form.Group>
              </Col>
              <Col md={6}>
                <Form.Group className="mb-2">
                  <Form.Label>Last name</Form.Label>
                  <Form.Control value={form.last_name} onChange={(e) => setForm({ ...form, last_name: e.target.value })} />
                </Form.Group>
              </Col>
            </Row>

            <Form.Group className="mb-2">
              <Form.Label>Branch</Form.Label>
              <Form.Select value={form.church_id} onChange={(e) => setForm({ ...form, church_id: Number(e.target.value) })}>
                <option value="">Select branch</option>
                {branches.map((b) => (<option key={b.id} value={b.id}>{b.name}</option>))}
              </Form.Select>
            </Form.Group>

            <Row>
              <Col md={6}>
                <Form.Group className="mb-2">
                  <Form.Label>Phone</Form.Label>
                  <Form.Control value={form.phone} onChange={(e) => setForm({ ...form, phone: e.target.value })} />
                </Form.Group>
              </Col>
              <Col md={6}>
                <Form.Group className="mb-2">
                  <Form.Label>Email</Form.Label>
                  <Form.Control type="email" value={form.email} onChange={(e) => setForm({ ...form, email: e.target.value })} />
                </Form.Group>
              </Col>
            </Row>

            <Form.Group className="mb-2">
              <Form.Label>Date of birth</Form.Label>
              <Form.Control type="date" value={form.dob} onChange={(e) => setForm({ ...form, dob: e.target.value })} />
            </Form.Group>

            <Form.Group className="mb-2">
              <Form.Label>Notes</Form.Label>
              <Form.Control as="textarea" rows={3} value={form.notes} onChange={(e) => setForm({ ...form, notes: e.target.value })} />
            </Form.Group>
          </Form>
        </Modal.Body>
        <Modal.Footer>
          <Button variant="secondary" onClick={() => setShowModal(false)}>Cancel</Button>
          <Button variant="primary" onClick={saveMember}>Save</Button>
        </Modal.Footer>
      </Modal>

      {/* CSV Modal */}
      <Modal show={showCsvModal} onHide={() => setShowCsvModal(false)} centered size="lg">
        <Modal.Header closeButton>
          <Modal.Title>Import Members (CSV)</Modal.Title>
        </Modal.Header>
        <Modal.Body>
          <p className="text-muted">CSV columns expected: <code>first_name,last_name,dob,phone,email,church_id,notes</code></p>
          <Form.Group className="mb-3">
            <Form.Label>Select CSV file</Form.Label>
            <Form.Control
              type="file"
              accept=".csv"
              onChange={(e: React.ChangeEvent<HTMLInputElement>) => handleCsvFile(e.target.files ? e.target.files[0] : null)}
            />
          </Form.Group>

          <div className="mb-3">
            <h6>Preview ({csvPreview.length} rows)</h6>
            <div style={{ maxHeight: 280, overflow: "auto" }}>
              <Table size="sm" bordered>
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>DOB</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Branch</th>
                  </tr>
                </thead>
                <tbody>
                  {csvPreview.map((r, i) => (
                    <tr key={i}>
                      <td>{i + 1}</td>
                      <td>{r.first_name} {r.last_name}</td>
                      <td>{r.dob}</td>
                      <td>{r.phone}</td>
                      <td>{r.email}</td>
                      <td>{r.church?.name ?? r.church_id}</td>
                    </tr>
                  ))}
                  {csvPreview.length === 0 && (
                    <tr><td colSpan={6} className="text-center text-muted">No preview</td></tr>
                  )}
                </tbody>
              </Table>
            </div>
          </div>
        </Modal.Body>

        <Modal.Footer>
          <Button variant="secondary" onClick={() => { setCsvFile(null); setCsvPreview([]); setShowCsvModal(false); }}>Cancel</Button>
          <Button variant="primary" onClick={uploadCsv} disabled={csvUploading || csvPreview.length === 0}>
            {csvUploading ? "Uploading..." : "Import CSV"}
          </Button>
        </Modal.Footer>
      </Modal>

    </div>
  );
}
