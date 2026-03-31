import { create } from 'zustand';
import type { User } from '@/entities/user';
import { getCookie } from '@/shared/api/cookie';

interface AuthState {
  user: User | null;
  accessToken: string | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  
  setUser: (user: User | null) => void;
  updateUser: (data: Partial<User>) => void;
  setAccessToken: (token: string | null) => void;
  setIsLoading: (isLoading: boolean) => void;
  logout: () => void;
}

export const useAuthStore = create<AuthState>((set) => ({
  user: null,
  accessToken: null,
  isAuthenticated: false,
  isLoading: !!getCookie('logged_in'),

  setUser: (user) => set({ user, isAuthenticated: !!user }),
  updateUser: (data) =>
    set((state) => ({
      user: state.user ? { ...state.user, ...data } : null,
    })),
  setAccessToken: (accessToken) => set({ accessToken }),
  setIsLoading: (isLoading) => set({ isLoading }),
  logout: () => set({ user: null, accessToken: null, isAuthenticated: false }),
}));
