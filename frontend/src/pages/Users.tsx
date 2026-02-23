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

type UserForm = {
  name: string;
  email: string;
  phone?: string;
  role: string;
  password?: string;
};

const emptyUser: UserForm = {
  name: "",
  email: "",
  phone: "",
  role: "member",
  password: "",
};

const Users: React.FC = () => {
  const [users, setUsers] = useState<User[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [showModal, setShowModal] = useState(false);
  const [editingUser, setEditingUser] = useState<User | null>(null);
  const [form, setForm] = useState<UserForm>(emptyUser);

  const fetchUsers = async () => {
    try {
      setLoading(true);
      const res = await UsersAPI.list();
      setUsers(Array.isArray(res.data) ? res.data : []);
    } catch {
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
    setForm({
      name: user.name,
      email: user.email,
      phone: user.phone || "",
      role: user.role,
      password: "",
    });
    setShowModal(true);
  };

  const closeModal = () => {
    setShowModal(false);
    setForm(emptyUser);
    setEditingUser(null);
  };

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
    setForm((prev) => ({ ...prev, [e.target.name]: e.target.value }));
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    try {
      setLoading(true);
      if (editingUser) {
        const payload: Record<string, unknown> = {
          name: form.name,
          email: form.email,
          phone: form.phone || null,
          role: form.role,
        };

        if (form.password) {
          payload.password = form.password;
        }

        await UsersAPI.update(editingUser.id, payload);
      } else {
        await UsersAPI.create({
          name: form.name,
          email: form.email,
          phone: form.phone || null,
          role: form.role,
          password: form.password,
        });
      }

      closeModal();
      fetchUsers();
    } catch {
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
    } catch {
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
          {users.map((user, index) => (
            <tr key={user.id}>
              <td>{index + 1}</td>
              <td>{user.name}</td>
              <td>{user.email}</td>
              <td>{user.phone || "-"}</td>
              <td>{user.role}</td>
              <td>
                <button className="btn-edit" onClick={() => openEdit(user)}>
                  Edit
                </button>
                <button className="btn-danger" onClick={() => handleDelete(user.id)}>
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
                value={form.name}
                onChange={handleChange}
                required
              />

              <input
                type="email"
                name="email"
                placeholder="Email"
                value={form.email}
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

              <select name="role" value={form.role} onChange={handleChange}>
                <option value="super_admin">Super Admin</option>
                <option value="regional_admin">Regional Admin</option>
                <option value="district_admin">District Admin</option>
                <option value="branch_admin">Branch Admin</option>
                <option value="bishop">Bishop</option>
                <option value="pastor">Pastor</option>
                <option value="assistant_pastor">Assistant Pastor</option>
                <option value="accountant">Accountant</option>
                <option value="evangelist">Evangelist</option>
                <option value="choir_leader">Choir Leader</option>
                <option value="youth_leader">Youth Leader</option>
                <option value="member">Member</option>
              </select>

              <input
                type="password"
                name="password"
                placeholder={editingUser ? "New Password (optional)" : "Password"}
                value={form.password || ""}
                onChange={handleChange}
                required={!editingUser}
              />

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
