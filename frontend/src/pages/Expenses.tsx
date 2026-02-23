import React, { useEffect, useMemo, useState } from "react";
import { ExpensesAPI, ChurchesAPI } from "../services/apiResources";
import "../styles/expenses.css";

interface Church {
  id: number;
  name: string;
}

interface Expense {
  id: number;
  church_id: number;
  amount: number;
  description: string;
  date: string;
  church?: Church;
}

const emptyExpense: Partial<Expense> = {
  church_id: undefined,
  amount: 0,
  description: "",
  date: "",
};

const Expenses: React.FC = () => {
  const [expenses, setExpenses] = useState<Expense[]>([]);
  const [churches, setChurches] = useState<Church[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [showModal, setShowModal] = useState(false);
  const [editingExpense, setEditingExpense] = useState<Expense | null>(null);
  const [form, setForm] = useState<Partial<Expense>>(emptyExpense);

  const [fromDate, setFromDate] = useState("");
  const [toDate, setToDate] = useState("");

  const fetchExpenses = async () => {
    try {
      setLoading(true);
      const res = await ExpensesAPI.list();
      setExpenses(Array.isArray(res.data) ? res.data : []);
    } catch {
      setError("Failed to load expenses");
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
    fetchExpenses();
    fetchChurches();
  }, []);

  const openCreate = () => {
    setEditingExpense(null);
    setForm(emptyExpense);
    setShowModal(true);
  };

  const openEdit = (expense: Expense) => {
    setEditingExpense(expense);
    setForm({
      church_id: expense.church_id,
      amount: expense.amount,
      description: expense.description,
      date: expense.date,
    });
    setShowModal(true);
  };

  const closeModal = () => {
    setShowModal(false);
    setEditingExpense(null);
    setForm(emptyExpense);
  };

  const handleChange = (
    e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>
  ) => {
    const { name, value } = e.target;

    setForm((prev) => ({
      ...prev,
      [name]: name === "amount" || name === "church_id" ? Number(value) : value,
    }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    try {
      setLoading(true);
      if (editingExpense) {
        await ExpensesAPI.update(editingExpense.id, form as Record<string, unknown>);
      } else {
        await ExpensesAPI.create(form as Record<string, unknown>);
      }
      closeModal();
      fetchExpenses();
    } catch {
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
    } catch {
      alert("Failed to delete expense");
    }
  };

  const filteredExpenses = useMemo(() => {
    return expenses.filter((expense) => {
      const matchesFrom = fromDate ? expense.date >= fromDate : true;
      const matchesTo = toDate ? expense.date <= toDate : true;
      return matchesFrom && matchesTo;
    });
  }, [expenses, fromDate, toDate]);

  const totalAmount = useMemo(
    () => filteredExpenses.reduce((sum, expense) => sum + Number(expense.amount), 0),
    [filteredExpenses]
  );

  return (
    <div className="expenses-page">
      <div className="expenses-header">
        <h2>Expenses</h2>
        <button className="btn-primary" onClick={openCreate}>
          + Add Expense
        </button>
      </div>

      <div className="filters">
        <input type="date" value={fromDate} onChange={(e) => setFromDate(e.target.value)} />
        <input type="date" value={toDate} onChange={(e) => setToDate(e.target.value)} />
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
            <th>Description</th>
            <th>Church</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {filteredExpenses.map((expense, index) => (
            <tr key={expense.id}>
              <td>{index + 1}</td>
              <td>{expense.description}</td>
              <td>{expense.church?.name || "-"}</td>
              <td>{expense.amount.toLocaleString()}</td>
              <td>{expense.date}</td>
              <td>
                <button className="btn-edit" onClick={() => openEdit(expense)}>
                  Edit
                </button>
                <button className="btn-danger" onClick={() => handleDelete(expense.id)}>
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
            <h3>{editingExpense ? "Edit Expense" : "Create Expense"}</h3>

            <form onSubmit={handleSubmit}>
              <select
                name="church_id"
                value={form.church_id || ""}
                onChange={handleChange}
                required
              >
                <option value="">Select Church</option>
                {churches.map((church) => (
                  <option key={church.id} value={church.id}>
                    {church.name}
                  </option>
                ))}
              </select>

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
                placeholder="Description"
                value={form.description || ""}
                onChange={handleChange}
                required
              />

              <div className="modal-actions">
                <button type="submit" className="btn-primary">
                  {editingExpense ? "Update" : "Create"}
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

export default Expenses;
