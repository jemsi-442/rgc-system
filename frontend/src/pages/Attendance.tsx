import React, { useEffect, useMemo, useState } from "react";
import { AttendanceAPI, MembersAPI } from "../services/apiResources";
import "../styles/attendance.css";

interface Member {
  id: number;
  name: string;
}

interface Attendance {
  id: number;
  member_id: number;
  status: "present" | "absent";
  date: string;
  member?: Member;
}

const AttendancePage: React.FC = () => {
  const [attendance, setAttendance] = useState<Attendance[]>([]);
  const [members, setMembers] = useState<Member[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");

  const [dateFilter, setDateFilter] = useState("");
  const [bulkDate, setBulkDate] = useState("");
  const [bulkData, setBulkData] = useState<{ [key: number]: "present" | "absent" }>({});

  const fetchAttendance = async () => {
    try {
      setLoading(true);
      const res = await AttendanceAPI.list();
      setAttendance(res.data);
    } catch (err) {
      setError("Failed to load attendance");
    } finally {
      setLoading(false);
    }
  };

  const fetchMembers = async () => {
    try {
      const res = await MembersAPI.list();
      setMembers(res.data);
    } catch (err) {
      console.error("Failed to load members");
    }
  };

  useEffect(() => {
    fetchAttendance();
    fetchMembers();
  }, []);

  const filteredAttendance = useMemo(() => {
    if (!dateFilter) return attendance;
    return attendance.filter((a) => a.date === dateFilter);
  }, [attendance, dateFilter]);

  const stats = useMemo(() => {
    const present = filteredAttendance.filter((a) => a.status === "present").length;
    const absent = filteredAttendance.filter((a) => a.status === "absent").length;
    return { present, absent };
  }, [filteredAttendance]);

  const toggleStatus = async (record: Attendance) => {
    try {
      await AttendanceAPI.update(record.id, {
        status: record.status === "present" ? "absent" : "present",
      });
      fetchAttendance();
    } catch (err) {
      alert("Failed to update status");
    }
  };

  const handleDelete = async (id: number) => {
    if (!window.confirm("Delete this record?")) return;
    try {
      await AttendanceAPI.delete(id);
      fetchAttendance();
    } catch (err) {
      alert("Failed to delete record");
    }
  };

  const handleBulkSubmit = async () => {
    if (!bulkDate) return alert("Select a date");

    try {
      setLoading(true);
      const payload = Object.entries(bulkData).map(([member_id, status]) => ({
        member_id: Number(member_id),
        status,
        date: bulkDate,
      }));

      await AttendanceAPI.bulkCreate(payload);
      setBulkData({});
      fetchAttendance();
      alert("Attendance saved");
    } catch (err) {
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

      {/* Filters */}
      <div className="filters">
        <input
          type="date"
          value={dateFilter}
          onChange={(e) => setDateFilter(e.target.value)}
        />
        <button onClick={() => setDateFilter("")}>Clear</button>
      </div>

      {/* Stats */}
      <div className="stats-box">
        <div className="stat present">Present: {stats.present}</div>
        <div className="stat absent">Absent: {stats.absent}</div>
      </div>

      {loading && <p>Loading...</p>}
      {error && <p className="error">{error}</p>}

      {/* Attendance Table */}
      <table className="attendance-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Member</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          {filteredAttendance.map((a, i) => (
            <tr key={a.id}>
              <td>{i + 1}</td>
              <td>{a.member?.name || a.member_id}</td>
              <td>
                <span className={`badge ${a.status}`}>
                  {a.status.toUpperCase()}
                </span>
              </td>
              <td>{a.date}</td>
              <td>
                <button className="btn-edit" onClick={() => toggleStatus(a)}>
                  Toggle
                </button>
                <button
                  className="btn-danger"
                  onClick={() => handleDelete(a.id)}
                >
                  Delete
                </button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>

      {/* Bulk Entry */}
      <div className="bulk-section">
        <h3>Bulk Attendance</h3>

        <input
          type="date"
          value={bulkDate}
          onChange={(e) => setBulkDate(e.target.value)}
        />

        <div className="bulk-list">
          {members.map((m) => (
            <div key={m.id} className="bulk-row">
              <span>{m.name}</span>
              <select
                value={bulkData[m.id] || ""}
                onChange={(e) =>
                  setBulkData({
                    ...bulkData,
                    [m.id]: e.target.value as "present" | "absent",
                  })
                }
              >
                <option value="">--</option>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
              </select>
            </div>
          ))}
        </div>

        <button className="btn-primary" onClick={handleBulkSubmit}>
          Save Bulk Attendance
        </button>
      </div>
    </div>
  );
};

export default AttendancePage;
