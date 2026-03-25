import React from 'react';
import { User as UserIcon, LogOut } from 'lucide-react';
import { useAuth } from '../context/AuthContext';

interface AuthenticatedPageProps {
  navigate: (path: string) => void;
}

export const AuthenticatedPage: React.FC<AuthenticatedPageProps> = ({ navigate }) => {
  const { user, logout } = useAuth();

  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  return (
    <div className="w-full max-w-md bg-white p-8 rounded-3xl shadow-xl shadow-slate-200/50 text-center">
      <div className="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-blue-600">
        <UserIcon size={32} />
      </div>
      <h2 className="text-2xl font-bold text-slate-800 mb-1">Вы вошли</h2>
      <p className="text-slate-500 mb-6">{user?.email}</p>
      <button
        onClick={handleLogout}
        className="flex items-center gap-2 text-slate-500 hover:text-red-500 transition-colors bg-slate-50 px-4 py-2 rounded-xl text-sm font-medium mx-auto"
      >
        <LogOut size={16} /> Выйти
      </button>
    </div>
  );
};
