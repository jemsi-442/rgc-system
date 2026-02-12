import api from "./api";

/**
 * Generic CRUD factory (optional utility)
 */
const createCRUD = (endpoint: string) => ({
  list: (params?: any) => api.get(`/${endpoint}`, { params }),
  get: (id: number | string) => api.get(`/${endpoint}/${id}`),
  create: (data: any) => api.post(`/${endpoint}`, data),
  update: (id: number | string, data: any) => api.put(`/${endpoint}/${id}`, data),
  delete: (id: number | string) => api.delete(`/${endpoint}/${id}`),
});

/**
 * ============ CORE RESOURCES ============
 */

export const RegionsAPI = createCRUD("regions");

export const DistrictsAPI = createCRUD("districts");

export const ChurchesAPI = createCRUD("churches");

export const MembersAPI = createCRUD("members");

export const UsersAPI = createCRUD("users");

/**
 * ============ CHURCH OPERATIONS ============
 */

export const PastorsAPI = createCRUD("pastors");

export const OfferingsAPI = createCRUD("offerings");

export const ExpensesAPI = createCRUD("expenses");

export const AttendanceAPI = createCRUD("attendance");

/**
 * ============ OPTIONAL: SPECIALIZED METHODS ============
 * (Unaweza kutumia hizi kama unataka filters, reports, etc.)
 */

// Example: Members by church
export const MembersByChurch = (churchId: number | string) =>
  api.get(`/members`, { params: { church_id: churchId } });

// Example: Attendance by date
export const AttendanceByDate = (date: string) =>
  api.get(`/attendance`, { params: { date } });

// Example: Offerings summary
export const OfferingsSummary = (from: string, to: string) =>
  api.get(`/offerings/summary`, { params: { from, to } });

// Example: Expenses summary
export const ExpensesSummary = (from: string, to: string) =>
  api.get(`/expenses/summary`, { params: { from, to } });


