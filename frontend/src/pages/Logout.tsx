import React, { useContext, useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import api from "../services/apiAuth";
import { AuthContext } from "../context/AuthContext";

export default function Logout() {
  const navigate = useNavigate();
  const authContext = useContext(AuthContext);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string>("");

  useEffect(() => {
    const logoutUser = async () => {
      setLoading(true);
      setError("");
      try {
        await api.post("/logout"); // call backend logout
      } catch (err: any) {
        console.error(err);
        setError("Error logging out. Please try again.");
      } finally {
        // Clear localStorage and context
        localStorage.removeItem("token");
        localStorage.removeItem("user");
        authContext?.setUser(null);
        setLoading(false);
        navigate("/login"); // redirect to login
      }
    };

    logoutUser();
  }, [authContext, navigate]);

  return (
    <div className="container mt-5" style={{ maxWidth: "400px" }}>
      <h3 className="text-center mb-4">Logging Out...</h3>
      {loading && <p className="text-center">Please wait...</p>}
      {error && <div className="alert alert-danger">{error}</div>}
    </div>
  );
}
