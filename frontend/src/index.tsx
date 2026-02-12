import React, { Suspense, lazy } from "react";
import ReactDOM from "react-dom/client";
import { BrowserRouter, Routes, Route, Navigate, Outlet } from "react-router-dom";

import "bootstrap/dist/css/bootstrap.min.css";
import "react-toastify/dist/ReactToastify.css";
import "./index.css";

// Context (default export)
import AuthProvider from "./context/AuthContext";

import { ToastContainer } from "react-toastify";
import Navbar from "./components/Navbar";
import Sidebar from "./components/Sidebar";
import Loader from "./components/Loader";

// Lazy Loaded Pages
const Dashboard = lazy(() => import("./pages/Dashboard"));
const Offerings = lazy(() => import("./pages/Offerings"));
const Members = lazy(() => import("./pages/Members"));
const Branches = lazy(() => import("./pages/Branches"));
const Churches = lazy(() => import("./pages/Churches"));
const Districts = lazy(() => import("./pages/Districts"));
const Expenses = lazy(() => import("./pages/Expenses"));
const Pastors = lazy(() => import("./pages/Pastors"));
const Regions = lazy(() => import("./pages/Regions"));
const Users = lazy(() => import("./pages/Users"));
const Login = lazy(() => import("./pages/LoginRegister"));

/* ----------------------- Protected Route ----------------------- */
const ProtectedRoute = () => {
  const token = localStorage.getItem("token");
  return token ? <Outlet /> : <Navigate to="/login" replace />;
};

/* ----------------------- App Layout ----------------------- */
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

/* ----------------------- Error Boundary ----------------------- */
class ErrorBoundary extends React.Component<any, { hasError: boolean }> {
  constructor(props: any) {
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

/* ----------------------- Render ----------------------- */
root.render(
  <React.StrictMode>
    <AuthProvider>
      <BrowserRouter>
        <ErrorBoundary>
          <Suspense fallback={<Loader />}>
            <Routes>
              {/* Public */}
              <Route path="/login" element={<Login />} />

              {/* Protected */}
              <Route element={<ProtectedRoute />}>
                <Route element={<AppLayout />}>
                  <Route index element={<Dashboard />} />
                  <Route path="/dashboard" element={<Dashboard />} />
                  <Route path="/offerings" element={<Offerings />} />
                  <Route path="/members" element={<Members />} />
                  <Route path="/branches" element={<Branches />} />
                  <Route path="/churches" element={<Churches />} />
                  <Route path="/districts" element={<Districts />} />
                  <Route path="/regions" element={<Regions />} />
                  <Route path="/pastors" element={<Pastors />} />
                  <Route path="/expenses" element={<Expenses />} />
                  <Route path="/users" element={<Users />} />
                </Route>
              </Route>

        context      {/* Catch All */}
              <Route path="*" element={<Navigate to="/" replace />} />
            </Routes>

            <ToastContainer position="top-right" theme="colored" />
          </Suspense>
        </ErrorBoundary>
      </BrowserRouter>
    </AuthProvider>
  </React.StrictMode>
);
