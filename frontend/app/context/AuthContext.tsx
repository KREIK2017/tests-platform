"use client";

import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useState,
  type ReactNode,
} from "react";
import api from "@/lib/api";
import { getToken, removeToken, setToken } from "@/lib/auth";
import type { Role, SingleResponse, User } from "@/lib/types";

interface AuthContextValue {
  user: User | null;
  loading: boolean;
  login: (email: string, password: string) => Promise<User>;
  register: (
    name: string,
    email: string,
    password: string,
    passwordConfirmation: string,
    role: Role,
  ) => Promise<User>;
  logout: () => Promise<void>;
  refresh: () => Promise<void>;
}

const AuthContext = createContext<AuthContextValue | null>(null);

interface AuthResponse {
  user: User;
  token: string;
}

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState<boolean>(true);

  const fetchMe = useCallback(async () => {
    try {
      const { data } = await api.get<SingleResponse<User>>("/me");
      setUser(data.data);
    } catch {
      removeToken();
      setUser(null);
    }
  }, []);

  useEffect(() => {
    let active = true;
    const token = getToken();
    if (!token) {
      if (active) {
        setUser(null);
        setLoading(false);
      }
      return () => {
        active = false;
      };
    }

    (async () => {
      await fetchMe();
      if (active) setLoading(false);
    })();

    return () => {
      active = false;
    };
  }, [fetchMe]);

  const login = useCallback<AuthContextValue["login"]>(
    async (email, password) => {
      const { data } = await api.post<AuthResponse>("/login", { email, password });
      setToken(data.token);
      setUser(data.user);
      return data.user;
    },
    [],
  );

  const register = useCallback<AuthContextValue["register"]>(
    async (name, email, password, passwordConfirmation, role) => {
      const { data } = await api.post<AuthResponse>("/register", {
        name,
        email,
        password,
        password_confirmation: passwordConfirmation,
        role,
      });
      setToken(data.token);
      setUser(data.user);
      return data.user;
    },
    [],
  );

  const logout = useCallback(async () => {
    try {
      await api.post("/logout");
    } catch {
      // Token may already be invalid — ignore.
    }
    removeToken();
    setUser(null);
  }, []);

  return (
    <AuthContext.Provider
      value={{ user, loading, login, register, logout, refresh: fetchMe }}
    >
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth(): AuthContextValue {
  const ctx = useContext(AuthContext);
  if (!ctx) {
    throw new Error("useAuth must be used inside <AuthProvider>");
  }
  return ctx;
}
