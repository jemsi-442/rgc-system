import React, { Suspense, lazy, useContext } from "react";
import ReactDOM from "react-dom/client";
import { BrowserRouter, Navigate, Outlet, Route, Routes } from "react-router-dom";

import "bootstrap/dist/css/bootstrap.min.css";
import "react-toastify/dist/ReactToastify.css";
import "./index.css";

import AuthProvider from "./context/AuthContext";
import { AuthContext } from "./context/AuthContext";
import { ToastContainer } from "react-toastify";
import Navbar from "./components/Navbar";
import Sidebar from "./components/Sidebar";
import Loader from "./components/Loader";

const Dashboard = lazy(() => import("./pages/Dashboard"));
const Offerings = lazy(() => import("./pages/Offerings"));
const Members = lazy(() => import("./pages/Members"));
const Branches = lazy(() => import("./pages/Branches"));
const Expenses = lazy(() => import("./pages/Expenses"));
const Pastors = lazy(() => import("./pages/Pastors"));
const Users = lazy(() => import("./pages/Users"));
const Login = lazy(() => import("./pages/LoginRegister"));
const Home = lazy(() => import("./pages/Home"));
const Attendance = lazy(() => import("./pages/Attendance"));
const Logout = lazy(() => import("./pages/Logout"));
const SlideManager = lazy(() => import("./pages/SlideManager"));
const Profile = lazy(() => import("./pages/Profile"));
const BranchChat = lazy(() => import("./pages/BranchChat"));

const ProtectedRoute = () => {
  const token = localStorage.getItem("token");
  return token ? <Outlet /> : <Navigate to="/login" replace />;
};

const RoleRoute = ({ roles }: { roles: string[] }) => {
  const authContext = useContext(AuthContext);
  const allowed = authContext?.hasRole(...roles) ?? false;

  return allowed ? <Outlet /> : <Navigate to="/dashboard" replace />;
};

const AppLayout = () => (
  <div className="d-flex">
    <Sidebar />
    <div className="flex-grow-1">
      <Navbar />
      <div className="p-4">
        <Outlet />
      </div>
    </div>
  </div>
);

class ErrorBoundary extends React.Component<
  { children: React.ReactNode },
  { hasError: boolean }
> {
  constructor(props: { children: React.ReactNode }) {
    super(props);
    this.state = { hasError: false };
  }

  static getDerivedStateFromError() {
    return { hasError: true };
  }

  render() {
    if (this.state.hasError) {
      return (
        <div className="text-center mt-5">
          <h2>Something went wrong!</h2>
          <p>Please reload the page.</p>
        </div>
      );
    }

    return this.props.children;
  }
}

const root = ReactDOM.createRoot(document.getElementById("root") as HTMLElement);

root.render(
  <React.StrictMode>
    <AuthProvider>
      <BrowserRouter>
        <ErrorBoundary>
          <Suspense fallback={<Loader />}>
            <Routes>
              <Route path="/" element={<Home />} />
              <Route path="/login" element={<Login />} />
              <Route path="/register" element={<Login />} />

              <Route element={<ProtectedRoute />}>
                <Route element={<AppLayout />}>
                  <Route index element={<Dashboard />} />
                  <Route path="/dashboard" element={<Dashboard />} />
                  <Route element={<RoleRoute roles={["super_admin", "regional_admin", "district_admin", "branch_admin", "member", "admin", "user"]} />}>
                    <Route path="/offerings" element={<Offerings />} />
                    <Route path="/expenses" element={<Expenses />} />
                    <Route path="/attendance" element={<Attendance />} />
                    <Route path="/chat" element={<BranchChat />} />
                  </Route>

                  <Route element={<RoleRoute roles={["super_admin", "regional_admin", "district_admin", "branch_admin", "admin"]} />}>
                    <Route path="/members" element={<Members />} />
                    <Route path="/branches" element={<Branches />} />
                    <Route path="/pastors" element={<Pastors />} />
                  </Route>

                  <Route element={<RoleRoute roles={["super_admin"]} />}>
                    <Route path="/slides" element={<SlideManager />} />
                    <Route path="/users" element={<Users />} />
                  </Route>

                  <Route path="/profile" element={<Profile />} />
                  <Route path="/logout" element={<Logout />} />
                </Route>
              </Route>

              <Route path="*" element={<Navigate to="/" replace />} />
            </Routes>

            <ToastContainer position="top-right" theme="colored" />
          </Suspense>
        </ErrorBoundary>
      </BrowserRouter>
    </AuthProvider>
  </React.StrictMode>
);
