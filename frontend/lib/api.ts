import axios, { AxiosError } from "axios";
import Cookies from "js-cookie";

const api = axios.create({
  baseURL: process.env.NEXT_PUBLIC_API_URL,
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

api.interceptors.request.use((config) => {
  const token = Cookies.get("auth_token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

api.interceptors.response.use(
  (res) => res,
  (err: AxiosError) => {
    if (typeof window !== "undefined" && err.response?.status === 401) {
      Cookies.remove("auth_token");
      const { pathname } = window.location;
      if (pathname !== "/login" && pathname !== "/register" && pathname !== "/") {
        window.location.href = "/login";
      }
    }
    return Promise.reject(err);
  },
);

export default api;

export interface ApiErrorPayload {
  message: string;
  errors?: Record<string, string[]>;
}

export function extractApiError(err: unknown): ApiErrorPayload {
  if (axios.isAxiosError(err)) {
    const data = err.response?.data as ApiErrorPayload | undefined;
    if (data?.message) {
      return data;
    }
    return { message: err.message };
  }
  if (err instanceof Error) {
    return { message: err.message };
  }
  return { message: "Unknown error" };
}
