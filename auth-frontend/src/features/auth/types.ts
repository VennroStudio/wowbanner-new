import type { User } from '@/entities/user';

export interface AuthContextType {
  user: User | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  login: (accessToken: string) => Promise<void>;
  logout: () => Promise<void>;
  apiFetch: (endpoint: string, options?: RequestInit) => Promise<unknown>;
}

export interface LoginDto {
  email: string;
  password: string;
}

export interface RegisterDto {
  firstName: string;
  lastName: string;
  email: string;
  password: string;
}

export interface ResetPasswordDto {
  token: string;
  password: string;
}
