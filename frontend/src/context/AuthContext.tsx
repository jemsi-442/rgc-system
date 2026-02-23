import React, { createContext, useEffect, useState } from "react";

type RoleLike = { name: string };

export interface AuthUser {
  id: number;
  name: string;
  email: string;
  role?: string;
  roles?: RoleLike[];
}

interface AuthContextProps {
  user: AuthUser | null;
  setUser: React.Dispatch<React.SetStateAction<AuthUser | null>>;
  hasRole: (...roles: string[]) => boolean;
}

export const AuthContext = createContext<AuthContextProps | null>(null);

const AuthProvider = ({ children }: { children: React.ReactNode }) => {
  const [user, setUser] = useState<AuthUser | null>(null);

  useEffect(() => {
    const token = localStorage.getItem("token");
    const savedUser = localStorage.getItem("user");

    if (!token || !savedUser) {
      setUser(null);
      return;
    }

    try {
      setUser(JSON.parse(savedUser));
    } catch {
      setUser(null);
      localStorage.removeItem("user");
    }
  }, []);

  const hasRole = (...roles: string[]) => {
    if (!user) return false;

    const roleSet = new Set<string>([
      ...(user.role ? [user.role] : []),
      ...(user.roles?.map((role) => role.name) ?? []),
    ]);

    return roles.some((role) => roleSet.has(role));
  };

  return <AuthContext.Provider value={{ user, setUser, hasRole }}>{children}</AuthContext.Provider>;
};

export default AuthProvider;
