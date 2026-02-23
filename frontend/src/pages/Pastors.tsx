import React, { useEffect, useState } from "react";
import { ChurchesAPI, PastorsAPI } from "../services/apiResources";
import "../styles/pastors.css";

interface Church {
  id: number;
  name: string;
}

interface Pastor {
  id: number;
  full_name: string;
  title?: string;
  email?: string;
  phone?: string;
  church_id?: number | null;
}

const emptyPastor: Partial<Pastor> = {
  full_name: "",
  title: "",
  email: "",
  phone: "",
  church_id: null,
};

const Pastors: React.FC = () => {
  const [pastors, setPastors] = useState<Pastor[]>([]);
  const [churches, setChurches] = useState<Church[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [showModal, setShowModal] = useState(false);
  const [editingPastor, setEditingPastor] = useState<Pastor | null>(null);
  const [form, setForm] = useState<Partial<Pastor>>(emptyPastor);

  const fetchPastors = async () => {
    try {
      setLoading(true);
      const res = await PastorsAPI.list();
      setPastors(Array.isArray(res.data) ? res.data : []);
    } catch {
      setError("Failed to load pastors");
    } finally {
      setLoading(false);
    }
  };

  const fetchChurches = async () => {
    try {
      const res = await ChurchesAPI.list();
      setChurches(Array.isArray(res.data) ? res.data : []);
    } catch {
      setChurches([]);
    }
  };

  useEffect(() => {
    fetchPastors();
    fetchChurches();
  }, []);

  const openCreate = () => {
    setEditingPastor(null);
    setForm(emptyPastor);
    setShowModal(true);
  };

  const openEdit = (pastor: Pastor) => {
    setEditingPastor(pastor);
    setForm(pastor);
    setShowModal(true);
  };

  const closeModal = () => {
    setShowModal(false);
    setEditingPastor(null);
    setForm(emptyPastor);
  };

  const handleChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>
  ) => {
    const { name, value } = e.target;

    setForm((prev) => ({
      ...prev,
      [name]: name === "church_id" ? (value === "" ? null : Number(value)) : value,
    }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    try {
      setLoading(true);
      if (editingPastor) {
        await PastorsAPI.update(editingPastor.id, form as Record<string, unknown>);
      } else {
        await PastorsAPI.create(form as Record<string, unknown>);
      }
      closeModal();
      fetchPastors();
    } catch {
      alert("Failed to save pastor");
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id: number) => {
    if (!window.confirm("Are you sure you want to delete this pastor?")) return;
    try {
      await PastorsAPI.delete(id);
      fetchPastors();
    } catch {
      alert("Failed to delete pastor");
    }
  };

  return (
    <div className="pastors-page">
      <div className="pastors-header">
        <h2>Pastors</h2>
        <button className="btn-primary" onClick={openCreate}>
          + Add Pastor
        </button>
      </div>

      {loading && <p>Loading...</p>}
      {error && <p className="error">{error}</p>}

      <table className="pastors-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Full Name</th>
            <th>Title</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {pastors.map((pastor, index) => (
            <tr key={pastor.id}>
              <td>{index + 1}</td>
              <td>{pastor.full_name}</td>
              <td>{pastor.title || "-"}</td>
              <td>{pastor.email || "-"}</td>
              <td>{pastor.phone || "-"}</td>
              <td>
                <button className="btn-edit" onClick={() => openEdit(pastor)}>
                  Edit
                </button>
                <button className="btn-danger" onClick={() => handleDelete(pastor.id)}>
                  Delete
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>

      {showModal && (
        <div className="modal-overlay">
          <div className="modal">
            <h3>{editingPastor ? "Edit Pastor" : "Create Pastor"}</h3>

            <form onSubmit={handleSubmit}>
              <input
                type="text"
                name="full_name"
                placeholder="Full Name"
                value={form.full_name || ""}
                onChange={handleChange}
                required
              />

              <input
                type="text"
                name="title"
                placeholder="Title"
                value={form.title || ""}
                onChange={handleChange}
              />

              <input
                type="email"
                name="email"
                placeholder="Email"
                value={form.email || ""}
                onChange={handleChange}
              />

              <input
                type="text"
                name="phone"
                placeholder="Phone"
                value={form.phone || ""}
                onChange={handleChange}
              />

              <select
                name="church_id"
                value={form.church_id ?? ""}
                onChange={handleChange}
              >
                <option value="">Assign Church (optional)</option>
                {churches.map((church) => (
                  <option key={church.id} value={church.id}>
                    {church.name}
                  </option>
                ))}
              </select>

              <div className="modal-actions">
                <button type="submit" className="btn-primary">
                  {editingPastor ? "Update" : "Create"}
                </button>
                <button type="button" className="btn-secondary" onClick={closeModal}>
                  Cancel
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};

export default Pastors;
