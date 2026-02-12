import React, { useEffect, useState } from "react";
import { UsersAPI } from "../services/apiResources";
import "../styles/users.css";

interface User {
  id: number;
  name: string;
  email: string;
  phone?: string;
  role: string;
  church_id?: number;
}

const emptyUser: Partial<User> = {
  name: "",
  email: "",
  phone: "",
  role: "user",
};

const Users: React.FC = () => {
  const [users, setUsers] = useState<User[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [showModal, setShowModal] = useState(false);
  const [editingUser, setEditingUser] = useState<User | null>(null);
  const [form, setForm] = useState<Partial<User>>(emptyUser);

  const fetchUsers = async () => {
    try {
      setLoading(true);
      const res = await UsersAPI.list();
      setUsers(res.data);
    } catch (err: any) {
      setError("Failed to load users");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchUsers();
  }, []);

  const openCreate = () => {
    setEditingUser(null);
    setForm(emptyUser);
    setShowModal(true);
  };

  const openEdit = (user: User) => {
    setEditingUser(user);
    setForm(user);
    setShowModal(true);
  };

  const closeModal = () => {
    setShowModal(false);
    setForm(emptyUser);
    setEditingUser(null);
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      setLoading(true);
      if (editingUser) {
        await UsersAPI.update(editingUser.id, form);
      } else {
        await UsersAPI.create(form);
      }
      closeModal();
      fetchUsers();
    } catch (err) {
      alert("Failed to save user");
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id: number) => {
    if (!window.confirm("Are you sure you want to delete this user?")) return;
    try {
      await UsersAPI.delete(id);
      fetchUsers();
    } catch (err) {
      alert("Failed to delete user");
    }
  };

  return (
    <div className="users-page">
      <div className="users-header">
        <h2>Users</h2>
        <button className="btn-primary" onClick={openCreate}>
          + Add User
        </button>
      </div>

      {loading && <p>Loading...</p>}
      {error && <p className="error">{error}</p>}

      <table className="users-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Role</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {users.map((u, i) => (
            <tr key={u.id}>
              <td>{i + 1}</td>
              <td>{u.name}</td>
              <td>{u.email}</td>
              <td>{u.phone || "-"}</td>
              <td>{u.role}</td>
              <td>
                <button className="btn-edit" onClick={() => openEdit(u)}>
                  Edit
                </button>
                <button className="btn-danger" onClick={() => handleDelete(u.id)}>
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
            <h3>{editingUser ? "Edit User" : "Create User"}</h3>

            <form onSubmit={handleSubmit}>
              <input
                type="text"
                name="name"
                placeholder="Name"
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
                required
              />

              <input
                type="text"
                name="phone"
                placeholder="Phone"
                value={form.phone || ""}
                onChange={handleChange}
              />

              <select name="role" value={form.role || "user"} onChange={handleChange}>
                <option value="super_admin">Super Admin</option>
                <option value="admin">Admin</option>
                <option value="regional_admin">Regional Admin</option>
                <option value="district_admin">District Admin</option>
                <option value="pastor">Pastor</option>
                <option value="accountant">Accountant</option>
                <option value="user">User</option>
              </select>

              <div className="modal-actions">
                <button type="submit" className="btn-primary">
                  {editingUser ? "Update" : "Create"}
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

export default Users;
