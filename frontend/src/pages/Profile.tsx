import React, { useContext } from "react";
import { AuthContext } from "../context/AuthContext";

export default function Profile() {
  const authContext = useContext(AuthContext);
  const user = authContext?.user;

  return (
    <div className="container mt-4">
      <h2>Profile</h2>
      <div className="card p-3">
        <p><strong>Name:</strong> {user?.name}</p>
        <p><strong>Email:</strong> {user?.email}</p>
        <p><strong>Legacy Role:</strong> {user?.role || "-"}</p>
        <p><strong>Roles:</strong> {(user?.roles || []).map((role) => role.name).join(", ") || "-"}</p>
      </div>
    </div>
  );
}
