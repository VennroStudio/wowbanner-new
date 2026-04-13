import React, { useState, useRef, useEffect } from 'react';
import { LogOut, Settings, User as UserIcon } from 'lucide-react';
import { useAuthStore } from '@/features/auth';
import { UserAvatar } from '@/shared/ui/UserAvatar';
import { authApi } from '@/entities/user';
import { AUTH_URL } from '@/shared/constants';

export const UserDropdown = () => {
  const { user, logout } = useAuthStore();
  const [isOpen, setIsOpen] = useState(false);
  const dropdownRef = useRef<HTMLDivElement>(null);

  const displayName = user?.first_name 
    ? `${user.first_name} ${user.last_name || ''}`.trim() 
    : (user?.email || 'Пользователь');

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
        setIsOpen(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const handleLogout = async () => {
    try {
      await authApi.logout();
    } catch {
      // Игнорируем ошибку при логауте
    } finally {
      logout();
      window.location.href = AUTH_URL;
    }
  };

  return (
    <div className="relative" ref={dropdownRef}>
      <div 
        onClick={() => setIsOpen(!isOpen)}
        className="flex items-center gap-2 cursor-pointer hover:bg-slate-700 px-2 py-1 rounded-md transition-colors border border-transparent hover:border-slate-600"
      >
        <UserAvatar avatarUrl={user?.avatar} />
        <div className="text-sm font-medium hidden sm:block">{displayName}</div>
      </div>
      
      {isOpen && (
        <div className="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 border border-slate-200 text-slate-700 z-50">
          <div className="px-4 py-3 border-b border-slate-100">
            <p className="text-sm font-semibold text-slate-900 truncate">{displayName}</p>
            <p className="text-xs text-slate-500 truncate mt-0.5">{user?.email}</p>
          </div>
          
          <div className="py-1">
            <button className="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 flex items-center gap-3 cursor-pointer transition-colors text-slate-700">
              <UserIcon size={16} className="text-slate-400" />
              Мой профиль
            </button>
            <button className="w-full text-left px-4 py-2 text-sm hover:bg-slate-50 flex items-center gap-3 cursor-pointer transition-colors text-slate-700">
              <Settings size={16} className="text-slate-400" />
              Настройки
            </button>
          </div>
          
          <div className="border-t border-slate-100 py-1">
            <button 
              onClick={handleLogout}
              className="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center gap-3 cursor-pointer transition-colors font-medium"
            >
              <LogOut size={16} className="text-red-500" />
              Выйти из аккаунта
            </button>
          </div>
        </div>
      )}
    </div>
  );
};
