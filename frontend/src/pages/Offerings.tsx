import React, { useEffect, useState } from "react";
import { OfferingsAPI, ChurchesAPI } from "../services/apiResources";
import "../styles/offerings.css";

interface Offering {
  id: number;
  amount: number;
  date: string;
  church_id?: number;
  church?: {
    id: number;
    name: string;
  };
}

interface Church {
  id: number;
  name: string;
}

const emptyOffering: Partial<Offering> = {
  amount: 0,
  date: "",
  church_id: undefined,
};

const Offerings: React.FC = () => {
  const [offerings, setOfferings] = useState<Offering[]>([]);
  const [churches, setChurches] = useState<Church[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [showModal, setShowModal] = useState(false);
  const [editingOffering, setEditingOffering] = useState<Offering | null>(null);
  const [form, setForm] = useState<Partial<Offering>>(emptyOffering);

  const fetchOfferings = async () => {
    try {
      setLoading(true);
      const res = await OfferingsAPI.list();
      setOfferings(res.data);
    } catch (err) {
      setError("Failed to load offerings");
    } finally {
      setLoading(false);
    }
  };

  const fetchChurches = async () => {
    try {
      const res = await ChurchesAPI.list();
      setChurches(res.data);
    } catch (err) {
      console.error("Failed to load churches");
    }
  };

  useEffect(() => {
    fetchOfferings();
    fetchChurches();
  }, []);

  const openCreate = () => {
    setEditingOffering(null);
    setForm(emptyOffering);
    setShowModal(true);
  };

  const openEdit = (offering: Offering) => {
    setEditingOffering(offering);
    setForm({
      amount: offering.amount,
      date: offering.date,
      church_id: offering.church_id,
    });
    setShowModal(true);
  };

  const closeModal = () => {
    setShowModal(false);
    setEditingOffering(null);
    setForm(emptyOffering);
  };

  const handleChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>
  ) => {
    const { name, value } = e.target;
    setForm({ ...form, [name]: name === "amount" ? Number(value) : value });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      setLoading(true);
      if (editingOffering) {
        await OfferingsAPI.update(editingOffering.id, form);
      } else {
        await OfferingsAPI.create(form);
      }
      closeModal();
      fetchOfferings();
    } catch (err) {
      alert("Failed to save offering");
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id: number) => {
    if (!window.confirm("Are you sure you want to delete this offering?")) return;
    try {
      await OfferingsAPI.delete(id);
      fetchOfferings();
    } catch (err) {
      alert("Failed to delete offering");
    }
  };

  return (
    <div className="offerings-page">
      <div className="offerings-header">
        <h2>Offerings</h2>
        <button className="btn-primary" onClick={openCreate}>
          + Add Offering
        </button>
      </div>

      {loading && <p>Loading...</p>}
      {error && <p className="error">{error}</p>}

      <table className="offerings-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Church</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {offerings.map((o, i) => (
            <tr key={o.id}>
              <td>{i + 1}</td>
              <td>{o.amount.toLocaleString()}</td>
              <td>{o.date}</td>
              <td>{o.church?.name || "-"}</td>
              <td>
                <button className="btn-edit" onClick={() => openEdit(o)}>
                  Edit
                </button>
                <button
                  className="btn-danger"
                  onClick={() => handleDelete(o.id)}
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
            <h3>{editingOffering ? "Edit Offering" : "Create Offering"}</h3>

            <form onSubmit={handleSubmit}>
              <input
                type="number"
                name="amount"
                placeholder="Amount"
                value={form.amount || 0}
                onChange={handleChange}
                required
              />

              <input
                type="date"
                name="date"
                value={form.date || ""}
                onChange={handleChange}
                required
              />

              <select
                name="church_id"
                value={form.church_id || ""}
                onChange={handleChange}
              >
                <option value="">Select Church</option>
                {churches.map((c) => (
                  <option key={c.id} value={c.id}>
                    {c.name}
                  </option>
                ))}
              </select>

              <div className="modal-actions">
                <button type="submit" className="btn-primary">
                  {editingOffering ? "Update" : "Create"}
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

export default Offerings;
