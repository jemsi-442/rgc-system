import React, { useEffect, useState } from "react";
import { PastorsAPI } from "../services/apiResources";
import "../styles/pastors.css";

interface Pastor {
  id: number;
  name: string;
  email?: string;
  phone?: string;
  church_id?: number;
}

const emptyPastor: Partial<Pastor> = {
  name: "",
  email: "",
  phone: "",
};

const Pastors: React.FC = () => {
  const [pastors, setPastors] = useState<Pastor[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [showModal, setShowModal] = useState(false);
  const [editingPastor, setEditingPastor] = useState<Pastor | null>(null);
  const [form, setForm] = useState<Partial<Pastor>>(emptyPastor);

  const fetchPastors = async () => {
    try {
      setLoading(true);
      const res = await PastorsAPI.list();
      setPastors(res.data);
    } catch (err) {
      setError("Failed to load pastors");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchPastors();
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
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      setLoading(true);
      if (editingPastor) {
        await PastorsAPI.update(editingPastor.id, form);
      } else {
        await PastorsAPI.create(form);
      }
      closeModal();
      fetchPastors();
    } catch (err) {
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
    } catch (err) {
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
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {pastors.map((p, i) => (
            <tr key={p.id}>
              <td>{i + 1}</td>
              <td>{p.name}</td>
              <td>{p.email || "-"}</td>
              <td>{p.phone || "-"}</td>
              <td>
                <button className="btn-edit" onClick={() => openEdit(p)}>
                  Edit
                </button>
                <button
                  className="btn-danger"
                  onClick={() => handleDelete(p.id)}
                >
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
                name="name"
                placeholder="Full Name"
                value={form.name || ""}
                onChange={handleChange}
                required
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

              <div className="modal-actions">
                <button type="submit" className="btn-primary">
                  {editingPastor ? "Update" : "Create"}
                </button>
                <button
                  type="button"
                  className="btn-secondary"
                  onClick={closeModal}
                >
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
