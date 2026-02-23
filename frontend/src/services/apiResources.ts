import api from "./api";

export type ApiListResponse<T> = Promise<{ data: T[] }>;
export type ApiItemResponse<T> = Promise<{ data: T }>;

const createCRUD = <T>(endpoint: string) => ({
  list: (params?: Record<string, unknown>): ApiListResponse<T> =>
    api.get(`/${endpoint}`, { params }),
  get: (id: number | string): ApiItemResponse<T> => api.get(`/${endpoint}/${id}`),
  create: (data: Record<string, unknown>): ApiItemResponse<T> => api.post(`/${endpoint}`, data),
  update: (id: number | string, data: Record<string, unknown>): ApiItemResponse<T> =>
    api.put(`/${endpoint}/${id}`, data),
  delete: (id: number | string): Promise<{ data: { message?: string } }> =>
    api.delete(`/${endpoint}/${id}`),
});

export const RegionsAPI = createCRUD<any>("regions");
export const DistrictsAPI = createCRUD<any>("districts");
export const ChurchesAPI = createCRUD<any>("churches");
export const MembersAPI = createCRUD<any>("members");
export const UsersAPI = createCRUD<any>("users");
export const PastorsAPI = createCRUD<any>("pastors");
export const OfferingsAPI = createCRUD<any>("offerings");
export const ExpensesAPI = createCRUD<any>("expenses");

export const AttendanceAPI = {
  ...createCRUD<any>("attendance"),
  bulkCreate: (records: Array<Record<string, unknown>>) =>
    api.post("/attendance/bulk", { records }),
};

export const MembersByChurch = (churchId: number | string) =>
  api.get(`/members`, { params: { church_id: churchId } });

export const AttendanceByDate = (date: string) =>
  api.get(`/attendance`, { params: { date } });

export const OfferingsSummary = (from: string, to: string) =>
  api.get(`/reports/offerings/summary`, { params: { from, to } });

export const ExpensesSummary = (from: string, to: string) =>
  api.get(`/reports/expenses/summary`, { params: { from, to } });

export const RegionsHierarchyAPI = {
  list: (refresh = false) =>
    api.get(`/regions/hierarchy`, { params: { refresh: refresh ? 1 : 0 } }),
};

export const PublicChurchesAPI = {
  list: (districtId?: number | string) =>
    api.get("/churches/public", {
      params: districtId ? { district_id: districtId } : {},
    }),
};

export const SlidesAPI = {
  listPublic: () => api.get("/slides/public"),
  list: () => api.get("/slides"),
  create: (data: FormData) =>
    api.post("/slides", data, {
      headers: { "Content-Type": "multipart/form-data" },
    }),
  update: (id: number | string, data: FormData) =>
    api.post(`/slides/${id}?_method=PUT`, data, {
      headers: { "Content-Type": "multipart/form-data" },
    }),
  delete: (id: number | string) => api.delete(`/slides/${id}`),
};

export const BranchChatAPI = {
  list: (churchId?: number | string) =>
    api.get("/branch-chat", {
      params: churchId ? { church_id: churchId } : {},
    }),
  create: (payload: { message: string; church_id?: number }) => api.post("/branch-chat", payload),
  delete: (id: number | string) => api.delete(`/branch-chat/${id}`),
};
