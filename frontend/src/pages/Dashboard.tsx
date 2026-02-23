import React, { useContext } from "react";
import { AuthContext } from "../context/AuthContext";

export default function Dashboard() {
  const authContext = useContext(AuthContext);

  return (
    <div className="container mt-4">
      <h1>Dashboard</h1>
      <p>Welcome, {authContext?.user?.name}</p>

      {authContext?.hasRole("super_admin") && (
        <div className="alert alert-dark">
          <h5>Super Admin Dashboard</h5>
          <p>Create branches, assign admins, and govern national hierarchy.</p>
        </div>
      )}

      {authContext?.hasRole("regional_admin") && (
        <div className="alert alert-warning">
          <h5>Regional Admin Dashboard</h5>
          <p>Manage branch operations within your assigned region.</p>
        </div>
      )}

      {authContext?.hasRole("district_admin") && (
        <div className="alert alert-warning">
          <h5>District Admin Dashboard</h5>
          <p>Manage branch operations within your assigned district.</p>
        </div>
      )}

      {authContext?.hasRole("branch_admin") && (
        <div className="alert alert-warning">
          <h5>Branch Admin Dashboard</h5>
          <p>Manage members, offerings, expenses, attendance, and announcements for your branch.</p>
        </div>
      )}

      {authContext?.hasRole("member", "user") && (
        <div className="alert alert-info">
          <h5>Member Dashboard</h5>
          <p>View announcements, branch chat, and your profile.</p>
        </div>
      )}
    </div>
  );
}
