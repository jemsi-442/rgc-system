import api from "./api";

export const registerUser = (data: {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  region_id: number;
  district_id: number;
  church_id: number;
}) => api.post("/auth/register", data);

export const loginUser = (data: { email: string; password: string }) =>
  api.post("/auth/login", data);

export const logoutUser = () => api.post("/auth/logout");

export const getProfile = () => api.get("/auth/me");
