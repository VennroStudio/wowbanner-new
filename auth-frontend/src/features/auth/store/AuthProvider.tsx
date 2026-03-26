import React from 'react';
import { AuthContext } from './authContext';
import { useAuthProvider } from './useAuthProvider';

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const auth = useAuthProvider();

  return <AuthContext.Provider value={auth}>{children}</AuthContext.Provider>;
};
