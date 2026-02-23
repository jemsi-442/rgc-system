import axios from "axios";

const rawBaseUrl = process.env.REACT_APP_API_URL || "http://127.0.0.1:8000";
const normalizedBaseUrl = rawBaseUrl.replace(/\/+$/, "");
const baseURL = normalizedBaseUrl.endsWith("/api/v1")
  ? normalizedBaseUrl
  : normalizedBaseUrl.endsWith("/api")
  ? `${normalizedBaseUrl}/v1`
  : `${normalizedBaseUrl}/api/v1`;

const api = axios.create({
  baseURL,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

api.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers = config.headers ?? {};
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

api.interceptors.response.use(
  (response) => {
    const payload = response.data?.data ?? response.data;
    return { ...response, data: payload };
  },
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem("token");
      localStorage.removeItem("user");
      window.location.href = "/login";
    }
    return Promise.reject(error);
  }
);

export default api;
