import React, { useContext, useEffect, useMemo, useState } from "react";
import { useLocation, useNavigate } from "react-router-dom";
import { AuthContext } from "../context/AuthContext";
import { loginUser, registerUser } from "../services/apiAuth";
import { PublicChurchesAPI, RegionsHierarchyAPI } from "../services/apiResources";

type District = {
  id: number;
  name: string;
  region_id: number;
};

type Region = {
  id: number;
  name: string;
  districts: District[];
};

type Church = {
  id: number;
  name: string;
  district_id: number;
};

export default function LoginRegister() {
  const navigate = useNavigate();
  const location = useLocation();
  const authContext = useContext(AuthContext);
  const [mode, setMode] = useState<"login" | "register">("login");
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);

  const [regions, setRegions] = useState<Region[]>([]);
  const [churches, setChurches] = useState<Church[]>([]);
  const [regionSearch, setRegionSearch] = useState("");
  const [districtSearch, setDistrictSearch] = useState("");
  const [churchSearch, setChurchSearch] = useState("");

  const [loginData, setLoginData] = useState({
    email: "",
    password: "",
  });

  const [registerData, setRegisterData] = useState({
    name: "",
    email: "",
    password: "",
    confirmPassword: "",
    regionId: "",
    districtId: "",
    churchId: "",
  });

  useEffect(() => {
    setMode(location.pathname === "/register" ? "register" : "login");
    setError("");
  }, [location.pathname]);

  useEffect(() => {
    if (mode !== "register") return;

    const loadHierarchy = async () => {
      try {
        const res = await RegionsHierarchyAPI.list();
        setRegions(Array.isArray(res.data) ? res.data : []);
      } catch {
        setRegions([]);
      }
    };

    loadHierarchy();
  }, [mode]);

  useEffect(() => {
    if (!registerData.districtId) {
      setChurches([]);
      setRegisterData((prev) => ({ ...prev, churchId: "" }));
      return;
    }

    const loadChurches = async () => {
      try {
        const res = await PublicChurchesAPI.list(Number(registerData.districtId));
        setChurches(Array.isArray(res.data) ? res.data : []);
      } catch {
        setChurches([]);
      }
    };

    loadChurches();
  }, [registerData.districtId]);

  const resolveRedirect = (user: any) => {
    const roleNames: string[] = [
      ...(user?.role ? [user.role] : []),
      ...((user?.roles || []).map((r: any) => r.name)),
    ];

    if (roleNames.includes("super_admin")) return "/branches";
    if (roleNames.includes("regional_admin")) return "/branches";
    if (roleNames.includes("district_admin")) return "/branches";
    if (roleNames.includes("branch_admin")) return "/branches";
    if (roleNames.includes("admin")) return "/dashboard";
    return "/profile";
  };

  const handleLogin = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    setError("");
    setLoading(true);

    try {
      const res = await loginUser(loginData);
      localStorage.setItem("token", res.data.token);
      localStorage.setItem("user", JSON.stringify(res.data.user));
      authContext?.setUser(res.data.user);
      navigate(resolveRedirect(res.data.user), { replace: true });
    } catch (err: any) {
      setError(err?.response?.data?.message || "Login failed");
    } finally {
      setLoading(false);
    }
  };

  const handleRegister = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    setError("");

    if (registerData.password !== registerData.confirmPassword) {
      setError("Passwords do not match");
      return;
    }

    if (!registerData.regionId || !registerData.districtId || !registerData.churchId) {
      setError("Region, district, and branch are required");
      return;
    }

    setLoading(true);

    try {
      const res = await registerUser({
        name: registerData.name.trim(),
        email: registerData.email.trim(),
        password: registerData.password,
        password_confirmation: registerData.confirmPassword,
        region_id: Number(registerData.regionId),
        district_id: Number(registerData.districtId),
        church_id: Number(registerData.churchId),
      });
      localStorage.setItem("token", res.data.token);
      localStorage.setItem("user", JSON.stringify(res.data.user));
      authContext?.setUser(res.data.user);
      navigate(resolveRedirect(res.data.user), { replace: true });
    } catch (err: any) {
      setError(err?.response?.data?.message || "Registration failed");
    } finally {
      setLoading(false);
    }
  };

  const selectedRegion = useMemo(
    () => regions.find((region) => region.id === Number(registerData.regionId)),
    [regions, registerData.regionId]
  );

  const filteredRegions = useMemo(
    () => regions.filter((region) => region.name.toLowerCase().includes(regionSearch.toLowerCase())),
    [regions, regionSearch]
  );

  const filteredDistricts = useMemo(() => {
    const districts = selectedRegion?.districts ?? [];
    return districts.filter((district) => district.name.toLowerCase().includes(districtSearch.toLowerCase()));
  }, [selectedRegion, districtSearch]);

  const filteredChurches = useMemo(
    () => churches.filter((church) => church.name.toLowerCase().includes(churchSearch.toLowerCase())),
    [churches, churchSearch]
  );

  return (
    <div className="container mt-5" style={{ maxWidth: 520 }}>
      <h3 className="mb-3 text-center">RGC System</h3>

      <div className="d-flex gap-2 mb-3">
        <button
          type="button"
          className={`btn ${mode === "login" ? "btn-primary" : "btn-outline-primary"} w-100`}
          onClick={() => {
            setMode("login");
            setError("");
            navigate("/login", { replace: true });
          }}
        >
          Login
        </button>
        <button
          type="button"
          className={`btn ${mode === "register" ? "btn-primary" : "btn-outline-primary"} w-100`}
          onClick={() => {
            setMode("register");
            setError("");
            navigate("/register", { replace: true });
          }}
        >
          Register
        </button>
      </div>

      {error && <div className="alert alert-danger">{error}</div>}

      {mode === "login" ? (
        <form onSubmit={handleLogin} className="card card-body shadow-sm">
          <div className="mb-3">
            <label className="form-label">Email</label>
            <input
              className="form-control"
              type="email"
              value={loginData.email}
              onChange={(e) => setLoginData((p) => ({ ...p, email: e.target.value }))}
              required
            />
          </div>

          <div className="mb-3">
            <label className="form-label">Password</label>
            <input
              className="form-control"
              type="password"
              value={loginData.password}
              onChange={(e) => setLoginData((p) => ({ ...p, password: e.target.value }))}
              required
            />
          </div>

          <button className="btn btn-primary" type="submit" disabled={loading}>
            {loading ? "Signing in..." : "Sign In"}
          </button>
        </form>
      ) : (
        <form onSubmit={handleRegister} className="card card-body shadow-sm">
          <div className="mb-3">
            <label className="form-label">Name</label>
            <input
              className="form-control"
              type="text"
              value={registerData.name}
              onChange={(e) =>
                setRegisterData((p) => ({ ...p, name: e.target.value.replace(/[<>]/g, "") }))
              }
              required
            />
          </div>

          <div className="mb-3">
            <label className="form-label">Email</label>
            <input
              className="form-control"
              type="email"
              value={registerData.email}
              onChange={(e) => setRegisterData((p) => ({ ...p, email: e.target.value }))}
              required
            />
          </div>

          <div className="mb-3">
            <label className="form-label">Region</label>
            <input
              className="form-control mb-2"
              placeholder="Search region..."
              value={regionSearch}
              onChange={(e) => setRegionSearch(e.target.value)}
            />
            <select
              className="form-select"
              value={registerData.regionId}
              onChange={(e) =>
                setRegisterData((p) => ({ ...p, regionId: e.target.value, districtId: "", churchId: "" }))
              }
              required
            >
              <option value="">Select region</option>
              {filteredRegions.map((region) => (
                <option key={region.id} value={region.id}>
                  {region.name}
                </option>
              ))}
            </select>
          </div>

          <div className="mb-3">
            <label className="form-label">District</label>
            <input
              className="form-control mb-2"
              placeholder="Search district..."
              value={districtSearch}
              onChange={(e) => setDistrictSearch(e.target.value)}
              disabled={!registerData.regionId}
            />
            <select
              className="form-select"
              value={registerData.districtId}
              onChange={(e) => setRegisterData((p) => ({ ...p, districtId: e.target.value, churchId: "" }))}
              disabled={!registerData.regionId}
              required
            >
              <option value="">Select district</option>
              {filteredDistricts.map((district) => (
                <option key={district.id} value={district.id}>
                  {district.name}
                </option>
              ))}
            </select>
          </div>

          <div className="mb-3">
            <label className="form-label">Branch</label>
            <input
              className="form-control mb-2"
              placeholder="Search branch..."
              value={churchSearch}
              onChange={(e) => setChurchSearch(e.target.value)}
              disabled={!registerData.districtId}
            />
            <select
              className="form-select"
              value={registerData.churchId}
              onChange={(e) => setRegisterData((p) => ({ ...p, churchId: e.target.value }))}
              disabled={!registerData.districtId}
              required
            >
              <option value="">Select branch</option>
              {filteredChurches.map((church) => (
                <option key={church.id} value={church.id}>
                  {church.name}
                </option>
              ))}
            </select>
          </div>

          <div className="mb-3">
            <label className="form-label">Password</label>
            <input
              className="form-control"
              type="password"
              value={registerData.password}
              onChange={(e) => setRegisterData((p) => ({ ...p, password: e.target.value }))}
              required
            />
          </div>

          <div className="mb-3">
            <label className="form-label">Confirm Password</label>
            <input
              className="form-control"
              type="password"
              value={registerData.confirmPassword}
              onChange={(e) => setRegisterData((p) => ({ ...p, confirmPassword: e.target.value }))}
              required
            />
          </div>

          <button className="btn btn-primary" type="submit" disabled={loading}>
            {loading ? "Creating account..." : "Create Account"}
          </button>
        </form>
      )}
    </div>
  );
}
