import React from 'react';
import { LogOut, Settings, User as UserIcon } from 'lucide-react';
import { UserDropdownMenuItem } from './UserDropdownMenuItem';

interface Props {
  displayName: string;
  email?: string;
  onLogout: () => void;
}

export const UserDropdownMenu: React.FC<Props> = ({ displayName, email, onLogout }) => (
  <div className="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 border border-slate-200 text-slate-700 z-50">
    <div className="px-4 py-3 border-b border-slate-100">
      <p className="text-sm font-semibold text-slate-900 truncate">{displayName}</p>
      <p className="text-xs text-slate-500 truncate mt-0.5">{email}</p>
    </div>

    <div className="py-1">
      <UserDropdownMenuItem icon={UserIcon} label="Мой профиль" />
      <UserDropdownMenuItem icon={Settings} label="Настройки" />
    </div>

    <div className="border-t border-slate-100 py-1">
      <UserDropdownMenuItem icon={LogOut} label="Выйти из аккаунта" onClick={onLogout} variant="danger" />
    </div>
  </div>
);
