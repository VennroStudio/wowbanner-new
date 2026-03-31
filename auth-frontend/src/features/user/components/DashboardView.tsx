import React, { useState } from 'react';
import { LogOut, Camera } from 'lucide-react';
import { useAuth } from '@/features/auth';
import { useRouter } from '@/shared/hooks';
import { UserAvatar } from './UserAvatar';
import { AdminPanel } from './AdminPanel/AdminPanel';
import { NavigationPanel } from './NavigationPanel/NavigationPanel';
import { AvatarManager } from './AvatarManager';

import { PageCard } from '@/shared/components';
import { ROUTES } from '@/shared/constants';

export const DashboardView: React.FC = () => {
  const { navigate } = useRouter();
  const { user, isAdmin, logout } = useAuth();
  const [isAvatarManagerOpen, setIsAvatarManagerOpen] = useState(false);

  const handleLogout = async () => {
    try {
      await logout.mutateAsync();
      navigate(ROUTES.HOME);
    } catch (err) {
      console.error('Logout failed', err);
    }
  };

  return (
    <PageCard align="center">
      {/* Avatar Section */}
      <div className="flex flex-col items-center mb-6 mt-4">
        <button
          onClick={() => setIsAvatarManagerOpen(true)}
          className="relative w-24 h-24 ring-4 ring-blue-50/50 rounded-full hover:ring-blue-100/80 transition-all group overflow-hidden cursor-pointer"
        >
          <div className="w-full h-full bg-slate-100 flex items-center justify-center text-slate-400 group-hover:scale-110 transition-transform duration-500">
            <UserAvatar avatarUrl={user?.avatar} />
          </div>
          <div className="absolute inset-0 bg-slate-900/0 group-hover:bg-slate-900/20 transition-colors flex items-center justify-center">
            <Camera className="text-white opacity-0 group-hover:opacity-100 transition-opacity" size={24} />
          </div>
        </button>
      </div>
      <h2 className="text-2xl font-bold text-slate-800 mb-1">
        Здравствуйте, {user?.first_name }!
      </h2>
      <p className="text-slate-500 mb-6">{user?.email}</p>

      <NavigationPanel />
      {isAdmin && <AdminPanel />}

      <button
        onClick={handleLogout}
        disabled={logout.isPending}
        className="flex items-center gap-2 text-slate-400 hover:text-red-500 disabled:opacity-50 transition-colors text-sm font-medium mx-auto px-4 py-2"
      >
        <LogOut size={16} /> {logout.isPending ? 'Выход...' : 'Выйти из аккаунта'}
      </button>

      <AvatarManager
        isOpen={isAvatarManagerOpen}
        onClose={() => setIsAvatarManagerOpen(false)}
      />
    </PageCard>
  );
};
