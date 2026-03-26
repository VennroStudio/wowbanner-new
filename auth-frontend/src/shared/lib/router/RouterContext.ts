import { createContext } from 'react';

interface RouterContextType {
  path: string;
  query: URLSearchParams;
  navigate: (path: string) => void;
}

export const RouterContext = createContext<RouterContextType | null>(null);
