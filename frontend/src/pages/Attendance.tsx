import React, { useEffect, useMemo, useState } from "react";
import { AttendanceAPI, ChurchesAPI } from "../services/apiResources";
import "../styles/attendance.css";

interface Church {
  id: number;
  name: string;
}

interface AttendanceRecord {
  id: number;
  church_id: number;
  date: string;
  men: number;
  women: number;
  youth: number;
  children: number;
  total: number;
  notes?: string;
  church?: Church;
}

const emptyRecord = {
  church_id: "",
  date: "",
  men: 0,
  women: 0,
  youth: 0,
  children: 0,
  notes: "",
};

const AttendancePage: React.FC = () => {
  const [attendance, setAttendance] = useState<AttendanceRecord[]>([]);
  const [churches, setChurches] = useState<Church[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");

  const [dateFilter, setDateFilter] = useState("");
  const [form, setForm] = useState(emptyRecord);

  const [bulkDate, setBulkDate] = useState("");
  const [bulkRows, setBulkRows] = useState<
    Array<{ church_id: number; men: number; women: number; youth: number; children: number; notes: string }>
  >([]);

  const fetchAttendance = async () => {
    try {
      setLoading(true);
      const res = await AttendanceAPI.list();
      setAttendance(Array.isArray(res.data) ? res.data : []);
    } catch {
      setError("Failed to load attendance");
    } finally {
      setLoading(false);
    }
  };

  const fetchChurches = async () => {
    try {
      const res = await ChurchesAPI.list();
      const list = Array.isArray(res.data) ? res.data : [];
      setChurches(list);
      setBulkRows(
        list.map((church: Church) => ({
          church_id: church.id,
          men: 0,
          women: 0,
          youth: 0,
          children: 0,
          notes: "",
        }))
      );
    } catch {
      setChurches([]);
      setBulkRows([]);
    }
  };

  useEffect(() => {
    fetchAttendance();
    fetchChurches();
  }, []);

  const filteredAttendance = useMemo(() => {
    if (!dateFilter) return attendance;
    return attendance.filter((record) => record.date === dateFilter);
  }, [attendance, dateFilter]);

  const stats = useMemo(() => {
    const total = filteredAttendance.reduce((sum, record) => sum + Number(record.total || 0), 0);
    return { total, count: filteredAttendance.length };
  }, [filteredAttendance]);

  const handleCreate = async (e: React.FormEvent) => {
    e.preventDefault();

    try {
      setLoading(true);
      await AttendanceAPI.create({
        church_id: Number(form.church_id),
        date: form.date,
        men: Number(form.men),
        women: Number(form.women),
        youth: Number(form.youth),
        children: Number(form.children),
        notes: form.notes || null,
      });

      setForm(emptyRecord);
      fetchAttendance();
    } catch {
      alert("Failed to create attendance");
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id: number) => {
    if (!window.confirm("Delete this record?")) return;

    try {
      await AttendanceAPI.delete(id);
      fetchAttendance();
    } catch {
      alert("Failed to delete record");
    }
  };

  const updateBulkRow = (
    churchId: number,
    key: "men" | "women" | "youth" | "children" | "notes",
    value: string
  ) => {
    setBulkRows((prev) =>
      prev.map((row) =>
        row.church_id === churchId
          ? {
              ...row,
              [key]: key === "notes" ? value : Number(value),
            }
          : row
      )
    );
  };

  const handleBulkSubmit = async () => {
    if (!bulkDate) {
      alert("Select a date");
      return;
    }

    try {
      setLoading(true);
      await AttendanceAPI.bulkCreate(
        bulkRows.map((row) => ({
          church_id: row.church_id,
          date: bulkDate,
          men: row.men,
          women: row.women,
          youth: row.youth,
          children: row.children,
          notes: row.notes || null,
        }))
      );

      fetchAttendance();
      alert("Bulk attendance saved");
    } catch {
      alert("Bulk save failed");
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="attendance-page">
      <div className="attendance-header">
        <h2>Attendance</h2>
      </div>

      <div className="filters">
        <input type="date" value={dateFilter} onChange={(e) => setDateFilter(e.target.value)} />
        <button onClick={() => setDateFilter("")}>Clear</button>
      </div>

      <div className="stats-box">
        <div className="stat present">Records: {stats.count}</div>
        <div className="stat absent">Total Attendance: {stats.total}</div>
      </div>

      <form className="bulk-section" onSubmit={handleCreate}>
        <h3>Single Entry</h3>

        <select
          value={form.church_id}
          onChange={(e) => setForm((prev) => ({ ...prev, church_id: e.target.value }))}
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
          type="date"
          value={form.date}
          onChange={(e) => setForm((prev) => ({ ...prev, date: e.target.value }))}
          required
        />

        <div className="bulk-list">
          <input
            type="number"
            min={0}
            placeholder="Men"
            value={form.men}
            onChange={(e) => setForm((prev) => ({ ...prev, men: Number(e.target.value) }))}
          />
          <input
            type="number"
            min={0}
            placeholder="Women"
            value={form.women}
            onChange={(e) => setForm((prev) => ({ ...prev, women: Number(e.target.value) }))}
          />
          <input
            type="number"
            min={0}
            placeholder="Youth"
            value={form.youth}
            onChange={(e) => setForm((prev) => ({ ...prev, youth: Number(e.target.value) }))}
          />
          <input
            type="number"
            min={0}
            placeholder="Children"
            value={form.children}
            onChange={(e) => setForm((prev) => ({ ...prev, children: Number(e.target.value) }))}
          />
        </div>

        <input
          type="text"
          placeholder="Notes"
          value={form.notes}
          onChange={(e) => setForm((prev) => ({ ...prev, notes: e.target.value }))}
        />

        <button className="btn-primary" type="submit" disabled={loading}>
          Save Attendance
        </button>
      </form>

      {loading && <p>Loading...</p>}
      {error && <p className="error">{error}</p>}

      <table className="attendance-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Church</th>
            <th>Date</th>
            <th>Men</th>
            <th>Women</th>
            <th>Youth</th>
            <th>Children</th>
            <th>Total</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {filteredAttendance.map((record, index) => (
            <tr key={record.id}>
              <td>{index + 1}</td>
              <td>{record.church?.name || record.church_id}</td>
              <td>{record.date}</td>
              <td>{record.men}</td>
              <td>{record.women}</td>
              <td>{record.youth}</td>
              <td>{record.children}</td>
              <td>{record.total}</td>
              <td>
                <button className="btn-danger" onClick={() => handleDelete(record.id)}>
                  Delete
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>

      <div className="bulk-section">
        <h3>Bulk Attendance</h3>

        <input type="date" value={bulkDate} onChange={(e) => setBulkDate(e.target.value)} />

        <div className="bulk-list">
          {bulkRows.map((row) => {
            const church = churches.find((item) => item.id === row.church_id);

            return (
              <div key={row.church_id} className="bulk-row">
                <span>{church?.name || row.church_id}</span>
                <input
                  type="number"
                  min={0}
                  value={row.men}
                  onChange={(e) => updateBulkRow(row.church_id, "men", e.target.value)}
                  placeholder="Men"
                />
                <input
                  type="number"
                  min={0}
                  value={row.women}
                  onChange={(e) => updateBulkRow(row.church_id, "women", e.target.value)}
                  placeholder="Women"
                />
                <input
                  type="number"
                  min={0}
                  value={row.youth}
                  onChange={(e) => updateBulkRow(row.church_id, "youth", e.target.value)}
                  placeholder="Youth"
                />
                <input
                  type="number"
                  min={0}
                  value={row.children}
                  onChange={(e) => updateBulkRow(row.church_id, "children", e.target.value)}
                  placeholder="Children"
                />
                <input
                  type="text"
                  value={row.notes}
                  onChange={(e) => updateBulkRow(row.church_id, "notes", e.target.value)}
                  placeholder="Notes"
                />
              </div>
            );
          })}
        </div>

        <button className="btn-primary" onClick={handleBulkSubmit}>
          Save Bulk Attendance
        </button>
      </div>
    </div>
  );
};

export default AttendancePage;
