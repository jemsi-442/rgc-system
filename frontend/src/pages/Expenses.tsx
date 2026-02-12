import React, { useEffect, useMemo, useState } from "react";
import { ExpensesAPI } from "../services/apiResources";
import "../styles/expenses.css";

interface Expense {
  id: number;
  title: string;
  amount: number;
  category: string;
  date: string;
  description?: string;
}

const emptyExpense: Partial<Expense> = {
  title: "",
  amount: 0,
  category: "",
  date: "",
  description: "",
};

const Expenses: React.FC = () => {
  const [expenses, setExpenses] = useState<Expense[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [showModal, setShowModal] = useState(false);
  const [editingExpense, setEditingExpense] = useState<Expense | null>(null);
  const [form, setForm] = useState<Partial<Expense>>(emptyExpense);

  // Filters
  const [categoryFilter, setCategoryFilter] = useState("");
  const [fromDate, setFromDate] = useState("");
  const [toDate, setToDate] = useState("");

  const fetchExpenses = async () => {
    try {
      setLoading(true);
      const res = await ExpensesAPI.list();
      setExpenses(res.data);
    } catch (err) {
      setError("Failed to load expenses");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchExpenses();
  }, []);

  const openCreate = () => {
    setEditingExpense(null);
    setForm(emptyExpense);
    setShowModal(true);
  };

  const openEdit = (expense: Expense) => {
    setEditingExpense(expense);
    setForm(expense);
    setShowModal(true);
  };

  const closeModal = () => {
    setShowModal(false);
    setEditingExpense(null);
    setForm(emptyExpense);
  };

  const handleChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>
  ) => {
    const { name, value } = e.target;
    setForm({
      ...form,
      [name]: name === "amount" ? Number(value) : value,
    });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      setLoading(true);
      if (editingExpense) {
        await ExpensesAPI.update(editingExpense.id, form);
      } else {
        await ExpensesAPI.create(form);
      }
      closeModal();
      fetchExpenses();
    } catch (err) {
      alert("Failed to save expense");
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id: number) => {
    if (!window.confirm("Delete this expense?")) return;
    try {
      await ExpensesAPI.delete(id);
      fetchExpenses();
    } catch (err) {
      alert("Failed to delete expense");
    }
  };

  const filteredExpenses = useMemo(() => {
    return expenses.filter((e) => {
      const matchesCategory = categoryFilter
        ? e.category === categoryFilter
        : true;
      const matchesFrom = fromDate ? e.date >= fromDate : true;
      const matchesTo = toDate ? e.date <= toDate : true;
      return matchesCategory && matchesFrom && matchesTo;
    });
  }, [expenses, categoryFilter, fromDate, toDate]);

  const totalAmount = useMemo(() => {
    return filteredExpenses.reduce((sum, e) => sum + Number(e.amount), 0);
  }, [filteredExpenses]);

  const categories = useMemo(() => {
    return Array.from(new Set(expenses.map((e) => e.category)));
  }, [expenses]);

  return (
    <div className="expenses-page">
      <div className="expenses-header">
        <h2>Expenses</h2>
        <button className="btn-primary" onClick={openCreate}>
          + Add Expense
        </button>
      </div>

      {/* Filters */}
      <div className="filters">
        <select
          value={categoryFilter}
          onChange={(e) => setCategoryFilter(e.target.value)}
        >
          <option value="">All Categories</option>
          {categories.map((c) => (
            <option key={c} value={c}>
              {c}
            </option>
          ))}
        </select>

        <input
          type="date"
          value={fromDate}
          onChange={(e) => setFromDate(e.target.value)}
        />

        <input
          type="date"
          value={toDate}
          onChange={(e) => setToDate(e.target.value)}
        />
      </div>

      <div className="total-box">
        <strong>Total:</strong> {totalAmount.toLocaleString()}
      </div>

      {loading && <p>Loading...</p>}
      {error && <p className="error">{error}</p>}

      <table className="expenses-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Title</th>
            <th>Category</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {filteredExpenses.map((e, i) => (
            <tr key={e.id}>
              <td>{i + 1}</td>
              <td>{e.title}</td>
              <td>{e.category}</td>
              <td>{e.amount.toLocaleString()}</td>
              <td>{e.date}</td>
              <td>
                <button className="btn-edit" onClick={() => openEdit(e)}>
                  Edit
                </button>
                <button
                  className="btn-danger"
                  onClick={() => handleDelete(e.id)}
                >
                  Delete
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>

      {/* Modal */}
      {showModal && (
        <div className="modal-overlay">
          <div className="modal">
            <h3>{editingExpense ? "Edit Expense" : "Create Expense"}</h3>

            <form onSubmit={handleSubmit}>
              <input
                type="text"
                name="title"
                placeholder="Title"
                value={form.title || ""}
                onChange={handleChange}
                required
              />

              <input
                type="text"
                name="category"
                placeholder="Category"
                value={form.category || ""}
                onChange={handleChange}
                required
              />

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

              <textarea
                name="description"
                placeholder="Description (optional)"
                value={form.description || ""}
                onChange={handleChange}
              />

              <div className="modal-actions">
                <button type="submit" className="btn-primary">
                  {editingExpense ? "Update" : "Create"}
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

export default Expenses;
