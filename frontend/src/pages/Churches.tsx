import React, { useEffect, useState } from "react";
import apiResources from "../services/apiResources";
import { Button, Card, Modal, Form, Table, Row, Col } from "react-bootstrap";
import { toast } from "react-toastify";
import "../styles/churches.css";

interface District { id:number; name:string; }
interface Pastor { id:number; full_name:string; }
interface Church {
  id:number;
  name:string;
  address?:string;
  phone?:string;
  email?:string;
  district_id?:number | null;
  pastor_id?:number | null;
  pastors?: Pastor[];
}

export default function Churches() {
  const [churches, setChurches] = useState<Church[]>([]);
  const [districts, setDistricts] = useState<District[]>([]);
  const [pastors, setPastors] = useState<Pastor[]>([]);
  const [showModal, setShowModal] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);

  const [name, setName] = useState("");
  const [address, setAddress] = useState("");
  const [phone, setPhone] = useState("");
  const [email, setEmail] = useState("");
  const [districtId, setDistrictId] = useState<number | "">("");
  const [pastorId, setPastorId] = useState<number | "">("");

  const load = async () => {
    try {
      const [cRes, dRes, pRes] = await Promise.all([
        api.get("/churches"),
        api.get("/districts"),
        api.get("/pastors"),
      ]);
      setChurches(cRes.data || []);
      setDistricts(dRes.data || []);
      setPastors(pRes.data || []);
    } catch (err) {
      toast.error("Failed to load data");
    }
  };

  useEffect(() => { load(); }, []);

  const openAdd = () => {
    setEditingId(null);
    setName(""); setAddress(""); setPhone(""); setEmail(""); setDistrictId(""); setPastorId("");
    setShowModal(true);
  };

  const openEdit = (c: Church) => {
    setEditingId(c.id);
    setName(c.name);
    setAddress(c.address || "");
    setPhone(c.phone || "");
    setEmail(c.email || "");
    setDistrictId(c.district_id ?? "");
    setPastorId(c.pastor_id ?? "");
    setShowModal(true);
  };

  const handleSave = async () => {
    if (!name.trim()) return toast.error("Name required");
    const payload = {
      name, address, phone, email,
      district_id: districtId === "" ? null : districtId,
      pastor_id: pastorId === "" ? null : pastorId,
    };
    try {
      if (editingId) {
        await api.put(`/churches/${editingId}`, payload);
        toast.success("Church updated");
      } else {
        await api.post("/churches", payload);
        toast.success("Church created");
      }
      setShowModal(false);
      load();
    } catch (err) {
      toast.error("Error saving church");
    }
  };

  const handleDelete = async (id:number) => {
    if (!window.confirm("Delete this church?")) return;
    try {
      await api.delete(`/churches/${id}`);
      toast.success("Deleted");
      load();
    } catch {
      toast.error("Error deleting");
    }
  };

  return (
    <div className="container mt-4 churches-page">
      <Row className="align-items-center mb-3">
        <Col><h3 className="fw-bold">Branches / Churches</h3><div className="text-muted">Manage church branches</div></Col>
        <Col className="text-end"><Button variant="primary" onClick={openAdd}>+ Add Branch</Button></Col>
      </Row>

      <Card className="shadow-sm animated-card">
        <Card.Body>
          <Table striped hover responsive>
            <thead>
              <tr><th>#</th><th>Name</th><th>District</th><th>Pastor</th><th>Phone</th><th style={{width:150}}>Actions</th></tr>
            </thead>
            <tbody>
              {churches.length === 0 && (<tr><td colSpan={6} className="text-center text-muted py-4">No branches found.</td></tr>)}
              {churches.map((c, idx) => (
                <tr key={c.id}>
                  <td>{idx+1}</td>
                  <td>{c.name}</td>
                  <td>{(c as any).district?.name ?? "—"}</td>
                  <td>{(c as any).pastor?.full_name ?? "—"}</td>
                  <td>{c.phone ?? "—"}</td>
                  <td>
                    <Button size="sm" variant="warning" className="me-2" onClick={() => openEdit(c)}>Edit</Button>
                    <Button size="sm" variant="danger" onClick={() => handleDelete(c.id)}>Delete</Button>
                  </td>
                </tr>
              ))}
            </tbody>
          </Table>
        </Card.Body>
      </Card>

      {/* Modal */}
      <Modal show={showModal} onHide={() => setShowModal(false)} centered>
        <Modal.Header closeButton><Modal.Title>{editingId ? "Edit Branch" : "Add Branch"}</Modal.Title></Modal.Header>
        <Modal.Body>
          <Form>
            <Form.Group className="mb-3"><Form.Label>Name</Form.Label>
              <Form.Control value={name} onChange={(e)=>setName(e.target.value)} placeholder="Branch name" />
            </Form.Group>
            <Form.Group className="mb-3"><Form.Label>Address</Form.Label>
              <Form.Control value={address} onChange={(e)=>setAddress(e.target.value)} placeholder="Address" />
            </Form.Group>
            <Form.Group className="mb-3"><Form.Label>Phone</Form.Label>
              <Form.Control value={phone} onChange={(e)=>setPhone(e.target.value)} placeholder="Phone" />
            </Form.Group>
            <Form.Group className="mb-3"><Form.Label>Email</Form.Label>
              <Form.Control value={email} onChange={(e)=>setEmail(e.target.value)} placeholder="Email" />
            </Form.Group>

            <Row>
              <Col>
                <Form.Group className="mb-3"><Form.Label>District</Form.Label>
                  <Form.Select value={districtId} onChange={(e)=>setDistrictId(e.target.value===""?"":Number(e.target.value))}>
                    <option value="">Select district</option>
                    {districts.map(d => <option key={d.id} value={d.id}>{d.name}</option>)}
                  </Form.Select>
                </Form.Group>
              </Col>
              <Col>
                <Form.Group className="mb-3"><Form.Label>Assign Pastor</Form.Label>
                  <Form.Select value={pastorId} onChange={(e)=>setPastorId(e.target.value===""?"":Number(e.target.value))}>
                    <option value="">— None —</option>
                    {pastors.map(p => <option key={p.id} value={p.id}>{p.full_name}</option>)}
                  </Form.Select>
                </Form.Group>
              </Col>
            </Row>

          </Form>
        </Modal.Body>
        <Modal.Footer>
          <Button variant="secondary" onClick={()=>setShowModal(false)}>Cancel</Button>
          <Button variant="primary" onClick={handleSave}>Save</Button>
        </Modal.Footer>
      </Modal>
    </div>
  );
}
